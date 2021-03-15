<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadFile;


class GroupForm extends Model
{
    public $name;
    public $file;
    
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['name'], 'required'],
            [['file'], 'file', 
            'skipOnEmpty' => false,
            'extensions' => 'csv' ,
            'checkExtensionByMimeType' => false
            ]
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Name',
        ];
    }

    public function insertBatch($batch) {
        return Yii::$app->db->createCommand()->batchInsert(
            'groups', 
            ['group_name', 'full_name', 'destination'],
            $batch
        )->execute();
    }
}
