<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Teacher".
 *
 * @property int $T_ID
 * @property string $firstname
 *
 * @property Homework[] $homeworks
 * @property Subject[] $ss
 * @property TeacherSubject[] $teacherSubjects
 */
class Teacher extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Teacher';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['firstname'], 'required'],
            [['firstname'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'T_ID' => Yii::t('app', 'T ID'),
            'firstname' => Yii::t('app', 'Firstname'),
        ];
    }

    /**
     * Gets query for [[Homeworks]].
     *
     * @return \yii\db\ActiveQuery|HomeworkQuery
     */
    public function getHomeworks()
    {
        return $this->hasMany(Homework::class, ['T_ID' => 'T_ID']);
    }

    /**
     * Gets query for [[Ss]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getSs()
    {
        return $this->hasMany(Subject::class, ['S_ID' => 'S_ID'])->viaTable('Teacher_Subject', ['T_ID' => 'T_ID']);
    }

    /**
     * Gets query for [[TeacherSubjects]].
     *
     * @return \yii\db\ActiveQuery|TeacherSubjectQuery
     */
    public function getTeacherSubjects()
    {
        return $this->hasMany(TeacherSubject::class, ['T_ID' => 'T_ID']);
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
