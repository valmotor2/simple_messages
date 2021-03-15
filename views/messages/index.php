<?php
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MessagesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Messages';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="messages-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Messages', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Create from CSV', ['csv'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Create from Groups', ['groups'], ['class' => 'btn btn-info']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            'id',
            'name',
            'destination',
            'message:ntext',
            'when_send',
            [
                'attribute' => 'status_desc',
                'filter'=>$filters
            ],
            //'creat',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{view} {delete}',],
        ],
    ]); ?>


</div>
