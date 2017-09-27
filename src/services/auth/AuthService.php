<?php
namespace src\services\auth;
use src\entities\User;
use src\forms\LoginForm;
use src\repositories\UserRepository;
class AuthService
{
    private $users;
    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }
    public function auth(LoginForm $form): User
    {
        $user = $this->users->findByUsernameOrEmail($form->username);
        if (!$user || !$user->isActive() || !$user->validatePassword($form->password)) {
            throw new \DomainException('Undefined user or password.');
        }
        return $user;
    }
}