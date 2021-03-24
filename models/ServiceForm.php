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


    public function isBulk(Boolean $status) 
    {
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


    public function prepareAndSendToService(Messages $message) 
    {

        //$destination = str_replace('+', '', $message->destination);
        $destination = $message->destination;
        $prepare_message = [
            'sql_id' => 0,
            'momt' => 'MT',
            'sender' => 'app',
            'receiver' => $destination,
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


    public function sendEach(Messages $message) 
    {
        $response = $this->prepareAndSendToService($message);

        if($response) {
            return $this->sendToService($response);
        }

        return false;

    }

    public function sendToService($messages) 
    {

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


    public function checkStatus(Messages $message)
    {

        // search in sms_sent table after status dlr
        // check DLR_MASK 
        // if the DLR NOT EXIST AND is older than 72 ore 
        // return Messages:STATUS_UNKNONW;
        $search_by = 'http://127.0.0.1/index.php?r=messages%2Fstatus&message_id='.$message->id.'&type=%d';

        $sql = '
            SELECT momt, dlr_mask 
            FROM sent_sms 
            WHERE dlr_url = "'.$search_by.'" 
                AND momt = "DLR" 
            ORDER BY time DESC
            LIMIT 1
            ';
        $results = Yii::$app->db->createCommand($sql)->queryAll();

  
        // error, we can not have nothing?!
        // check later
        if(empty($results)):
            return Messages::STATUS_SENDING;
        endif;

        foreach($results as $result):

            return ServiceForm::getStatusByService($result['dlr_mask']);
            // @TODO    
        endforeach;

        die;

        return Messages::STATUS_SENDING;
    }

    static function getStatusByService($type)
    {
        $status_desc = Messages::STATUS_UNKNOWN;
        switch($type) {
            case 8:
                $status_desc = Messages::STATUS_SENT;
                break;
            case 1:
                $status_desc = Messages::STATUS_CONFIRMED;
                break;
            case 31:
            case 4:
                $status_desc = Messages::STATUS_SENDING;
                break;
            case 2:
            case 16:
                $status_desc = Messages::STATUS_ERROR;
                break;
            default:
                $status_desc = Messages::STATUS_UNKNOWN;
        }   

        return $status_desc;
    }
    
}
