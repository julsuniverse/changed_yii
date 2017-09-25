<?php

namespace frontend\models\services\contact;

use frontend\models\ContactForm;
use Yii;
use yii\mail\MailerInterface;

class ContactService
{
    private $mailer;
    private  $adminEmail;
    public function __construct($email, MailerInterface $mailer)
    {
        $this->adminEmail = $email;
        $this->mailer = $mailer;
    }

    public function sendEmail(ContactForm $form)
    {
        $sent =  $this->mailer->compose()
            ->setTo($this->adminEmail)
            ->setSubject($form->subject)
            ->setTextBody($form->body)
            ->send();

        if(!$sent)
            throw new \DomainException('Sending error');
    }
}