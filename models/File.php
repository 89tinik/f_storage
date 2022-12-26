<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "file".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $path
 * @property string|null $public_link
 * @property int|null $size
 * @property string|null $created
 * @property int|null $parent_id
 * @property int|null $thumb
 * @property int|null $user_id
 *
 * @property User $user
 */
class File extends \yii\db\ActiveRecord
{
    const THUMB = [
        'word' => '/images/icon/word.png',
        'default' => '/images/icon/default.png'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['size', 'parent_id', 'user_id', 'public_link'], 'integer'],
            [['created'], 'safe'],
            [['name', 'path', 'thumb'], 'string', 'max' => 255],
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
            'path' => 'Path',
            'public_link' => 'Public Link',
            'size' => 'Size',
            'created' => 'Created',
            'parent_id' => 'Parent ID',
            'thumb' => 'Thumb',
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
     * @param string $extention
     * @return void
     */
    public function setThumbFile(string $extention)
    {
        $thumbArray = self::THUMB;
        switch ($extention) {
            case 'png':
            case 'jpg':
            case 'jpeg':
                $this->thumb = '/' . $this->generateThumbImage($this->path, $extention);
                break;
            case 'doc':
            case 'docx':
                $this->thumb = $thumbArray['word'];
                break;
            default:
                $this->thumb = $thumbArray['default'];
        }

    }

    /**
     * Gets query for [[Parent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Folder::class, ['id' => 'parent_id']);
    }

    /**
     * @param $path
     * @param $extention
     * @return string|void
     */
    public function generateThumbImage($path, $extention)
    {
        //здесь можно сжимать первью картинки для экономия места
        $pathArray = explode('/', $path);
        $imageName = array_pop($pathArray) . '.' . $extention;
        $pathArray[0] = 'images/thumbs';
        $thumbPath = implode('/', $pathArray);
        if (!file_exists($thumbPath)) {
            mkdir($thumbPath, 0755, true);
        }
        $to = $thumbPath . '/' . $imageName;
        if (copy($path, $to)) {
            return $to;
        }
    }

    /**
     * @return void
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function deleteFile()
    {
        unlink($this->path);
        if (!in_array($this->thumb, self::THUMB)) {
            unlink(substr($this->thumb,1));
        }
        $this->user->storage_size = $this->user->storage_size - $this->size;
        $this->user->save(false);
        $this->delete();
    }

    /**
     * @return void
     */
    public function addSessionDownload()
    {
        $session = Yii::$app->session;
        $downloads = [];
        if ($session->has('downloads')) {
            $downloads = $session->get('downloads');
        }
        if (!in_array($this->path, $downloads)) {
            $downloads[] = $this->path;
        }
        $session['downloads'] = $downloads;
    }

    /**
     * @return bool
     */
    public function checkDownload()
    {
        $session = Yii::$app->session;
        if ($session->has('downloads')) {
            $downloads = $session->get('downloads');
            if (in_array($this->path, $downloads)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return void
     */
    public function changeShare()
    {
        $this->public_link = ($this->public_link) ? 0 : 1;
        $this->save();
    }

    /**
     * @return string
     */
    public function formatFileSize() {
        $size = $this->size;
        $a = array("B", "KB", "MB", "GB", "TB", "PB");
        $pos = 0;
        while ($size >= 1024) {
            $size /= 1024;
            $pos++;
        }
        return round($size,2)." ".$a[$pos];
    }
}
