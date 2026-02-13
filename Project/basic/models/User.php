<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "User".
 *
 * @property int $U_ID
 * @property string $U_username
 * @property string $U_password
 * @property string $U_role
 * @property string $U_creation_date
 *
 * @property Homework[] $homeworks
 */
class User extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'User';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['U_username', 'U_password', 'U_role', 'U_creation_date'], 'required'],
            [['U_creation_date'], 'safe'],
            [['U_username', 'U_password', 'U_role'], 'string', 'max' => 255],
            [['U_username'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'U_ID' => Yii::t('app', 'U ID'),
            'U_username' => Yii::t('app', 'U Username'),
            'U_password' => Yii::t('app', 'U Password'),
            'U_role' => Yii::t('app', 'U Role'),
            'U_creation_date' => Yii::t('app', 'U Creation Date'),
        ];
    }

    /**
     * Gets query for [[Homeworks]].
     *
     * @return \yii\db\ActiveQuery|HomeworkQuery
     */
    public function getHomeworks()
    {
        return $this->hasMany(Homework::class, ['U_ID' => 'U_ID']);
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
