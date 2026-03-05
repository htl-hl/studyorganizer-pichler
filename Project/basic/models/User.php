<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public const ROLE_USER = 'user';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_TEACHER = 'teacher';

    public static function tableName()
    {
        return 'User';
    }

    public static function primaryKey()
    {
        return ['U_ID'];
    }

    public function rules()
    {
        return [
            [['U_username', 'U_password', 'U_role', 'U_creation_date'], 'required'],
            [['U_username'], 'trim'],
            [['U_username'], 'string', 'max' => 255],
            [['U_username'], 'unique'],
            [['U_password'], 'string', 'max' => 255],
            [['U_role'], 'in', 'range' => [self::ROLE_USER, self::ROLE_ADMIN, self::ROLE_TEACHER]],
            [['U_is_active'], 'boolean'],
            [['U_is_active'], 'default', 'value' => 1],
            [['U_creation_date'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'U_ID' => 'ID',
            'U_username' => 'Username',
            'U_password' => 'Password',
            'U_role' => 'Role',
            'U_is_active' => 'Active',
            'U_creation_date' => 'Creation Date',
        ];
    }

    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        if ($this->isNewRecord && $this->U_creation_date === null) {
            $this->U_creation_date = date('Y-m-d H:i:s');
        }

        if ($this->U_is_active === null) {
            $this->U_is_active = 1;
        }

        if ($this->U_role !== null) {
            $this->U_role = strtolower(trim((string)$this->U_role));
        }

        return true;
    }

    public static function findIdentity($id)
    {
        return static::findOne(['U_ID' => (int)$id]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public static function findByUsername($username)
    {
        return static::findOne(['U_username' => trim((string)$username)]);
    }

    public function getId()
    {
        return (string)$this->U_ID;
    }

    public function getAuthKey()
    {
        return hash_hmac(
            'sha256',
            $this->U_ID . '|' . $this->U_username . '|' . $this->U_password,
            Yii::$app->request->cookieValidationKey
        );
    }

    public function validateAuthKey($authKey)
    {
        return hash_equals($this->getAuthKey(), (string)$authKey);
    }

    public function validatePassword($password)
    {
        $hashInfo = password_get_info((string)$this->U_password);
        if (!empty($hashInfo['algo'])) {
            return Yii::$app->security->validatePassword((string)$password, (string)$this->U_password);
        }

        return hash_equals((string)$this->U_password, (string)$password);
    }

    public function setPassword($password)
    {
        $this->U_password = Yii::$app->security->generatePasswordHash((string)$password);
    }

    public function getUsername()
    {
        return (string)$this->U_username;
    }

    public function getRole()
    {
        return $this->normalizedRole();
    }

    public function normalizedRole()
    {
        return strtolower(trim((string)$this->U_role));
    }

    public function isAdmin()
    {
        return $this->normalizedRole() === self::ROLE_ADMIN;
    }

    public function isTeacher()
    {
        return $this->normalizedRole() === self::ROLE_TEACHER;
    }

    public function isUserOrAdmin()
    {
        return in_array($this->normalizedRole(), [self::ROLE_USER, self::ROLE_ADMIN], true);
    }

    public function isActive()
    {
        return (int)$this->U_is_active === 1;
    }

    public function getUserSubjects()
    {
        return $this->hasMany(User_Subject::class, ['U_ID' => 'U_ID']);
    }

    public function getSubjects()
    {
        return $this->hasMany(Subject::class, ['S_ID' => 'S_ID'])
            ->via('userSubjects');
    }

    public function getOwnedHomeworks()
    {
        return $this->hasMany(Homework::class, ['U_ID' => 'U_ID']);
    }

    public function getAssignedHomeworks()
    {
        return $this->hasMany(Homework::class, ['Teacher_U_ID' => 'U_ID']);
    }

    public static function find()
    {
        return new UserQuery(get_called_class());
    }
}
