<?php

namespace app\controllers;

use Yii;
use app\helpers\Utils;
use yii\filters\AccessControl;
use app\models\Messages;
use app\models\MessageForm;
use app\models\MessageFormCsv;
use app\models\MessageFormGroup;
use app\models\MessagesSearch;
use app\models\Groups;
use app\models\Settings;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\db\Expression;

/**
 * MessagesController implements the CRUD actions for Messages model.
 */
class MessagesController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['api', 'receive', 'status', 'r'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index', 'create', 'csv', 'groups', 'view', 'update', 'delete', 'settings'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],

                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Messages models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MessagesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'filters' => [
                Messages::STATUS_WAITING,
                Messages::STATUS_SENDING,
                Messages::STATUS_SENT,
                Messages::STATUS_CONFIRMED,
                Messages::STATUS_ERROR,
                Messages::STATUS_RECEIVED,
                
            ]
        ]);
    }

    public function actionSettings() {
        return $this->render('settings');
    }

    /**
     * Displays a single Messages model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Messages model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MessageForm();

        if ($model->load(Yii::$app->request->post())) {

            $model->save();
            Yii::$app->session->setFlash('success', "Your message was created.");
            return $this->redirect(['index']);
            //return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionCsv()
    {
        $model = new MessageFormCsv();

        if ($model->load(Yii::$app->request->post()) ) {

            $model->file = UploadedFile::getInstance($model, 'file');

            $file = $model->file->tempName;


            $handle = fopen($file, 'r');
            $skipFirstRow = true;

            $insertLimitBatch = 5000;
            $insertBatch = [];
            $counter = 0;
            while(( $fileop = fgetcsv($handle, 1000, ",")) !== false)
            {
                if($skipFirstRow) { $skipFirstRow = false; continue; }
                
                //'destination', 'name', 'message', 'when_send', 'status_desc'
                $split = explode(':', $fileop[0]);

                $destination = $split[0];
                $name = empty($split[1]) ? '' : $split[1];
                $insert = [
                    Utils::filter_destination($destination), 
                    $name,
                    $fileop[1],
                    $model->when_send,
                    Messages::STATUS_WAITING,
                ];
                $insertBatch[] = $insert;
                $counter += 1;

                if($counter > $insertLimitBatch) {
                    // insert batch... 
                    $model->insertBatch($insertBatch);
                    $counter = 0;
                    $insertBatch = [];
                }
            }

            if($counter > 0) {
                $model->insertBatch($insertBatch);
            }

            Yii::$app->session->setFlash('success', "The content of your csv was added to messages.");
            return $this->redirect(['index']);
            // return $this->redirect(['view', 'id' => $model->id]);
        }


        return $this->render('csv', [
            'model' => $model,
        ]);
    }

    public function actionGroups()
    {
        $model = new MessageFormGroup();

        if ($model->load(Yii::$app->request->post()) ) {

            $all = Groups::find()->where(['group_name' => $model->group])->all();

            $count = 0;
            $limitInsertBatch = 5000;
            $insertBatch = [];
            foreach($all as $each):

                $insert = [
                    Utils::filter_destination($each->destination),
                    $each->full_name,
                    $model->message,
                    $model->when_send,
                    Messages::STATUS_WAITING
                ];
                $insertBatch[] = $insert;

                $count += 1;


                if($count > $limitInsertBatch) {
                    $model->insertBatch($insertBatch);
                    $count = 0;
                    $insertBatch = [];
                }
            endforeach;

            if($count > 0) {
                $model->insertBatch($insertBatch);
            }

            Yii::$app->session->setFlash('success', "The messages to group / groups was created!");
            return $this->redirect(['index']);
        }


        return $this->render('groups', [
            'model' => $model,
        ]);
    }
    /**
     * Updates an existing Messages model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Messages model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        Yii::$app->session->setFlash('success', "The message was deleted.");

        return $this->redirect(['index']);
    }


    /**
     * Receive through URL an command to send a message
     */
    public function actionApi() {
     
        $get = Yii::$app->request->get();
        $post = Yii::$app->request->post();
        if(!empty($post)) {
            $get = $post;
        }
        
        $options = Settings::optionsMessages();

        if(empty($options->api->tokens)) 
        {
           return false; 
        }
     
        if(empty($get['token']) || empty($get['destination']) || empty($get['message']))
        {
            throw new NotFoundHttpException('The requested is not complete.');
        }


        $hasAccess = false;
        foreach($options->api->tokens as $token):
            if($get['token'] === $token):
                $hasAccess = true;
            endif;
        endforeach;

        if(!$hasAccess) {
            throw new ForbiddenHttpException('Token is not valid.');
        }


        $message = new Messages();

        $message->destination = Utils::filter_destination($get['destination']);
        $message->message = $get['message'];
        $message->status_desc = Messages::STATUS_WAITING;
        $message->when_send = new Expression('NOW()');

        $found = Groups::find()->where(['destination' => $message->destination])->one();
        if(!empty($found)) 
        {
            $message->name = $found->full_name;
        }

        if($message->validate() && $message->save()) {
            echo $message->status_desc;
        } else {
            throw new NotFoundHttpException('The requested is not complete.');
        }

    }

    /**
     * Change status for an message
     */
    public function actionStatus()
    {
        $get = Yii::$app->request->get();

        if(empty($get['message_id']) || empty($get['type'])) {
            throw new NotFoundHttpException('The requested is not complete.');
        }

        $message_id = (int) $get['message_id'];
        $type = (int) $get['type'];
        $now = (new \DateTime())->format('d.m.Y H:i:s');

        $message = Messages::find($message_id)->one();

        if(empty($message)) {
            throw new NotFoundHttpException('The requested is not found.');
        }

        switch($type) {
            case 8:
                $message->status_desc = Messages::STATUS_SENT;
                break;
            case 1:
                $message->status_desc = Messages::STATUS_CONFIRMED;
                break;
            case 31:
            case 4:
                $message->status_desc = Messages::STATUS_SENDING;
                break;
            case 2:
            case 16:
                $message->status_desc = Messages::STATUS_ERROR;
                break;
            default:
                $message->status_desc = Messages::STATUS_UNKNOWN;
        }   

        $message->validate() && $message->save();
        echo $message->status_desc;
    }
    
    /**
     * Receive a new message from service
     */
    public function actionReceive()
    {
        $get = Yii::$app->request->get();

        if(!empty($get['phone']) || !empty($get['text'])) 
        {
            $message = new Messages();

            $message->destination = Utils::filter_destination($get['phone']);
            $message->message = $get['text'];
            $message->status_desc = Messages::STATUS_RECEIVED;
            $message->when_send = new Expression('NOW()');

            $found = Groups::find()->where(['destination' => $message->destination])->one();
            if(!empty($found)) 
            {
                $message->name = $found->full_name;
            }

            if($message->validate() && $message->save()) {
                $this->forwardReceivedMessageThroughEmail($message);
                $this->forwardReceivedMessageThroughUrl($message);
                echo $message->status_desc;
            } else {
                throw new NotFoundHttpException('The requested is not complete.');
            }
        }

    }

    private function forwardReceivedMessageThroughEmail(Message $message)
    {
        $options = Settings::optionsMessages();
        if(empty($options->api->send_messages_received_to_mail)) {
           return false; 
        }

        foreach($options->api->send_messages_received_to_mail as $mail):
            Yii::$app->mailer->compose()
            ->setTo($mail)
            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
            ->setSubject('Ati primit un mesaj de la ' . $message->destination)
            ->setTextBody(json_encode($message->attributes))
            ->send();
        endforeach;
        

        return true;
    }

    /**
     * It's called by actionReceive
     * If someone want to forward received message from this service to their service
     */
    private function forwardReceivedMessageThroughUrl(Messages $message)
    {
        $options = Settings::optionsMessages();

        if(empty($options->api->forward_messages_received_to_url)) {
           return false; 
        }

        foreach($options->api->forward_messages_received_to_url as $url):
            $post = ['from' => $message->destination, 'message' =>  $message->message];

            try { 
                $ch = curl_init( $url );
                curl_setopt( $ch, CURLOPT_POST, 1);
                curl_setopt( $ch, CURLOPT_POSTFIELDS, $post);
                curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt( $ch, CURLOPT_HEADER, 0);
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
                curl_exec( $ch );
            } catch (Exception $e) {}

        endforeach;
    }

    /**
     * Finds the Messages model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Messages the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Messages::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
