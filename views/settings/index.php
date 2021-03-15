<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Settings';
$this->params['breadcrumbs'][] = ['label' => $this->title];

\yii\web\YiiAsset::register($this);
?>
<div class="messages-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'send_messages_received_to_mail')->textInput(['maxlength' => true]) ?>
    <small>If the system will receive an message, it will send an email to this address setted in this field.</small>
    <br /><br />
    <?= $form->field($model, 'api_token')->textInput(['maxlength' => true]) ?>
    <small>Token for API, will receive through POST with next params: @destination: string, @message: string and @token: string ( This token setted in this field)</small>
    <br />
    <small>If the token is not generated or is not the same as in this field, the API will not working.</small>
    <br />
    <small>URL link for  API is: <strong><?=Url::toRoute(['messages/api'], true);?></strong>, and is used for send messages.</small>
    <br /><br />

    <?= $form->field($model, 'api_forward_messages_received_to_url')->textInput(['maxlength' => true]) ?>
    <small>If the system will receive an message, it will forward message to next url , if it is setted.</small>
    <br />
    <small>It will send through POST with next params as: @from: string and @message: string</small>
    <br />   <br />
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
