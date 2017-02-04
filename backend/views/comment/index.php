<?php
use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yuncms\admin\widgets\Jarvis;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel yuncms\comment\models\CommentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('comment', 'Manage Comment');
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs("jQuery(\"#batch_deletion\").on(\"click\", function () {
    yii.confirm('" . Yii::t('app', 'Are you sure you want to delete this item?') . "',function(){
        var ids = jQuery('#gridview').yiiGridView(\"getSelectedRows\");
        jQuery.post(\"" . Url::to(['/comment/comment/batch-delete']) . "\",{ids:ids});
    });
});", View::POS_LOAD);
?>
<section id="widget-grid">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12 comment-index">
            <?php Pjax::begin(); ?>
            <?php Jarvis::begin([
                'noPadding' => true,
                'editbutton' => false,
                'deletebutton' => false,
                'header' => Html::encode($this->title),
                'bodyToolbarActions' => [
                    [
                        'label' => Yii::t('comment', 'Manage Comment'),
                        'url' => ['/comment/comment/index'],
                    ],
                    [
                        'options' => ['id' => 'batch_deletion', 'class' => 'btn btn-sm btn-danger'],
                        'label' => Yii::t('comment', 'Batch Deletion'),
                        'url' => 'javascript:void(0);',
                    ]
                ]
            ]); ?>
            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
            <?= GridView::widget([
                'options' => ['id' => 'gridview'],
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'class' => 'yii\grid\CheckboxColumn',
                        "name" => "id",
                    ],
                    //['class' => 'yii\grid\SerialColumn'],
                    'id',
                    'content:text',
                    'parent',
                    'user_id',
                    'source_id',
                    'source_type',
                    'status',
                    'created_at:datetime',
                    // 'updated_at',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => Yii::t('app', 'Operation'),
                        'template' => '{delete}',
//                        'buttons' => [
//                            'update' => function ($url, $model, $key) {
//                                return $model->status === 'editable' ? Html::a('Update', $url) : '';
//                            },
//                        ],

                    ],
                ],
            ]); ?>
            <?php Jarvis::end(); ?>
            <?php Pjax::end(); ?>
        </article>
    </div>
</section>
