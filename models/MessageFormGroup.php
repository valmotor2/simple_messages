<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadFile;


class MessageFormGroup extends Model
{
    public $when_send;
    public $message;
    public $group;
    
    /**
     * @return array the validation rules.
     */
    public function rules()
    { 
        return [
            // name, email, subject and body are required
            [['group', 'when_send', 'message'], 'required'],

        ];
    }

    /**
     * @return array customized attribute labels
     */ 
    public function attributeLabels()
    {
        return [
            'group' => 'Group',
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
 