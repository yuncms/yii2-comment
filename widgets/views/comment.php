<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="collapse widget-comments mb-20" id="comments-<?= $source_type ?>-<?= $source_id ?>"
     data-source_type="<?= $source_type ?>" data-source_id="<?= $source_type ?>">
    <div class="widget-comment-list"></div>
    <?php if (!Yii::$app->user->isGuest): ?>
        <div class="widget-comment-form row">
            <?php
            /** @var ActiveForm $form */
            $form = ActiveForm::begin(['action' => Url::to(['/comment/default/create']),]);
            echo $form->field($model,'source_id')->hiddenInput()->label(false)->error(false);
            echo $form->field($model,'source_type')->hiddenInput()->label(false)->error(false);
            ?>
            <div class="col-md-12">
                <div class="form-group">
                    <?= $form->field($model, 'content')->textarea(['placeholder' => '写下你的评论'])->label(false); ?>
                </div>
            </div>
            <div class="col-md-12 text-right">
                <?php if ($hide_cancel): ?>
                    <a href="#" class="text-muted collapse-cancel"
                       data-collapse_id="comments-<?= $source_type ?>-<?= $source_id ?>">取消</a>
                <?php endif; ?>
                <?= Html::submitButton(Yii::t('comment', 'Submit Comment'), ['class' => 'btn btn-primary btn-sm ml-10 comment-btn']) ?>
            </div>
            <?php ActiveForm::end() ?>
        </div>
    <?php else: ?>
        <div class="widget-comment-form row">
            <div class="col-md-12">
                请先 <a href="{{ route('auth.user.login') }}">登录</a> 后评论
            </div>
        </div>
    <?php endif ?>
</div>
