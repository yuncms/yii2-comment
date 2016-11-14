<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace yuncms\comment\models;

use Yii;
use yii\base\Model;

/**
 * Class CommentForm
 * @package yuncms\comment\models
 */
class CommentForm extends Comment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'source_id', 'source_type','content'], 'required'],
            [['source_type','content'], 'filter', 'filter' => 'trim'],
            ['content', 'validateContent'],
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
}