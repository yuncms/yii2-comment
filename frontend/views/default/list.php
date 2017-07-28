<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;

?>
<?php Pjax::begin(); ?>
<?= ListView::widget([
    'options' => ['class' => null],
    'dataProvider' => $dataProvider,
    'itemView' => '_item',//子视图
    'itemOptions'=>['class'=>'media'],
    'layout' => "{items}\n{pager}",
]); ?>
<?php Pjax::end(); ?>
