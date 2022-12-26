<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "folder".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $parent_id
 * @property int|null $user_id
 *
 * @property User $user
 */
class Folder extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'folder';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id', 'user_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            ['parent_id', 'checkOwner'],
            ['user_id', 'checkUser'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'parent_id' => 'Parent ID',
            'user_id' => 'User ID',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Gets query for [[Files]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany(File::class, ['parent_id' => 'id']);

    }

    public function createFolder(array $data)
    {
        if ($this->load($data) && $this->save()) {
            $path = Folder::getPath($this->id);
            mkdir($path);
            return true;
        }
        return false;
    }

    static function getPath(int $folderId, string $path = '')
    {
        $folderInstance = Folder::findOne($folderId);
        $path = $folderId.'/'.$path ;
        if ($folderInstance->parent_id == 0) {
            return 'storage/' . $path;
        } else {
            return Folder::getPath($folderInstance->parent_id,  $path);
        }
    }

    static function getParentsFolder(int $folderId, array $parents = [])
    {
        $folderInstance = Folder::findOne($folderId);
        if($folderInstance){
            $parents[] = $folderInstance;
        }
        if ($folderInstance->parent_id == 0) {
            return $parents;
        } else {
            return Folder::getParentsFolder($folderInstance->parent_id, $parents);
        }
    }

    public function checkUser($attribute)
    {
        if (!$this->hasErrors()) {
            if ($this->user_id != Yii::$app->user->id && isset(Yii::$app->user->id)) {
                $this->addError($attribute, 'Не верный пользователь');
            }
        }
    }

    public function checkOwner($attribute)
    {
        if (!$this->hasErrors()) {
            $parentFolder = Folder::findOne($this->parent_id);

            if ($parentFolder->user_id != Yii::$app->user->id && !is_null($parentFolder)) {
                $this->addError($attribute, 'Вы не можете создать папку в этой дериктории!');
            }
        }
    }

    public static function getRootFolder()
    {
        return Folder::findOne(['user_id' => Yii::$app->user->id, 'parent_id' => 0]);
    }

    public static function getChildrenFolder(int $parentFolder)
    {
        return Folder::findAll(['user_id' => Yii::$app->user->id, 'parent_id' => $parentFolder]);
    }
}
