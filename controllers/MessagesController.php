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
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

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
                Messages::SENDING,
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



    public function actionChangeStatusFromService()
    {
        Utils::debug(Yii::$app->request->get(), 1);
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