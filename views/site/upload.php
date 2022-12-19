<?php
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\UploadForm $model */
?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

<?= $form->field($model, 'file')->fileInput() ?>
<?= $form->field($model, 'parent_id')->hiddenInput()->label(false);  ?>

    <button>Submit</button>

<?php ActiveForm::end() ?>