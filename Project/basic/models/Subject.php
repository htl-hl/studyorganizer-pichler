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
 * @property TeacherSubject[] $teacherSubjects
 * @property Teacher[] $ts
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
     * Gets query for [[Homeworks]].
     *
     * @return \yii\db\ActiveQuery|HomeworkQuery
     */
    public function getHomeworks()
    {
        return $this->hasMany(Homework::class, ['S_ID' => 'S_ID']);
    }

    /**
     * Gets query for [[TeacherSubjects]].
     *
     * @return \yii\db\ActiveQuery|TeacherSubjectQuery
     */
    public function getTeacherSubjects()
    {
        return $this->hasMany(TeacherSubject::class, ['S_ID' => 'S_ID']);
    }

    /**
     * Gets query for [[Ts]].
     *
     * @return \yii\db\ActiveQuery|TeacherQuery
     */
    public function getTs()
    {
        return $this->hasMany(Teacher::class, ['T_ID' => 'T_ID'])->viaTable('Teacher_Subject', ['S_ID' => 'S_ID']);
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
