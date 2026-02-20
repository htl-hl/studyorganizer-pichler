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
    public $username;
    public $password;

    private $_identity = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            ['username', 'trim'],
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
            $this->_identity = AuthIdentity::findByUsername($this->username);
        }

        return $this->_identity;
    }
}
