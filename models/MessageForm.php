<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Messages;
use app\models\Groups;
use app\helpers\Utils;

class MessageForm extends Model
{
    public $destination;
    public $message;
    public $when_send;
    
    
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['destination', 'message', 'when_send'], 'required']
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'destination' => 'Destination',
            'message' => 'Message',
            'when_send' => 'When Send'
        ];
    }

    public function save() {
        $record = new Messages();
        $record->destination = Utils::filter_destination($this->destination);
        $record->message = $this->message;
        $record->when_send = $this->when_send;
        $record->status_desc = Messages::STATUS_WAITING;

        $record->name = '';


        $found = Groups::findOne(['destination' => $record->destination]);

        if($found && $found->full_name) {
            $record->name = $found->full_name;
        }


        return $record->validate() && $record->save();
    }


 
}
