<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;




class AuthIdentity implements IdentityInterface
{
    public const TYPE_USER = 'user';
    public const TYPE_TEACHER = 'teacher';

    private $accountId;
    private $username;
    private $role;
    private $passwordHash;

    





    private function __construct($accountId, $username, $role, $passwordHash)
    {
        $this->accountId = $accountId;
        $this->username = $username;
        $this->role = $role;
        $this->passwordHash = $passwordHash;
    }

    





    public static function findByUsername($username)
    {
        return self::findByCredentials($username);
    }

    






    public static function findByCredentials($username, $loginAs = null)
    {
        $user = User::findOne(['U_username' => $username]);
        if ($user === null) {
            return null;
        }

        if ($user->isTeacher() && !$user->isActive()) {
            return null;
        }

        if ($loginAs === self::TYPE_TEACHER && !$user->isTeacher()) {
            return null;
        }

        if ($loginAs === self::TYPE_USER && !$user->isUserOrAdmin()) {
            return null;
        }

        return self::fromUser($user);
    }

    


    public static function findIdentity($id)
    {
        $user = User::findOne(['U_ID' => (int)$id]);
        if ($user === null) {
            return null;
        }

        if ($user->isTeacher() && !$user->isActive()) {
            return null;
        }

        return self::fromUser($user);
    }

    


    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    


    public function getId()
    {
        return (string)$this->accountId;
    }

    


    public function getAuthKey()
    {
        return hash_hmac(
            'sha256',
            $this->accountId . '|' . $this->username . '|' . $this->passwordHash,
            Yii::$app->request->cookieValidationKey
        );
    }

    


    public function validateAuthKey($authKey)
    {
        return hash_equals($this->getAuthKey(), (string)$authKey);
    }

    



    public function validatePassword($password)
    {
        $hashInfo = password_get_info((string)$this->passwordHash);
        if (!empty($hashInfo['algo'])) {
            return Yii::$app->security->validatePassword($password, $this->passwordHash);
        }

        return hash_equals((string)$this->passwordHash, (string)$password);
    }

    


    public function getUsername()
    {
        return $this->username;
    }

    


    public function getRole()
    {
        return $this->role;
    }

    



    private static function fromUser(User $user)
    {
        return new self(
            (int)$user->U_ID,
            $user->U_username,
            $user->normalizedRole(),
            $user->U_password
        );
    }
}
