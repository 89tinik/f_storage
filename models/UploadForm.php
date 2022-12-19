<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $file;
    public $parent_id;

    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false],
            ['parent_id', 'checkOwner'],
            ['file', 'checkSize'],
        ];
    }

    /**
     * @throws Exception
     */
    public function upload()
    {
        if ($this->validate()) {
            $path = Folder::getPath($this->parent_id);
            $fileName = Yii::$app->security->generateRandomString(20);
            $filePath = $path.$fileName;
            $this->file->saveAs($filePath);
            $file = new File();
            $file->name = $this->file->baseName . '.' . $this->file->extension;
            $file->user_id = Yii::$app->user->id;
            $file->path = $filePath;
            $file->parent_id = $this->parent_id;
            $file->created = date('Y-m-d H:i:s');
            $file->size = $this->file->size;
            $file->setTypeFyle($this->file->extension);
            if($file->save()){
                Yii::$app->user->identity->storage_size = Yii::$app->user->identity->storage_size + $this->file->size;
                Yii::$app->user->identity->save();
            }
        } else {
            return false;
        }
    }

    public function checkOwner($attribute){
        if (!$this->hasErrors()) {
            $parentFolder = Folder::findOne($this->parent_id);

            if ($parentFolder->user_id != Yii::$app->user->id) {
                $this->addError($attribute, 'Вы не можете загружать файл в эту дерикторию!');
            }
        }
    }
    public function checkSize($attribute){
        if (!$this->hasErrors()) {
            if(!Yii::$app->user->identity->admin){
                if ($this->file->size > Yii::$app->user->identity->max_file_size){
                    $this->addError($attribute, 'Размер файла первышает максимально допустимый.');
                }
                if (Yii::$app->user->identity->storage_size + $this->file->size > Yii::$app->user->identity->max_storage_size){
                    $this->addError($attribute, 'Освободите место в хранилеще.');
                }
            }

        }
    }
}

