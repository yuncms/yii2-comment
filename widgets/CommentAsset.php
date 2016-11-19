<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace yuncms\comment\widgets;

use yii\web\AssetBundle;

/**
 * Class CommentAsset
 * @package yuncms\comment
 */
class CommentAsset extends AssetBundle
{
    public $sourcePath = '@vendor/yuncms/yii2-comment/assets';

    public $js = [
        'js/comment.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset'
    ];
}