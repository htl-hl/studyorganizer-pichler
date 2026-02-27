<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public static function tableName()
    {
        return 'User';
    }

    public static function findIdentity($id)
    {
        echo "Debug: findIdentity called with id: $id<br>";
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public static function findByUsername($username)
    {
        echo "Debug: findByUsername called with: $username<br>";
        return static::findOne(['username' => $username]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        echo "Debug: validateAuthKey called<br>";
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword($password)
    {
        echo "Debug: validatePassword called<br>";
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }
}
