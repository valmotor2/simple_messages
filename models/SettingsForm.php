<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Settings;
use app\helpers\Utils;
use yii\filters\VerbFilter;

class SettingsForm extends Model
{
    public $send_messages_received_to_mail;
    public $api_token;
    public $api_forward_messages_received_to_url;
    
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['send_messages_received_to_mail', 'api_token', 'api_forward_messages_received_to_url'], 'safe'],
            ['send_messages_received_to_mail', 'email'],
            ['api_forward_messages_received_to_url', 'url'],
            [['api_token'], 'string','length'=>[40, 120]],
            [['api_token'], 'noSpaces'],

        ];
    }



    public function noSpaces($attribute, $params, $validator)
    {
        
        
        if (!empty($this->$attribute) &&  strpos($this->$attribute, ' ') > -1) {

            $this->addError($attribute, 'Spaces aren\'t allowed!');
            return false;
        } 

        return true;
    }
    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'send_messages_received_to_mail' => 'Send Messages Received To Mail',
            'api_token' => 'Api Token',
            'api_forward_messages_received_to_url' => 'Api forward Messages Received To Url'
        ];
    }


    public function init() {
        $messages = Settings::find()->where(['desc' => 'messages'])->one();

        if(empty($messages)) {
            throw new NotFoundHttpException('Record of messages in settings does not exist.');
        }

        $options = json_decode($messages->options);

        // this will be simple, on future we don't now what will be

        if(!empty($options->send_messages_received_to_mail)) {
            $this->send_messages_received_to_mail = $options->send_messages_received_to_mail[0];
        }

        if(!empty($options->api->tokens)) {
            $this->api_token = $options->api->tokens[0];
        }

        if(!empty($options->api->forward_messages_received_to_url)) {
            $this->send_messages_received_to_mail = $options->api->forward_messages_received_to_url[0];
        }

        return true;
    }

    public function saveConfigurations() {

        $messages = Settings::find()->where(['desc' => 'messages'])->one();

        if(empty($messages)) {
            throw new NotFoundHttpException('Record of messages in settings does not exist.');
        }

        $options = json_decode($messages->options);

    
        if(!empty($this->send_messages_received_to_mail)) {
            $options->send_messages_received_to_mail = [];
            $options->send_messages_received_to_mail[] = $this->send_messages_received_to_mail;
        } else {
            $options->send_messages_received_to_mail = [];
        }

        if(!empty($this->api_token)) {
            $options->api->tokens = [];
            $options->api->tokens[] = $this->api_token;
        } else {
            $options->api->tokens = [];
        }

        if(!empty($this->api_forward_messages_received_to_url)) {
            $options->api->api_forward_messages_received_to_url = [];
            $options->api->api_forward_messages_received_to_url[] = $this->api_forward_messages_received_to_url;
        } else {
            $options->api->api_forward_messages_received_to_url = [];
        }


        $messages->options = json_encode($options);

        return $messages->validate() && $messages->save();

    }
}
