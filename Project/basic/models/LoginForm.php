<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read AuthIdentity|null $identity
 *
 */
class LoginForm extends Model
{
    public const LOGIN_AS_USER = AuthIdentity::TYPE_USER;
    public const LOGIN_AS_TEACHER = AuthIdentity::TYPE_TEACHER;

    public $username;
    public $password;
    public $loginAs = self::LOGIN_AS_USER;

    private $_identity = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password', 'loginAs'], 'required'],
            ['username', 'trim'],
            ['loginAs', 'in', 'range' => array_keys($this->getLoginAsOptions())],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username' => 'Username',
            'password' => 'Password',
            'loginAs' => 'Login as',
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $identity = $this->getIdentity();

            if (!$identity || !$identity->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getIdentity(), 0);
        }
        return false;
    }

    /**
     * Finds identity by credentials.
     *
     * @return AuthIdentity|null
     */
    public function getIdentity()
    {
        if ($this->_identity === false) {
            $this->_identity = AuthIdentity::findByCredentials($this->username, $this->loginAs);
        }

        return $this->_identity;
    }

    /**
     * @return array<string, string>
     */
    public function getLoginAsOptions()
    {
        return [
            self::LOGIN_AS_USER => 'User / Admin',
            self::LOGIN_AS_TEACHER => 'Teacher',
        ];
    }
}
