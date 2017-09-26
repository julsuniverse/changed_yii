<?php

namespace frontend\models\services\auth;

use common\repositories\UserRepository;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use Yii;
use yii\mail\MailerInterface;

class PasswordResetService
{
    private $mailer;
    private  $users;
    public function __construct(MailerInterface $mailer, UserRepository $users)
    {
        $this->mailer = $mailer;
        $this->users = $users;
    }

    public function request(PasswordResetRequestForm $form)
    {
        $user = $this->users->getByEmail($form->email);

        if (!$user)
        {
            throw new \DomainException('User not found');
        }

        if (!$user->isActive()) {
            throw new \DomainException('User is not active.');
        }

       $user->requestPasswordReset();
       $this->users->save($user);
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
        $user = $this->users->existsByPasswordResetToken($token);
        if (!$user) {
            throw new \DomainException('Wrong password reset token.');
        }
    }

    public function resetPassword($token, ResetPasswordForm $form)
    {
        $user = $this->users->getByPasswordResetToken($token);
        if(!$user)
            throw new \DomainException('User not found');
        $user->resetPassword($form->password);
        $this->users->save($user);
    }
}
