<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Homework".
 *
 * @property int $H_ID
 * @property int $U_ID
 * @property int $S_ID
 * @property int $T_ID
 * @property string $title
 * @property string $description
 * @property string $due_at
 * @property int $is_done
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Subject $s
 * @property Teacher $t
 * @property User $u
 */
class Homework extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Homework';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['U_ID', 'S_ID', 'T_ID', 'title', 'description', 'due_at', 'is_done', 'created_at', 'updated_at'], 'required'],
            [['U_ID', 'S_ID', 'T_ID', 'is_done'], 'integer'],
            [['due_at', 'created_at', 'updated_at'], 'safe'],
            [['title', 'description'], 'string', 'max' => 255],
            [['S_ID'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::class, 'targetAttribute' => ['S_ID' => 'S_ID']],
            [['T_ID'], 'exist', 'skipOnError' => true, 'targetClass' => Teacher::class, 'targetAttribute' => ['T_ID' => 'T_ID']],
            [['U_ID'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['U_ID' => 'U_ID']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'H_ID' => Yii::t('app', 'H ID'),
            'U_ID' => Yii::t('app', 'U ID'),
            'S_ID' => Yii::t('app', 'S ID'),
            'T_ID' => Yii::t('app', 'T ID'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'due_at' => Yii::t('app', 'Due At'),
            'is_done' => Yii::t('app', 'Is Done'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
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
     * Gets query for [[U]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getU()
    {
        return $this->hasOne(User::class, ['U_ID' => 'U_ID']);
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
