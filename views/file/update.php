<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\File $model */
/** @var array $parents */

$this->title = $model->name;
foreach ($parents as $item){
    $this->params['breadcrumbs'][] = ['label' => $item->name, 'url' => ['site/index', 'id' => $item->id]];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
