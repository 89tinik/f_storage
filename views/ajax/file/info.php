<?php

use app\models\File;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var File $file */
?>
<div class="row">
    <div class="feature col file-small file-small-style">
        <h3>Информация</h3>
        <p>Имя: <b><?= $file->name ?></b></p>
        <p>Размер: <b><?= $file->formatFileSize() ?></b></p>
        <p>Загружен: <b><?= $file->created ?></b></p>
        <p>Доступ:
            <b><?= ($file->public_link) ? Html::a(Url::toRoute(['file/view', 'id' => $file->id], true), ['file/view', 'id' => $file->id]) : 'Нет' ?></b>
        </p>
        <div class="feature-icon d-inline-flex align-items-center justify-content-center bg-gradient fs-2 mb-3">
            <img src="<?= $file->thumb ?>" alt="" width="100%">
        </div>
        <?= Html::a('Скачать', ['file/download', 'id' => $file->id], ['class' => 'btn btn-info']) ?>
        <?= Html::button(($file->public_link) ? 'Скрыть' : 'Поделиться', ['class' => 'btn btn-success change-access','file-id' => $file->id]) ?>
        <?= Html::a('Переименовать', ['file/update', 'id' => $file->id], ['class' => 'btn btn-dark']) ?>
        <?= Html::a(
            'Удалить',
            ['file/delete', 'id' => $file->id],
            ['class' => 'btn btn-danger', 'data-confirm' => 'Вы уверены?', 'data-method' => 'post']
        ) ?>
    </div>
</div>
