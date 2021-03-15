<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadFile;


class MessageFormCsv extends Model
{
    public $when_send;
    public $file;
    
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['when_send'], 'required'],
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
            'file' => 'File',
            'when_send' => 'When send'
        ];
    }

    public function insertBatch($batch) {
        return Yii::$app->db->createCommand()->batchInsert(
            'messages', 
            ['destination', 'name', 'message', 'when_send', 'status_desc'],
            $batch
        )->execute();
    }
}
 