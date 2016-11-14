<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>
<?php if (Yii::$app->getUser()->getIsGuest()): ?>
    请登录
<?php else: ?>

    <?php
    /** @var ActiveForm $form */
    $form = ActiveForm::begin([
        'action' => Url::to(['/comment/default/create']),
    ]);
    ?>
    <?= $form->field($model, 'source_type')->label(false); ?>
    <?= $form->field($model, 'source_id')->label(false); ?>

    <?= $form->field($model, 'content')
        ->textarea(['rows' => 6])
        ->hint(Yii::t('question', 'Markdown powered content'))
        ->label(false); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('comment', 'Submit') : Yii::t('comment', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end() ?>

<?php endif ?>