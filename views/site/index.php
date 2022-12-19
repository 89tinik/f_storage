<?php

use app\models\File;
use app\models\Folder;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $folders */
/** @var array $parents */
/** @var Folder $current */

$this->title = 'My Yii Application';
?>
<div class="site-index">
    <?php if (!empty($parents)): ?>
    <div class="folder-index">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <?php
                /** @var Folder $item */
                foreach ($parents as $item):
                    ?>

                    <li class="breadcrumb-item"><?= Html::a($item->name, ['index', 'id' => $item->id]) ?></li>
                <?php endforeach ?>
                <li class="breadcrumb-item active" aria-current="page"><?= $current->name ?></li>
            </ol>
        </nav>

        <?php endif; ?>


        <h1><?= $current->name ?></h1>

        <p>
            <?= Html::a('Create Folder', ['create-folder', 'id' => $current->id], ['class' => 'btn btn-success']) ?>
            <?= Html::a('Create File', ['upload', 'folder_id' => $current->id], ['class' => 'btn btn-success']) ?>
        </p>


        <div class="row g-4 py-5 row-cols-1 row-cols-lg-6">


            <?php
            /** @var Folder $folder */
            foreach ($folders as $folder) :
                ?>
                <div class="feature col">
                    <div class="feature-icon d-inline-flex align-items-center justify-content-center text-bg-primary bg-gradient fs-2 mb-3">
                        <img src="/images/icon/folder.jpeg" alt="" width="100%"/>
                    </div>
                    <p><?= Html::a($folder->name, ['index', 'id' => $folder->id], ['class' => 'icon-link d-inline-flex align-items-center']) ?></p>

                </div>
            <?php endforeach; ?>
        </div>


        <div class="row g-4 py-5 row-cols-1 row-cols-lg-6">

            <?php
            /** @var File $files */
            foreach ($current->getFiles()->all() as $file) :
                ?>
                <div class="feature col">
                    <div class="feature-icon d-inline-flex align-items-center justify-content-center text-bg-primary bg-gradient fs-2 mb-3">
                        <img src="<?= $file->getType0()->one()->icon ?>" alt="" width="100%"/>
                    </div>
                    <p><?= Html::a($file->name, ['index', 'id' => $folder->id], ['class' => 'icon-link d-inline-flex align-items-center']) ?></p>

                </div>
            <?php endforeach; ?>
        </div>


    </div>
</div>
