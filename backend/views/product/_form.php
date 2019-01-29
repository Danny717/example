<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Product */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'amazon_link')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'target_link')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'walmart_link')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hayneedle_link')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'waifair_link')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'amazon_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'target_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'walmart_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hayneedle_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'waifair_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'update_time')->textInput() ?>

    <?= $form->field($model, 'img')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'asin')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'buybox')->textInput() ?>

    <?= $form->field($model, 'availability')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
