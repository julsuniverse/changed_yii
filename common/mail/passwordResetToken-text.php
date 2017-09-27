<?php

/* @var $this yii\web\View */
/* @var $user src\forms\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['auth/reset/reset-password', 'token' => $user->password_reset_token]);
?>
Hello <?= $user->username ?>,

Follow the link below to reset your password:

<?= $resetLink ?>
