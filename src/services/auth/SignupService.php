<?php
namespace src\services\auth;

use src\entities\User;
use src\repositories\UserRepository;
use src\forms\SignupForm;
use yii\mail\MailerInterface;

class SignupService
{
    private $mailer;
    private $users;
    public function __construct(MailerInterface $mailer, UserRepository $users)
    {
        $this->mailer = $mailer;
        $this->users = $users;
    }

    public function requestSignup(SignupForm $form)
    {
        if(User::findOne(['username' => $form->username]))
            throw new \DomainException('User is already exist');
        if(User::findOne(['email' => $form->email]))
            throw new \DomainException('Email is already exist');
        $user = User::create($form->username, $form->email, $form->password);
        $this->users->save($user);

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

        if(! $user = $this->users->getByEmailConfirmToken($token))
            throw new \DomainException('Token is not found');

        $user->confirmSignup();
        $this->users->save($user);

        return $user;

    }
}

