<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace yuncms\comment\frontend\widgets\assets;

use yii\web\AssetBundle;

/**
 * Class CommentAsset
 * @package yuncms\comment
 */
class CommentAsset extends AssetBundle
{
    public $sourcePath = '@yuncms/comment/frontend/widgets/views/assets';

    public $js = [
        'js/comment.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset'
    ];
}