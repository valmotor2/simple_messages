
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datetimepicker\DateTimePicker;
use kartik\select2\Select2;
use yii\web\JsExpression;

$this->title = 'Create Messages through Group';
$this->params['breadcrumbs'][] = ['label' => 'Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$url = \yii\helpers\Url::to(['groups/search']);
 
?>

<div class="messages-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Message', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Create from CSV', ['csv'], ['class' => 'btn btn-primary']) ?>
    </p>


    <div class="messages-form">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'group')->widget(Select2::classname(), [
        'data' => [],
        'options' => ['multiple'=>false, 'placeholder' => 'Search for a group ...'],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 3,
            'language' => [
                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
            ],
            'ajax' => [
                'url' => $url,
                'dataType' => 'json',
                'data' => new JsExpression('function(params) { return {q:params.term}; }')
            ],
            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            'templateResult' => new JsExpression('function(group) { return group.text; }'),
            'templateSelection' => new JsExpression('function (group) { return group.text; }'),
        ],
    ]);
    ?>
 
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
