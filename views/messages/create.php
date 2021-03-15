<?php

use yii\helpers\Html;

use yii\widgets\ActiveForm;
use dosamigos\datetimepicker\DateTimePicker;
/* @var $this yii\web\View */
/* @var $model app\models\Messages */

$this->title = 'Create Messages';
$this->params['breadcrumbs'][] = ['label' => 'Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="messages-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create from CSV', ['csv'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Create from Groups', ['groups'], ['class' => 'btn btn-info']) ?>
    </p>


    <div class="messages-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'destination')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'message')->textarea(['rows' => 6]) ?>

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


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    </div>


</div>
