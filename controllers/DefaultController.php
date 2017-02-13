<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace yuncms\comment\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yuncms\comment\models\Comment;
use yuncms\comment\models\CommentForm;

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
                        'actions' => ['list', 'store'],
                        'roles' => ['@', '?']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['store'],
                        'roles' => ['@']
                    ],
                ],
            ],
        ];
    }

    /**
     * ajax加载评论
     * @param string $sourceType
     * @param int $sourceId
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionList($sourceType, $sourceId)
    {
        $source = null;
        if ($sourceType == 'question' && Yii::$app->hasModule('question')) {
            $source = \yuncms\question\models\Question::findOne($sourceId);
        } else if ($sourceType === 'answer' && Yii::$app->hasModule('question')) {
            $source = \yuncms\question\models\Answer::findOne($sourceId);
        } else if ($sourceType == 'article' && Yii::$app->hasModule('article')) {
            $source = \yuncms\article\models\Article::findOne($sourceId);
        }//etc..

        if (!$source) {
            throw new NotFoundHttpException ();
        }

        $query = Comment::find()->where([
            'source_id' => $sourceId,
            'source_type' => get_class($source),
        ])->with('user');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $this->renderPartial('list', ['dataProvider' => $dataProvider]);
    }

    /**
     * 评论保存
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionStore()
    {
        $sourceType = Yii::$app->request->post('sourceType');
        $sourceId = Yii::$app->request->post('sourceId');
        /** @var null|\yii\db\ActiveRecord $source */
        $source = null;
        if ($sourceType == 'question' && Yii::$app->hasModule('question')) {
            $source = \yuncms\question\models\Question::findOne($sourceId);
            $notify_subject = $source->title;
            $notify_type = 'comment_question';
            $notify_refer_type = 'question';
            $notify_refer_id = 0;
        } else if ($sourceType === 'answer' && Yii::$app->hasModule('question')) {
            $source = \yuncms\question\models\Answer::findOne($sourceId);
            $notify_subject = $source->question->title;
            $notify_type = 'comment_answer';
            $notify_refer_type = 'answer';
            $notify_refer_id = $source->question_id;
        } else if ($sourceType == 'article' && Yii::$app->hasModule('article')) {
            $source = \yuncms\article\models\Article::findOne($sourceId);
            $notify_subject = $source->title;
            $notify_type = 'comment_article';
            $notify_refer_type = 'article';
            $notify_refer_id = $source->id;
        }//etc..

        if (!$source) {
            throw new NotFoundHttpException ();
        }

        $data = [
            'user_id' => Yii::$app->user->id,
            'source_id' => $sourceId,
            'source_type' => get_class($source),
            'content' => Yii::$app->request->post('content'),
            'to_user_id' => Yii::$app->request->post('to_user_id'),
        ];
        $model = new Comment($data);
        if ($model->save()) {
            /*问题、回答、文章评论数+1*/
            $source->updateCounters(['comments' => 1]);
            if ($model->to_user_id > 0) {
                Yii::$app->getModule('user')->notify(Yii::$app->user->id, $model->to_user_id, 'reply_comment', $notify_subject, $sourceId, $model->content, $notify_refer_type, $notify_refer_id);
            } else {
                Yii::$app->getModule('user')->notify(Yii::$app->user->id, $source->user_id, $notify_type, $notify_subject, $sourceId, $model->content, $notify_refer_type, $notify_refer_id);
            }
            return $this->renderPartial('detail', ['model' => $model, 'source_type' => $sourceType, 'source_id' => $sourceId]);
        } else {
            throw new ForbiddenHttpException($model->getFirstError());
        }
    }
}