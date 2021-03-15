<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\helpers\Utils;
use app\models\GroupForm;
use app\models\Groups;
use app\models\GroupsSearch;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;


/**
 * GroupsController implements the CRUD actions for Groups model.
 */
class GroupsController extends Controller
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
                        'actions' => ['index', 'create', 'view', 'delete', 'update', 'search'],
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
     * Lists all Groups models.
     * @return mixed
     */ 
    public function actionIndex()
    {
        $searchModel = new GroupsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Groups model.
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
     * Creates a new Groups model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GroupForm();

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
                
                $insert = [
                    $model->name, // name of group
                    $fileop[0] .' '. $fileop[1], // first and last name
                    Utils::filter_destination($fileop[2]), // append prefix to destination ( phone )
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

            Yii::$app->session->setFlash('success', "Your contacts to this group was created.");
            return $this->redirect(['index']);
            // return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    /**
     * Updates an existing Groups model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Your contacts was updated.");
            return $this->redirect(['view', 'id' => $model->id]);
            //return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Groups model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        Yii::$app->session->setFlash('success', "Your contacts was deleted.");

        return $this->redirect(['index']);
    }


    /**
     * User for searching on box
     */
    public function actionSearch($q = null, $id = null) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];

        if (!is_null($q)) {
            $query = new Query;
            $query->select('group_name AS id, group_name AS text')
                ->from('groups')
                ->where(['like', 'group_name', $q])
                ->groupBy('group_name')
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => City::find($id)->name];
        }
        return $out;
    }
    /**
     * Finds the Groups model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Groups the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Groups::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
