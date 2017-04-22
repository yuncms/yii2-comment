<?php

use yii\helpers\Html;
use xutl\inspinia\ActiveForm;

/* @var $this yii\web\View */
/* @var $model yuncms\comment\models\CommentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="comment-search pull-right">

    <?php $form = ActiveForm::begin([
        'layout' => 'inline',
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id', [
        'inputOptions' => [
            'placeholder' => $model->getAttributeLabel('id'),
        ],
    ]) ?>

    <?= $form->field($model, 'user_id', [
        'inputOptions' => [
            'placeholder' => $model->getAttributeLabel('user_id'),
        ],
    ]) ?>

    <?= $form->field($model, 'source_id', [
        'inputOptions' => [
            'placeholder' => $model->getAttributeLabel('source_id'),
        ],
    ]) ?>

<!--    --><?//= $form->field($model, 'source_type', [
//        'inputOptions' => [
//            'placeholder' => $model->getAttributeLabel('source_type'),
//        ],
//    ]) ?>
<!---->
<!--    --><?//= $form->field($model, 'parent', [
//        'inputOptions' => [
//            'placeholder' => $model->getAttributeLabel('parent'),
//        ],
//    ]) ?>

    <?php // echo $form->field($model, 'content') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
