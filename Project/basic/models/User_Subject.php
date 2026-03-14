<?php

namespace app\models;

use Yii;










class User_Subject extends \yii\db\ActiveRecord
{
    


    public static function tableName()
    {
        return 'User_Subject';
    }

    


    public function rules()
    {
        return [
            [['U_ID', 'S_ID'], 'required'],
            [['U_ID', 'S_ID'], 'integer'],
            [['U_ID', 'S_ID'], 'unique', 'targetAttribute' => ['U_ID', 'S_ID']],
            [['U_ID'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['U_ID' => 'U_ID']],
            [['S_ID'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::class, 'targetAttribute' => ['S_ID' => 'S_ID']],
        ];
    }

    


    public function attributeLabels()
    {
        return [
            'U_ID' => Yii::t('app', 'U ID'),
            'S_ID' => Yii::t('app', 'S ID'),
        ];
    }

    


    public function getUser()
    {
        return $this->hasOne(User::class, ['U_ID' => 'U_ID']);
    }

    


    public function getSubject()
    {
        return $this->hasOne(Subject::class, ['S_ID' => 'S_ID']);
    }

    



    public static function find()
    {
        return new UserSubjectQuery(get_called_class());
    }
}
