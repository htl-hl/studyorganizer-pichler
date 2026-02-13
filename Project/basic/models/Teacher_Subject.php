<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Teacher_Subject".
 *
 * @property int $T_ID
 * @property int $S_ID
 *
 * @property Subject $s
 * @property Teacher $t
 */
class Teacher_Subject extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Teacher_Subject';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['T_ID', 'S_ID'], 'required'],
            [['T_ID', 'S_ID'], 'integer'],
            [['T_ID', 'S_ID'], 'unique', 'targetAttribute' => ['T_ID', 'S_ID']],
            [['S_ID'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::class, 'targetAttribute' => ['S_ID' => 'S_ID']],
            [['T_ID'], 'exist', 'skipOnError' => true, 'targetClass' => Teacher::class, 'targetAttribute' => ['T_ID' => 'T_ID']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'T_ID' => Yii::t('app', 'T ID'),
            'S_ID' => Yii::t('app', 'S ID'),
        ];
    }

    /**
     * Gets query for [[S]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getS()
    {
        return $this->hasOne(Subject::class, ['S_ID' => 'S_ID']);
    }

    /**
     * Gets query for [[T]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getT()
    {
        return $this->hasOne(Teacher::class, ['T_ID' => 'T_ID']);
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
