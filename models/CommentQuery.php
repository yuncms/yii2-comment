<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\comment\models;

use yii\db\ActiveQuery;

/**
 * Class CommentQuery
 * @package yuncms\comment\models
 */
class CommentQuery extends ActiveQuery
{
    /**
     * @var string 模型类型
     */
    public $source_type;

    /**
     * @var string 数据表名称
     */
    public $tableName;

    /**
     * @param \yii\db\QueryBuilder $builder
     * @return $this|\yii\db\Query
     */
    public function prepare($builder)
    {
        $this->andWhere([$this - $this->tableName . 'source_type' => $this->source_type]);
        return parent::prepare($builder);
    }

    /**
     * 设置查询条件
     * @return $this
     */
    public function active()
    {
        return $this->andWhere(['status' => Comment::STATUS_PUBLISHED]);
    }

    /**
     * 获取指定Model的评论
     * @param string $sourceType
     * @param int $sourceId
     * @return $this
     * @deprecated 过期了
     */
    public function source($sourceType, $sourceId)
    {
        return $this->andWhere(['source_type' => $sourceType, 'source_id' => $sourceId])->active();
    }
}