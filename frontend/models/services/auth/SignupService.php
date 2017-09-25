<?php
namespace frontend\models\services\auth;

use common\models\User;
use frontend\models\SignupForm;
use yii\mail\MailerInterface;

class SignupService
{
    private $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function requestSignup(SignupForm $form)
    {
        if(User::findOne(['username' => $form->username]))
            throw new \DomainException('User is already exist');
        if(User::findOne(['email' => $form->email]))
            throw new \DomainException('Email is already exist');
        $user = User::create($form->username, $form->email, $form->password);
        $user->save();

        $sent =  $this->mailer->compose([
            'html' => 'emailConfirmToken-html',
            'text' => 'emailConfirmToken-text',
        ], ['token' => $user->email_confirm_token])
            ->setTo($form->email)
            ->setSubject('Registration confirm message')
            ->send();

        if(!$sent)
            throw new \DomainException('Sending error');
    }

    public function confirm($token)
    {
        if(empty($token))
            throw new \DomainException('Token is empty');

        if(! $user=User::findOne(['email_confirm_token' => $token]))
            throw new \DomainException('Token is not found');

        $user->confirmSignup();
        $user->save();

        return $user;

    }
}

