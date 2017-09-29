<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\comment\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\HtmlPurifier;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\BlameableBehavior;
use yuncms\system\ScanInterface;
use yuncms\system\jobs\ScanTextJob;
use yuncms\user\models\User;

/**
 * Comment 评论模型
 *
 * @property integer $id 评论ID
 * @property integer $user_id 用户ID
 * @property string $source_type
 * @property integer $source_id
 * @property string $content 评论内容
 * @property integer|null $to_user_id @某人
 * @property integer $parent 父评论ID
 * @property integer $status 评论状态
 * @property integer $created_at 发表时间
 *
 * @property-read boolean $isDraft 是否草稿
 * @property-read boolean $isPublished 是否发布
 */
class Comment extends ActiveRecord implements ScanInterface
{
    //场景定义
    const SCENARIO_CREATE = 'create';//创建
    const SCENARIO_UPDATE = 'update';//更新

    //状态定义
    const STATUS_DRAFT = 0;//草稿
    const STATUS_REVIEW = 1;//审核
    const STATUS_REJECTED = 2;//拒绝
    const STATUS_PUBLISHED = 3;//发布

    //事件定义
    const BEFORE_PUBLISHED = 'beforePublished';
    const AFTER_PUBLISHED = 'afterPublished';
    const BEFORE_REJECTED = 'beforeRejected';
    const AFTER_REJECTED = 'afterRejected';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_FIND => 'content'
                ],
                'value' => function ($event) {
                    return HtmlPurifier::process($event->sender->content);
                }
            ],
            [
                'class' => BlameableBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'user_id',
                ],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        return ArrayHelper::merge($scenarios, [
            static::SCENARIO_CREATE => ['source_id', 'source_type', 'content'],
            static::SCENARIO_UPDATE => ['source_id', 'source_type', 'content'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['source_id', 'source_type', 'content'], 'required'],
            [['source_type', 'content'], 'filter', 'filter' => 'trim'],
            ['content', 'validateContent'],
            ['status', 'default', 'value' => self::STATUS_DRAFT],
            ['status', 'in', 'range' => [
                self::STATUS_DRAFT, self::STATUS_REVIEW, self::STATUS_REJECTED, self::STATUS_PUBLISHED,
            ]]
        ];
    }

    /**
     * 验证评论内容
     *
     * @param string $attribute 目前正在验证的属性
     * @param array $params 规则中给出的附加名称值对
     */
    public function validateContent($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $model = static::findOne(['user_id' => $this->user_id]);
            if ($model) {
                //一分钟内多次提交
                if ((time() - $model->created_at) < 60) {
                    $this->addError($attribute, Yii::t('comment', 'One minute only comment once.'));
                }
                //计算相似度
                $similar = similar_text($model->content, $this->content);
                if ($similar > 50) {
                    $this->addError($attribute, Yii::t('comment', 'You can not submit the same comment.'));
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'content' => Yii::t('comment', 'Content'),
            'parent' => Yii::t('comment', 'Parent Content'),
            'source_type' => Yii::t('comment', 'source Type'),
            'source_id' => Yii::t('comment', 'source Id'),
            'status' => Yii::t('comment', 'Status'),
            'created_at' => Yii::t('comment', 'Created At'),
        ];
    }

    /**
     * 关联用户
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * at 某人
     * @return ActiveQuery
     */
    public function getToUser()
    {
        return $this->hasOne(User::className(), ['id' => 'to_user_id']);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%comment}}';
    }

    /**
     * @inheritdoc
     * @return CommentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CommentQuery(get_called_class());
    }

    /**
     * 是否草稿状态
     * @return bool
     */
    public function isDraft()
    {
        return $this->status == static::STATUS_DRAFT;
    }

    /**
     * 是否发布状态
     * @return bool
     */
    public function isPublished()
    {
        return $this->status == static::STATUS_PUBLISHED;
    }

    /**
     * 审核通过
     * @return int
     */
    public function setPublished()
    {
        $this->trigger(self::BEFORE_PUBLISHED);
        $rows = $this->updateAttributes(['status' => static::STATUS_PUBLISHED, 'published_at' => time()]);
        $this->trigger(self::AFTER_PUBLISHED);
        return $rows;
    }

    /**
     * 拒绝通过
     * @param string $failedReason 拒绝原因
     * @return int
     */
    public function setRejected($failedReason)
    {
        $this->trigger(self::BEFORE_REJECTED);
        $rows = $this->updateAttributes(['status' => static::STATUS_REJECTED, 'failed_reason' => $failedReason]);
        $this->trigger(self::AFTER_REJECTED);
        return $rows;
    }

    /**
     * 机器审核
     * @param int $id model id
     * @param string $suggestion the ID to be looked for
     * @return void
     */
    public static function review($id, $suggestion)
    {
        if (($model = static::findOne($id)) != null) {
            if ($suggestion == 'pass') {
                $model->setPublished();
            } elseif ($suggestion == 'block') {
                $model->setRejected('');
            } elseif ($suggestion == 'review') { //人工审核，不做处理

            }
        }
    }

    /**
     * 获取待审
     * @param int $id
     * @return string 待审核的内容字符串
     */
    public static function findReview($id)
    {
        if (($model = static::findOne($id)) != null) {
            return $model->content;
        }
        return null;
    }

    /**
     * 保存后机器审核
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            //开始机器审核
            Yii::$app->queue->push(new ScanTextJob([
                'modelClass' => get_class($this), 'modelId' => $this->id, 'scenario' => 'new', 'category' => 'comment'
            ]));
        }
        return parent::afterSave($insert, $changedAttributes);
    }
}