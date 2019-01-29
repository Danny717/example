<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProductSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'amazon_link') ?>

    <?= $form->field($model, 'target_link') ?>

    <?= $form->field($model, 'walmart_link') ?>

    <?php // echo $form->field($model, 'hayneedle_link') ?>

    <?php // echo $form->field($model, 'waifair_link') ?>

    <?php // echo $form->field($model, 'amazon_price') ?>

    <?php // echo $form->field($model, 'target_price') ?>

    <?php // echo $form->field($model, 'walmart_price') ?>

    <?php // echo $form->field($model, 'hayneedle_price') ?>

    <?php // echo $form->field($model, 'waifair_price') ?>

    <?php // echo $form->field($model, 'update_time') ?>

    <?php // echo $form->field($model, 'img') ?>

    <?php // echo $form->field($model, 'asin') ?>

    <?php // echo $form->field($model, 'buybox') ?>

    <?php // echo $form->field($model, 'availability') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
