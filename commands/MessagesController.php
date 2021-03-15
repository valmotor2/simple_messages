<?php

namespace app\commands;

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

        
        $date = (new \DateTime())->format('Y-m-d H:i:s');
        $all = Messages::find()
            ->where(['status_desc' => Messages::STATUS_WAITING])
            ->andWhere(['<', 'when_send', $date])
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
     * This will be at 15 minutes / update
     */
    public function actionUpdateStatusOfMessages() {
        echo "Update status of messages\n";
        return ExitCode::OK;
    }
}
