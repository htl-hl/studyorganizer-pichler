<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    public const ROLE_USER = 'user';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_TEACHER = 'teacher';

    public static function tableName()
    {
        return 'User';
    }

    public function rules()
    {
        return [
            [['U_username', 'U_password', 'U_role', 'U_creation_date'], 'required'],
            [['U_username'], 'trim'],
            [['U_is_active'], 'default', 'value' => 1],
            [['U_is_active'], 'integer'],
            [['U_creation_date'], 'safe'],
            [['U_username', 'U_password', 'U_role'], 'string', 'max' => 255],
            [['U_username'], 'unique'],
            ['U_role', 'in', 'range' => [self::ROLE_USER, self::ROLE_ADMIN, self::ROLE_TEACHER]],
        ];
    }

    public function attributeLabels()
    {
        return [
            'U_ID' => 'U ID',
            'U_username' => 'Username',
            'U_password' => 'Password',
            'U_role' => 'Role',
            'U_is_active' => 'Is Active',
            'U_creation_date' => 'Creation Date',
        ];
    }

    public function getHomeworks()
    {
        return $this->hasMany(Homework::class, ['U_ID' => 'U_ID']);
    }

    public function getAssignedHomeworks()
    {
        return $this->hasMany(Homework::class, ['Teacher_U_ID' => 'U_ID']);
    }

    public function getUserSubjects()
    {
        return $this->hasMany(User_Subject::class, ['U_ID' => 'U_ID']);
    }

    public function beforeValidate()
    {
        if (empty($this->U_creation_date)) {
            $this->U_creation_date = date('Y-m-d H:i:s');
        }

        return parent::beforeValidate();
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if (!$this->isPasswordHash($this->U_password)) {
            $this->setPassword($this->U_password);
        }

        return true;
    }

    public function setPassword($password)
    {
        $this->U_password = Yii::$app->security->generatePasswordHash((string)$password);
    }

    public function validatePassword($password)
    {
        $hashInfo = password_get_info((string)$this->U_password);
        if (!empty($hashInfo['algo'])) {
            return Yii::$app->security->validatePassword((string)$password, (string)$this->U_password);
        }

        return hash_equals((string)$this->U_password, (string)$password);
    }

    public function normalizedRole()
    {
        return strtolower((string)$this->U_role);
    }

    public function getUsername()
    {
        return (string)$this->U_username;
    }

    public function getRole()
    {
        return $this->normalizedRole();
    }

    public function isAdmin()
    {
        return $this->normalizedRole() === self::ROLE_ADMIN;
    }

    public function isUserOrAdmin()
    {
        return in_array($this->normalizedRole(), [self::ROLE_USER, self::ROLE_ADMIN], true);
    }

    public function isTeacher()
    {
        return $this->normalizedRole() === self::ROLE_TEACHER;
    }

    public function isActive()
    {
        return (int)$this->U_is_active === 1;
    }

    public static function findByUsername($username)
    {
        return static::findOne(['U_username' => trim((string)$username)]);
    }

    public static function findIdentity($id)
    {
        return static::findOne(['U_ID' => (int)$id]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
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

    private function isPasswordHash($value)
    {
        $hashInfo = password_get_info((string)$value);

        return !empty($hashInfo['algo']);
    }

    public static function find()
    {
        return new UserQuery(get_called_class());
    }
}
