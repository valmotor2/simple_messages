<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datetimepicker\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Messages */

$this->title = 'Create Messages through upload file CSV';
$this->params['breadcrumbs'][] = ['label' => 'Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="messages-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Message', ['create'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Create from Groups', ['groups'], ['class' => 'btn btn-info']) ?>
    </p>


    <div class="groups-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?=$form->field($model, 'when_send')->widget(DateTimePicker::className(), [
        'model' => $model,
        'attribute' => 'when_send',
        'language' => 'ro',
        'size' => 'ms',
        'clientOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd HH:ii:ss',
            'todayBtn' => true
        ]
    ])?>


    <?= $form->field($model, 'file')->fileInput() ?>
    <?= Html::encode("In the file of CSV, first line is skipped, the rest must have this order: destination:?name,message")?>
    <br />
    <small>?name is not required, it's the name of destination, example: 0722123...:Valentin Gonganau</small>
    <hr />

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    </div>

</div>
