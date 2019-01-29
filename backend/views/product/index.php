<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Product', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'title',
            //'amazon_link',
            //'target_link',
            //'walmart_link',
            //'hayneedle_link',
            //'waifair_link',
            'amazon_price',
            'target_price',
            'walmart_price',
            //'hayneedle_price',
            //'waifair_price',
            //'update_time',
            //'img',
            //'asin',
            //'buybox',
            //'availability',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
