<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;

$this->title = 'Просмотр JSON';
$this->params['breadcrumbs'][] = $this->title;

// $provider = new ArrayDataProvider([
//     'allModels' => $data,
//     'sort' => [
//         'attributes' => ['id', 'name', 'price'],
//     ],
//     'pagination' => [
//         'pageSize' => 20,
//     ],
// ]);

// $searchModel = ['id' => null, 'name' => $namefilter];

?>
<div class="site-about">

<?= GridView::widget([
    'dataProvider' => $provider,
    'filterModel' => $filter,
    'columns' => [
        'id',
        'name',
        'description',
        'price',
        'weigth',
        'calories',
        'volume',
        'image',
    ],
]) ?>

</div>
