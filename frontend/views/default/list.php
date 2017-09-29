<?php
use yii\widgets\ListView;
?>
<?= ListView::widget([
    'options' => ['class' => null],
    'dataProvider' => $dataProvider,
    'itemView' => '_item',//子视图
    'itemOptions'=>['class'=>'media'],
    'layout' => "{items}\n{pager}",
]); ?>
