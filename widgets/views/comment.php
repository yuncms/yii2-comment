<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="collapse widget-comments mb-20" id="comments-<?=$source_type?>-<?=$source_id?>" data-source_type="<?=$source_type?>" data-source_id="<?=$source_type?>">
    <div class="widget-comment-list"></div>
    <?php if (!Yii::$app->user->isGuest): ?>
        <div class="widget-comment-form row">
            <form class="col-md-12">
                <div class="form-group">
                    <textarea name="content" placeholder="写下你的评论" class="form-control"
                              id="comment-<?=$source_type?>-content-<?=$source_id?>"></textarea>
                </div>
            </form>
            <div class="col-md-12 text-right">
                <?php if($hide_cancel):?>
                <a href="#" class="text-muted collapse-cancel"
                   data-collapse_id="comments-<?=$source_type?>-<?=$source_id?>">取消</a>
                <?php endif;?>
                <button type="submit" class="btn btn-primary btn-sm ml-10 comment-btn"
                        id="<?=$source_type?>-comment-<?=$source_id?>-btn"
                        data-token="{{ csrf_token() }}" data-source_id="<?=$source_id?>"
                        data-source_type="<?=$source_type?>" data-to_user_id="0">提交评论
                </button>
            </div>
        </div>
        <!--<?php
        /** @var ActiveForm $form */
        $form = ActiveForm::begin(['action' => Url::to(['/comment/default/create']),]);
        ?>
        <?= $form->field($model, 'source_type')->hiddenInput()->label(false)->error(false); ?>
        <?= $form->field($model, 'source_id')->hiddenInput()->label(false)->error(false); ?>
        <?= $form->errorSummary($model); ?>

        <?= $form->field($model, 'content')
            ->textarea(['rows' => 6])
            ->label(false); ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('comment', 'Submit'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end() ?>-->

    <?php else: ?>
        <div class="widget-comment-form row">
            <div class="col-md-12">
                请先 <a href="{{ route('auth.user.login') }}">登录</a> 后评论
            </div>
        </div>
    <?php endif ?>
</div>
