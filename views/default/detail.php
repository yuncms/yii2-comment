<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
?>
<div class="media">
    <?= $this->render(
        '_item.php', ['model' => $model]
    ) ?>
</div>