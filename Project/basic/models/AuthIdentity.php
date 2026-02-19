<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * Unified identity for User/Admin/Teacher accounts from the User table.
 */
class AuthIdentity implements IdentityInterface
{
    public const TYPE_USER = 'user';
    public const TYPE_TEACHER = 'teacher';

    private $accountId;
    private $username;
    private $role;
    private $passwordHash;

    /**
     * @param int $accountId
     * @param string $username
     * @param string $role
     * @param string $passwordHash
     */
    private function __construct($accountId, $username, $role, $passwordHash)
    {
        $this->accountId = $accountId;
        $this->username = $username;
        $this->role = $role;
        $this->passwordHash = $passwordHash;
    }

    /**
     * Finds identity from username + selected login mode.
     *
     * @param string $username
     * @param string $loginAs
     * @return self|null
     */
    public static function findByCredentials($username, $loginAs)
    {
        $user = User::findOne(['U_username' => $username]);
        if ($user === null) {
            return null;
        }

        if ($loginAs === self::TYPE_TEACHER && !$user->isTeacher()) {
            return null;
        }

        if ($loginAs === self::TYPE_TEACHER && !$user->isActive()) {
            return null;
        }

        if ($loginAs === self::TYPE_USER && !$user->isUserOrAdmin()) {
            return null;
        }

        return self::fromUser($user);
    }

    /**
     * @inheritDoc
     */
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

    /**
     * @inheritDoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return (string)$this->accountId;
    }

    /**
     * @inheritDoc
     */
    public function getAuthKey()
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function validateAuthKey($authKey)
    {
        return false;
    }

    /**
     * @param string $password
     * @return bool
     */
    public function validatePassword($password)
    {
        $hashInfo = password_get_info((string)$this->passwordHash);
        if (!empty($hashInfo['algo'])) {
            return Yii::$app->security->validatePassword($password, $this->passwordHash);
        }

        return hash_equals((string)$this->passwordHash, (string)$password);
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param User $user
     * @return self
     */
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
