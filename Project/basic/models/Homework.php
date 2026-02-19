<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "Homework".
 *
 * @property int $H_ID
 * @property int $U_ID
 * @property int $S_ID
 * @property int $Teacher_U_ID
 * @property string $title
 * @property string $description
 * @property string $due_at
 * @property int $is_done
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Subject $subject
 * @property User $owner
 * @property User $teacher
 * @property User_Subject $teacherSubject
 */
class Homework extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
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
            [['U_ID', 'S_ID', 'Teacher_U_ID', 'title', 'description', 'due_at'], 'required'],
            [['U_ID', 'S_ID', 'Teacher_U_ID', 'is_done'], 'integer'],
            [['is_done'], 'default', 'value' => 0],
            [['due_at', 'created_at', 'updated_at'], 'safe'],
            [['title', 'description'], 'string', 'max' => 255],
            [['U_ID'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['U_ID' => 'U_ID']],
            [['S_ID'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::class, 'targetAttribute' => ['S_ID' => 'S_ID']],
            [['Teacher_U_ID', 'S_ID'], 'exist', 'skipOnError' => true, 'targetClass' => User_Subject::class, 'targetAttribute' => ['Teacher_U_ID' => 'U_ID', 'S_ID' => 'S_ID']],
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
            'Teacher_U_ID' => Yii::t('app', 'Teacher U ID'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'due_at' => Yii::t('app', 'Due At'),
            'is_done' => Yii::t('app', 'Is Done'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::class, ['S_ID' => 'S_ID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(User::class, ['U_ID' => 'U_ID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeacher()
    {
        return $this->hasOne(User::class, ['U_ID' => 'Teacher_U_ID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeacherSubject()
    {
        return $this->hasOne(User_Subject::class, ['U_ID' => 'Teacher_U_ID', 'S_ID' => 'S_ID']);
    }

    /**
     * {@inheritdoc}
     * @return HomeworkQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new HomeworkQuery(get_called_class());
    }
}
