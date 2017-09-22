<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

?>
<div class="media-left">
    <a href="<?= Url::to(['/user/space/view', 'id' => $model->user_id]) ?>" target="_blank">
        <img class="media-object avatar-27" alt="<?= Html::encode($model->user->nickname) ?>"
             src="<?= $model->user->getAvatar() ?>">
    </a>
</div>
<div class="media-body">
    <div class="media-heading">
        <a href="<?= Url::to(['/user/space/view', 'id' => $model->user_id]) ?>"
           target="_blank"><?= $model->user->nickname ?></a>
        <?php if ($model->to_user_id): ?>
            <span class="text-muted">回复 </span>
            <a href="<?= Url::to(['/user/space/view', 'id' => $model->to_user_id]) ?>"
               target="_blank"><?= $model->toUser->nickname ?></a>
        <?php endif; ?>
    </div>
    <div class="content"><p><?= HtmlPurifier::process($model->content) ?></p></div>
    <div class="media-footer">
        <span class="text-muted"><?= Yii::$app->formatter->asRelativeTime($model->created_at); ?></span>
        <?php if (!Yii::$app->user->isGuest && $model->user_id != Yii::$app->user->id): ?>
            <a href="#" class="ml-10 comment-reply"
               data-source_id="<?= $model->source_id ?>" data-to_user_id="<?= $model->user_id ?>"
               data-source_type="<?= $model->source_type ?>"
               data-message="回复 <?= Html::encode($model->user->nickname) ?>"><i class="fa fa-reply"></i> 回复</a>
        <?php endif; ?>
    </div>

</div>
