<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "User".
 *
 * @property int $U_ID
 * @property string $U_username
 * @property string $U_password
 * @property string $U_role
 * @property int $U_is_active
 * @property string $U_creation_date
 *
 * @property Homework[] $homeworks
 */
class User extends \yii\db\ActiveRecord
{
    public const ROLE_USER = 'user';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_TEACHER = 'teacher';


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'User';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['U_username', 'U_password', 'U_role', 'U_creation_date'], 'required'],
            [['U_is_active'], 'default', 'value' => 1],
            [['U_is_active'], 'integer'],
            [['U_creation_date'], 'safe'],
            [['U_username', 'U_password', 'U_role'], 'string', 'max' => 255],
            [['U_username'], 'unique'],
            ['U_role', 'in', 'range' => [self::ROLE_USER, self::ROLE_ADMIN, self::ROLE_TEACHER]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'U_ID' => Yii::t('app', 'U ID'),
            'U_username' => Yii::t('app', 'U Username'),
            'U_password' => Yii::t('app', 'U Password'),
            'U_role' => Yii::t('app', 'U Role'),
            'U_is_active' => Yii::t('app', 'U Is Active'),
            'U_creation_date' => Yii::t('app', 'U Creation Date'),
        ];
    }

    /**
     * Gets query for [[Homeworks]].
     *
     * @return \yii\db\ActiveQuery|HomeworkQuery
     */
    public function getHomeworks()
    {
        return $this->hasMany(Homework::class, ['U_ID' => 'U_ID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssignedHomeworks()
    {
        return $this->hasMany(Homework::class, ['Teacher_U_ID' => 'U_ID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserSubjects()
    {
        return $this->hasMany(User_Subject::class, ['U_ID' => 'U_ID']);
    }

    /**
     * @return bool
     */
    public function beforeValidate()
    {
        if (empty($this->U_creation_date)) {
            $this->U_creation_date = date('Y-m-d H:i:s');
        }

        return parent::beforeValidate();
    }

    /**
     * Hashes password only when a plain text value is provided.
     *
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (!$this->isPasswordHash($this->U_password)) {
            $this->setPassword($this->U_password);
        }

        return parent::beforeSave($insert);
    }

    /**
     * @param string $password
     * @return void
     */
    public function setPassword($password)
    {
        $this->U_password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * @param string $password
     * @return bool
     */
    public function validatePassword($password)
    {
        $hashInfo = password_get_info((string)$this->U_password);
        if (!empty($hashInfo['algo'])) {
            return Yii::$app->security->validatePassword($password, $this->U_password);
        }

        return hash_equals((string)$this->U_password, (string)$password);
    }

    /**
     * @return string
     */
    public function normalizedRole()
    {
        return strtolower((string)$this->U_role);
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->normalizedRole() === self::ROLE_ADMIN;
    }

    /**
     * @return bool
     */
    public function isUserOrAdmin()
    {
        return in_array($this->normalizedRole(), [self::ROLE_USER, self::ROLE_ADMIN], true);
    }

    /**
     * @return bool
     */
    public function isTeacher()
    {
        return $this->normalizedRole() === self::ROLE_TEACHER;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return (int)$this->U_is_active === 1;
    }

    /**
     * Backward-compatible helper used by existing tests/code.
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['U_username' => $username]);
    }

    /**
     * @param string $value
     * @return bool
     */
    private function isPasswordHash($value)
    {
        $hashInfo = password_get_info((string)$value);

        return !empty($hashInfo['algo']);
    }

    /**
     * {@inheritdoc}
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

}
