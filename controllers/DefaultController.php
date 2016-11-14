<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace yuncms\comment\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yuncms\comment\models\Comment;

/**
 * Class DefaultController
 * @package yuncms\comment\controllers
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['@']
                    ],
                ],
            ],
        ];
    }

    public function actionCreate()
    {
        $model = new Comment();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash(Yii::t('comment', 'Comment finish.'));
            return $this->goBack();
        }
        return $this->render('create', ['model' => $model]);
    }
}