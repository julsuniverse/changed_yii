<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user frontend\models\User */

$activateLink = Yii::$app->urlManager->createAbsoluteUrl(['site/confirm-email', 'token' => $token]);
?>
<div class="password-reset">
    <p>Hello,</p>

    <p>Follow the link below to activate your account:</p>

    <p><?= Html::a(Html::encode($activateLink), $activateLink) ?></p>
</div>
