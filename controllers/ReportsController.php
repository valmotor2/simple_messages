<?php

namespace app\controllers;

use Yii;

use yii\web\Controller;
use yii\filters\AccessControl;
use app\helpers\Utils;
use app\models\ServiceKannel;

class ReportsController extends Controller
{
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

        ];
    }

    public function actionIndex()
    {

        $stats = new ServiceKannel();
        return $this->render('index', ['stats' => $stats]);
    }
}