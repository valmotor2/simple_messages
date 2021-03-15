<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\helpers\Utils;
use app\models\SettingsForm;
use yii\web\Controller;
use yii\filters\VerbFilter;


class SettingsController extends Controller
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
                        'actions' => ['index'],
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
        $model = new SettingsForm();
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->saveConfigurations();
            Yii::$app->session->setFlash('success', "Your settings was saved.");
            //return $this->redirect(['index']);
            //return $this->redirect(['view', 'id' => $model->id]);
        }

  
        return $this->render('index', [
            'model' => $model,
        ]);
    }

}
