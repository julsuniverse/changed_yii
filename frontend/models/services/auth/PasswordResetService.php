<?php

namespace frontend\models\services\auth;

use common\models\User;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use Yii;
use yii\mail\MailerInterface;

class PasswordResetService
{
    private $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function request(PasswordResetRequestForm $form)
    {
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $form->email,
        ]);

        if (!$user)
        {
            throw new \DomainException('User not found');
        }

       $user->requestPasswordReset();
       $user->save();
       $sent =  $this->mailer
            ->compose(
                ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                ['user' => $user]
            )
            ->setTo($user->email)
            ->setSubject('Password reset for ' . Yii::$app->name)
            ->send();

       if(!$sent)
           throw new \DomainException('Sending error');

    }

    public function validateToken($token)
    {
        if (empty($token) || !is_string($token)) {
            throw new \DomainException('Password reset token cannot be blank.');
        }
        $user = User::findByPasswordResetToken($token);
        if (!$user) {
            throw new \DomainException('Wrong password reset token.');
        }
    }

    public function resetPassword($token, ResetPasswordForm $form)
    {
        $user = User::findByPasswordResetToken($token);
        if(!$user)
            throw new \DomainException('User not found');
        $user->resetPassword($form->password);
        return $user->save(false);
    }
}
