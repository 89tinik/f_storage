<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Folder $model */
/** @var array $parents */

$this->title = 'Создать папку';
foreach ($parents as $item){
    $this->params['breadcrumbs'][] = ['label' => $item->name, 'url' => ['index', 'id' => $item->id]];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="folder-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
