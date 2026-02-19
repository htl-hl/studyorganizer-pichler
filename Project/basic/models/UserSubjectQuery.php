<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[User_Subject]].
 *
 * @see User_Subject
 */
class UserSubjectQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return User_Subject[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return User_Subject|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
