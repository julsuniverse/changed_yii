<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

$activateLink = Yii::$app->urlManager->createAbsoluteUrl(['site/confirm-email', 'token' => $token]);
?>
Hello,

Follow the link below to activate your account:

<?= $activateLink ?>
