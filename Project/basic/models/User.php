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

    // Primärschlüssel ist U_ID
    public static function primaryKey()
    {
        return ['U_ID'];
    }

    // IdentityInterface Methoden
    public static function findIdentity($id)
    {
        return static::findOne(['U_ID' => $id]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null; // Wird nicht verwendet
    }

    public static function findByUsername($username)
    {
        return static::findOne(['U_username' => $username]);
    }

    public function getId()
    {
        return $this->U_ID;
    }

    public function getAuthKey()
    {
        // Einen eindeutigen Auth-Key generieren (z.B. aus ID und Username)
        return md5($this->U_ID . $this->U_username);
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    // Passwort-Validierung
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->U_password);
    }

    // Passwort setzen (für Registrierung)
    public function setPassword($password)
    {
        $this->U_password = Yii::$app->security->generatePasswordHash($password);
    }

    // Auth-Key generieren (für Registrierung)
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    // Rollen-Funktionen (optional)
    public function isAdmin()
    {
        return $this->U_role === 'admin';
    }

    public function isActive()
    {
        return $this->U_is_active == 1;
    }
}
