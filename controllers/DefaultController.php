<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace yuncms\comment\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class DefaultController
 * @package yuncms\comment\controllers
 */
class DefaultController extends Controller
{
    public function actionComment(){
        $sourceType = Yii::$app->request->post('sourceType');
        $sourceId = Yii::$app->request->post('sourceId');

        $source = null;
        if ($sourceType == 'question') {
            $source = Question::findOne($sourceId);
            $subject = $source->title;
        } else if ($sourceType == 'user') {
            $userClass = Yii::$app->user->identityClass;
            $source = $userClass::find()->with('userData')->where(['id'=>$sourceId])->one();
            $subject = $source->username;
        } else if ($sourceType == 'code') {
            $source = Code::findOne($sourceId);
            $subject = $source->title;
        }
        if (!$source) {
            throw new NotFoundHttpException ();
        }

    }
}