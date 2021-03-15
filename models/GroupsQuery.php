<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Groups]].
 *
 * @see Groups
 */
class GroupsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Groups[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Groups|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
