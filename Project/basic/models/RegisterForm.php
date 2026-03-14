<?php

namespace app\models;

use Yii;
use yii\base\Model;

class RegisterForm extends Model
{
    public $username;
    public $password;
    public $passwordRepeat;

    


    public function rules()
    {
        return [
            [['username', 'password', 'passwordRepeat'], 'required'],
            ['username', 'trim'],
            ['username', 'string', 'min' => 3, 'max' => 255],
            ['username', 'unique', 'targetClass' => User::class, 'targetAttribute' => 'U_username', 'message' => 'This username is already taken.'],
            ['password', 'string', 'min' => 6],
            ['passwordRepeat', 'compare', 'compareAttribute' => 'password', 'message' => 'Passwords do not match.'],
        ];
    }

    


    public function attributeLabels()
    {
        return [
            'username' => 'Username',
            'password' => 'Password',
            'passwordRepeat' => 'Repeat password',
        ];
    }

    


    public function register()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = new User();
        $user->U_username = $this->username;
        $user->U_role = User::ROLE_USER;
        $user->U_is_active = 1;
        $user->U_creation_date = date('Y-m-d H:i:s');
        $user->setPassword($this->password);

        return $user->save();
    }
}
