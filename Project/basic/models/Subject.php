<?php

namespace app\models;

use Yii;











class Subject extends \yii\db\ActiveRecord
{
    


    public static function tableName()
    {
        return 'Subject';
    }

    


    public function rules()
    {
        return [
            [['S_name'], 'required'],
            [['S_name'], 'trim'],
            [['S_name'], 'string', 'max' => 255],
            [['S_name'], 'unique'],
        ];
    }

    


    public function attributeLabels()
    {
        return [
            'S_ID' => Yii::t('app', 'S ID'),
            'S_name' => Yii::t('app', 'S Name'),
        ];
    }

    


    public function getHomeworks()
    {
        return $this->hasMany(Homework::class, ['S_ID' => 'S_ID']);
    }

    


    public function getUserSubjects()
    {
        return $this->hasMany(User_Subject::class, ['S_ID' => 'S_ID']);
    }

    


    public function getUsers()
    {
        return $this->hasMany(User::class, ['U_ID' => 'U_ID'])->viaTable('User_Subject', ['S_ID' => 'S_ID']);
    }

    



    public static function find()
    {
        return new SubjectQuery(get_called_class());
    }
}
