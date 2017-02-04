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
use yii\helpers\HtmlPurifier;
use yii\behaviors\AttributeBehavior;

/**
 * Comment 评论模型
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $source_type
 * @property integer $source_id
 * @property string $content
 * @property int|null $to_user_id
 * @property integer $status
 * @property integer $created_at
 */
class Comment extends ActiveRecord
{
    //待定
    const STATUS_PENDING = 0;

    //正常
    const STATUS_ACCEPTED = 1;

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
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes'=>[
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
        ];
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
            ['status', 'default', 'value' => self::STATUS_PENDING],
            ['status', 'in', 'range' => [
                self::STATUS_PENDING,
                self::STATUS_ACCEPTED,
            ]]
        ];
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

    public function isPending()
    {
        return $this->status == static::STATUS_PENDING;
    }

    public function isAccepted()
    {
        return $this->status == static::STATUS_ACCEPTED;
    }

    /**
     * 设置评论为已审核
     */
    public function confirm()
    {
        return (bool)$this->updateAttributes(['status' => static::STATUS_ACCEPTED]);
    }


    /**
     * 获取用户
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Yii::$app->user->identityClass, ['id' => 'user_id']);
    }

    public function getToUser()
    {
        return $this->hasOne(Yii::$app->user->identityClass, ['id' => 'to_user_id']);
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

    public function beforeValidate()
    {
        if ($this->user_id == null) {
            $this->user_id = Yii::$app->user->id;
        }
        return parent::beforeValidate();
    }
}