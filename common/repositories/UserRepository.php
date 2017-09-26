<?php
namespace common\repositories;
use common\models\User;
class UserRepository
{
    public function findByUsernameOrEmail($value): ?User
    {
        return User::find()->andWhere(['or', ['username' => $value], ['email' => $value]])->one();
    }

    public function getByEmailConfirmToken($token): User
    {
        return $this->getBy(['email_confirm_token' => $token]);
    }

    public function getByEmail($email): User
    {
        return $this->getBy(['email' => $email]);
    }

    public function getByPasswordResetToken($token): User
    {
        return $this->getBy(['password_reset_token' => $token]);
    }

    public function existsByPasswordResetToken(string $token): bool
    {
        return (bool) User::findByPasswordResetToken($token);
    }

    public function save(User $user): void
    {
        if (!$user->save()) {
            throw new \RuntimeException('Saving error.');
        }
    }

    private function getBy(array $condition): User
    {
        if (!$user = User::find()->andWhere($condition)->limit(1)->one()) {
            throw new \DomainException('User not found.');
        }
        return $user;
    }

    public function alreadyExistEmail($email)
    {
        if(User::find()->where(['email' => $email])->limit(1)->one())
            throw new \DomainException('Email is alredy exist');
    }

    public function alreadyExistUsername($username)
    {
        if(User::find()->where(['username' => $username])->limit(1)->one())
            throw new \DomainException('Username is alredy exist');
    }
}