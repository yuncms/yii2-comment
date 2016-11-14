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
use yii\helpers\Markdown;
use yii\helpers\HtmlPurifier;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeBehavior;

/**
 * Comment 评论模型
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $source_type
 * @property integer $source_id
 * @property string $content
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Comment extends ActiveRecord
{
    const STATUS_DELETED = 0;

    const STATUS_ACTIVE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%comment}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
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
     * @return CommentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CommentQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'source_id', 'source_type','content'], 'required'],
            [['source_type','content'], 'filter', 'filter' => 'trim'],
            ['content', 'validateContent'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]]
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
            $comment = static::findOne(['user_id' => $this->user_id, 'source_id' => $this->source_id]);
            if ($comment) {
                //一分钟内多次提交
                if ((time() - $comment->created_at) < 65) {
                    $this->addError($attribute, Yii::t('comment', 'One minute only comment once.'));
                }
                //计算相似度
                $similar = similar_text($comment->content, $this->content);
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

        ];
    }

    /**
     * 获取用户
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Yii::$app->user->identityClass, ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            if($this->user_id == null){
                $this->user_id = Yii::$app->user->id;
            }
        }
        parent::beforeSave($insert);
    }
}