<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

use app\models\Messages;
use app\models\ServiceForm;
date_default_timezone_set('Europe/Bucharest');

class MessagesController extends Controller
{
    /**
     * This will check each minutes to send messages
     */
    public function actionSendMessages() {
        $limit = Yii::$app->params['limit_service_sms_send_per_minute'];
        $date = (new \DateTime())->format('Y-m-d H:i:s');
        $all = Messages::find()
            ->where(['status_desc' => Messages::STATUS_WAITING])
            ->andWhere(['<', 'when_send', $date])
            ->limit($limit)
            ->all();

        $service = new ServiceForm();

        foreach($all as $each):
            $each->status_desc = Messages::STATUS_SENDING;
            
            $each->validate() && $each->save();

            $response = $service->sendEach($each);

            if(!$response) {
                $each->status_desc = Messages::STATUS_ERROR;
                $each->validate() && $each->save();
            }

        endforeach;
        
        return ExitCode::OK;
    }


    /**
     * This will be at each day at 08:00 , 
     * because it's not necessary to checking, 
     * `cuz we receive always a new status  when it is changed through actionStatus
     */
    public function actionUpdateStatusOfMessages() {

        $all = Messages::find()
            ->where(['status_desc' => Messages::STATUS_SENDING])
            ->all();

        $service = new ServiceForm();

        foreach($all as $message):
            $status = $service->checkStatus($message);
            $message->status_desc = $status;

            $message->validate() && $message->save();
        endforeach;

        echo "Update status of messages\n";
        return ExitCode::OK;
    }
}
