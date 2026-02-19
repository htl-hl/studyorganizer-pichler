<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * Unified identity for user/admin (User table) and teacher (Teacher table).
 */
class AuthIdentity implements IdentityInterface
{
    public const TYPE_USER = 'user';
    public const TYPE_TEACHER = 'teacher';

    private $accountType;
    private $accountId;
    private $username;
    private $role;
    private $passwordHash;

    /**
     * @param string $accountType
     * @param int $accountId
     * @param string $username
     * @param string $role
     * @param string|null $passwordHash
     */
    private function __construct($accountType, $accountId, $username, $role, $passwordHash = null)
    {
        $this->accountType = $accountType;
        $this->accountId = $accountId;
        $this->username = $username;
        $this->role = $role;
        $this->passwordHash = $passwordHash;
    }

    /**
     * Finds identity from login form credentials.
     *
     * Teacher login uses existing schema fields:
     * - username: Teacher.firstname
     * - password: Teacher.T_ID
     *
     * @param string $username
     * @param string $password
     * @param string $loginAs
     * @return self|null
     */
    public static function findByCredentials($username, $password, $loginAs)
    {
        if ($loginAs === self::TYPE_TEACHER) {
            if (!ctype_digit((string)$password)) {
                return null;
            }

            $teacher = Teacher::find()
                ->where(['firstname' => $username, 'T_ID' => (int)$password])
                ->one();

            return $teacher === null ? null : self::fromTeacher($teacher);
        }

        $user = User::findOne(['U_username' => $username]);
        if ($user === null || !$user->isUserOrAdmin()) {
            return null;
        }

        return self::fromUser($user);
    }

    /**
     * @inheritDoc
     */
    public static function findIdentity($id)
    {
        if ($id === null || $id === '') {
            return null;
        }

        $rawId = (string)$id;
        $type = self::TYPE_USER;

        if (strpos($rawId, ':') !== false) {
            [$type, $rawId] = explode(':', $rawId, 2);
        }

        $numericId = (int)$rawId;
        if ($numericId <= 0) {
            return null;
        }

        if ($type === self::TYPE_TEACHER) {
            $teacher = Teacher::findOne(['T_ID' => $numericId]);

            return $teacher === null ? null : self::fromTeacher($teacher);
        }

        $user = User::findOne(['U_ID' => $numericId]);
        if ($user === null || !$user->isUserOrAdmin()) {
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
        return $this->accountType . ':' . $this->accountId;
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
     * User/admin password validation.
     * Teacher auth is already validated in findByCredentials().
     *
     * @param string $password
     * @return bool
     */
    public function validatePassword($password)
    {
        if ($this->accountType === self::TYPE_TEACHER) {
            return true;
        }

        $hashInfo = password_get_info((string)$this->passwordHash);
        if (!empty($hashInfo['algo'])) {
            return Yii::$app->security->validatePassword($password, $this->passwordHash);
        }

        // Compatibility with existing plain-text passwords.
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
            self::TYPE_USER,
            (int)$user->U_ID,
            $user->U_username,
            $user->normalizedRole(),
            $user->U_password
        );
    }

    /**
     * @param Teacher $teacher
     * @return self
     */
    private static function fromTeacher(Teacher $teacher)
    {
        return new self(
            self::TYPE_TEACHER,
            (int)$teacher->T_ID,
            $teacher->firstname,
            self::TYPE_TEACHER
        );
    }
}
