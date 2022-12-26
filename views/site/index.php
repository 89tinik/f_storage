<?php

use app\models\File;
use app\models\Folder;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $folders */
/** @var array $parents */
/** @var Folder $current */

$this->title = 'My Yii Application';
if (!empty($parents)) {
    foreach ($parents as $item) {
        $this->params['breadcrumbs'][] = ['label' => $item->name, 'url' => ['index', 'id' => $item->id]];
    }
}
if (!empty($this->params['breadcrumbs'])) {
    $this->params['breadcrumbs'][] = $current->name;
}
?>
<div class="site-index">
    <h1><?= $current->name ?></h1>

    <p>
        <?= Html::a('Создать папку', ['create-folder', 'id' => $current->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Загрузить файл', ['file/upload', 'folder_id' => $current->id], ['class' => 'btn btn-success']) ?>
    </p>


    <div class="row g-4 py-5 row-cols-1 row-cols-lg-6">


        <?php
        /** @var Folder $folder */
        foreach ($folders as $folder) :
            ?>
            <div class="feature col">
                <div class="feature-icon d-inline-flex align-items-center justify-content-center text-bg-primary bg-gradient fs-2 mb-3 folder">
                    <!--                        <div class="menu-folder"></div>-->
                    <img src="/images/icon/folder.jpeg" alt="" width="100%"/>
                </div>
                <p><?= Html::a($folder->name, ['index', 'id' => $folder->id], ['class' => 'icon-link d-inline-flex align-items-center']) ?></p>

            </div>
        <?php endforeach; ?>
    </div>

    <?php
    $files = $current->getFiles()->all();
    if (!empty($files)):
        ?>
        <h2>Файлы:</h2>
        <div class="row g-4 py-5 row-cols-1 row-cols-lg-6">

            <div class="col-lg-8">
                <div class="row g-4 py-5 row-cols-2 row-cols-lg-4">
                    <?php
                    /** @var File $file */
                    foreach ($current->getFiles()->all() as $file) :
                        ?>
                        <div class="feature col file-small file-small-style" data-file="<?= $file->id ?>">
                            <div class="feature-icon d-inline-flex align-items-center justify-content-center bg-gradient fs-2 mb-3">
                                <img src="<?= $file->thumb ?>" alt="" width="100%"/>
                            </div>
                            <p><?= $file->name ?></p>

                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-lg-4 file-info-block">

            </div>
        </div>
    <?php endif; ?>

</div>
</div>
