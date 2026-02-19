<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "User_Subject".
 *
 * @property int $U_ID
 * @property int $S_ID
 *
 * @property Subject $subject
 * @property User $user
 */
class User_Subject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'User_Subject';
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'U_ID' => Yii::t('app', 'U ID'),
            'S_ID' => Yii::t('app', 'S ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['U_ID' => 'U_ID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::class, ['S_ID' => 'S_ID']);
    }

    /**
     * {@inheritdoc}
     * @return UserSubjectQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserSubjectQuery(get_called_class());
    }
}
