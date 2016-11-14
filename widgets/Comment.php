<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace yuncms\comment\widgets;

use yii\base\Widget;
use yii\base\InvalidConfigException;
use yuncms\comment\models\Comment as CommentForm;

/**
 * Class Comment
 * @package yuncms\comment\widgets
 */
class Comment extends Widget
{
    /**
     * @var string 内容类型
     */
    public $source_type;

    /**
     * @var int 关系ID
     */
    public $source_id;

    /** @var bool */
    public $validate = true;

    /** @inheritdoc */
    public function init()
    {
        parent::init();
        if (empty ($this->source_type)) {
            throw new InvalidConfigException ('The "source_type" property must be set.');
        }
        if (empty ($this->source_id)) {
            throw new InvalidConfigException ('The "source_id" property must be set.');
        }
    }

    /** @inheritdoc */
    public function run()
    {
        $model = new CommentForm([
            'source_type' => $this->source_type,
            'source_id' => $this->source_id
        ]);
        return $this->render('comment', [
            'model' => $model,
            'source_type' => $this->source_type,
            'source_id' => $this->source_id,
        ]);
    }
}