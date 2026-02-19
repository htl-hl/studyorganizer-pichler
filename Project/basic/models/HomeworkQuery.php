<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Homework]].
 *
 * @see Homework
 */
class HomeworkQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return Homework[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Homework|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
