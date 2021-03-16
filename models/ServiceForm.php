<?php

namespace app\models;

use Yii;


use app\models\Messages;
use app\helpers\Utils;
use yii\helpers\Url;

class ServiceForm
{
    private $isBulk = false;
    private $message = [];


    public function isBulk(Boolean $status) {
        $this->isBulk = $statsu;
    }

    # daca se seteaza url, cand se confirma dlr-ul mesajului ca a fost trimis cu succes pe langa faptu ca in aceast
    # tabel se adauga automat un dlr, se mai trimite se mai trimite si confirmarea prin link.
    # este necesar aceasta functie pentru schimbarea statusului a mesajului
    # id_last_inserted este unic
    public static function set_url(Messages $message)
    {
        return Yii::$app->params['url_host'].'/index.php?r=messages%2Fstatus&message_id='.$message->id.'&type=%d';
    }


    public function prepareAndSendToService(Messages $message) {

        $prepare_message = [
            'sql_id' => 0,
            'momt' => 'MT',
            'sender' => 'app',
            'receiver' => $message->destination,
            'smsc_id' => '',
            'msgdata' => urlencode(mb_convert_encoding($message->message, 'UTF-8', 'UTF-8')),
            'dlr_url' => $this->set_url($message),
            'sms_type' => 2,
            'dlr_mask' => 31,
            'coding' => 0, // 0 - 7bit, 1 - 8 bit , 2 - UNICODE,
            'time' => time(),
            'foreign_id' => $message->id,
        ];
     
        if (! trim( $prepare_message['msgdata'] )) {
            return null; // send error
        }

        return $prepare_message;
    }


    public function sendEach(Messages $message) {
        $response = $this->prepareAndSendToService($message);

        if($response) {
            return $this->sendToService($response);
        }

        return false;

    }

    public function sendToService($messages) {

        $header = [];

        if($this->isBulk) {
            foreach($mesages[0] as $key => $value):
                $header[] = $key;
            endforeach;
        } else {
            foreach($messages as $key => $value):
                $header[] = $key;
            endforeach;
        }
        
        

        return Yii::$app->db->createCommand()->batchInsert(
            'send_sms', 
            $header,
            $this->isBulk ? $messages : [ $messages ]
        )->execute();

    }


    
}
