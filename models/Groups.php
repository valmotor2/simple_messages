<?php

namespace app\models;

use Yii;
use app\helpers\Utils;
/**
 * This is the model class for table "groups".
 *
 * @property int $id
 * @property string $group_name
 * @property string|null $nume_prenume
 * @property string $destination
 * @property string|null $creat
 */
class Groups extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'groups';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['group_name', 'destination'], 'required'],
            [['creat'], 'safe'],
            [['group_name', 'full_name', 'destination'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'group_name' => 'Group Name',
            'full_name' => 'Full Name',
            'destination' => 'Destination',
            'creat' => 'Creat',
        ];
    }

    /**
     * {@inheritdoc}
     * @return GroupsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new GroupsQuery(get_called_class());
    }

    public function beforeSave($insert) {


        if($insert) {
            $this->destination = Utils::filter_destination($this->destination);
        }

        return parent::beforeSave($insert);
    }


}
