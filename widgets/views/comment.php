<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<?php if (Yii::$app->getUser()->getIsGuest()): ?>
    请登录
<?php else: ?>

    <?php
    /** @var ActiveForm $form */
    $form = ActiveForm::begin(['action' => Url::to(['/comment/default/create']),]);
    ?>
    <?= $form->field($model, 'source_type')->hiddenInput()->label(false)->error(false); ?>
    <?= $form->field($model, 'source_id')->hiddenInput()->label(false)->error(false); ?>
    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'content')
        ->textarea(['rows' => 6])
        ->hint(Yii::t('question', 'Markdown powered content'))
        ->label(false); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('comment', 'Submit'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end() ?>

<?php endif ?>