<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace yuncms\comment;

use Yii;


class Module extends \yii\base\Module
{
    public function init(){
        parent::init();
        /**
         * 注册语言包
         */
        if (!isset(Yii::$app->i18n->translations['comment*'])) {
            Yii::$app->i18n->translations['comment*'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en-US',
                'basePath' => __DIR__ . '/messages',
            ];
        }
    }
}