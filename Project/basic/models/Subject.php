<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Subject".
 *
 * @property int $S_ID
 * @property string $S_name
 *
 * @property Homework[] $homeworks
 * @property User_Subject[] $userSubjects
 * @property User[] $users
 */
class Subject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Subject';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['S_name'], 'required'],
            [['S_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'S_ID' => Yii::t('app', 'S ID'),
            'S_name' => Yii::t('app', 'S Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHomeworks()
    {
        return $this->hasMany(Homework::class, ['S_ID' => 'S_ID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserSubjects()
    {
        return $this->hasMany(User_Subject::class, ['S_ID' => 'S_ID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['U_ID' => 'U_ID'])->viaTable('User_Subject', ['S_ID' => 'S_ID']);
    }

    /**
     * {@inheritdoc}
     * @return SubjectQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SubjectQuery(get_called_class());
    }
}
