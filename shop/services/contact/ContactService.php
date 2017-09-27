<?php

namespace shop\services\contact;

use shop\entities\ContactForm;
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