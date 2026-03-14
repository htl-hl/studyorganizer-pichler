<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;




















class Homework extends \yii\db\ActiveRecord
{
    


    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => static function () {
                    return date('Y-m-d H:i:s');
                },
            ],
        ];
    }

    


    public static function tableName()
    {
        return 'Homework';
    }

    


    public function rules()
    {
        return [
            [['U_ID', 'S_ID', 'Teacher_U_ID', 'title', 'description', 'due_at'], 'required'],
            [['U_ID', 'S_ID', 'Teacher_U_ID', 'is_done'], 'integer'],
            [['is_done'], 'default', 'value' => 0],
            [['due_at', 'created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['description'], 'string'],
            [['U_ID'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['U_ID' => 'U_ID']],
            [['S_ID'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::class, 'targetAttribute' => ['S_ID' => 'S_ID']],
            [['Teacher_U_ID', 'S_ID'], 'exist', 'skipOnError' => true, 'targetClass' => User_Subject::class, 'targetAttribute' => ['Teacher_U_ID' => 'U_ID', 'S_ID' => 'S_ID']],
        ];
    }

    


    public function attributeLabels()
    {
        return [
            'H_ID' => Yii::t('app', 'H ID'),
            'U_ID' => Yii::t('app', 'U ID'),
            'S_ID' => Yii::t('app', 'S ID'),
            'Teacher_U_ID' => Yii::t('app', 'Teacher U ID'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'due_at' => Yii::t('app', 'Due At'),
            'is_done' => Yii::t('app', 'Is Done'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    


    public function getSubject()
    {
        return $this->hasOne(Subject::class, ['S_ID' => 'S_ID']);
    }

    


    public function getOwner()
    {
        return $this->hasOne(User::class, ['U_ID' => 'U_ID']);
    }

    


    public function getTeacher()
    {
        return $this->hasOne(User::class, ['U_ID' => 'Teacher_U_ID']);
    }

    


    public function getTeacherSubject()
    {
        return $this->hasOne(User_Subject::class, ['U_ID' => 'Teacher_U_ID', 'S_ID' => 'S_ID']);
    }

    



    public static function find()
    {
        return new HomeworkQuery(get_called_class());
    }
}
