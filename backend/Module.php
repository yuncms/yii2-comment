<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace yuncms\comment\backend;

class Module extends \yuncms\comment\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'yuncms\comment\backend\controllers';

    /**
     * @var string
     */
    public $defaultRoute = 'comment';
}