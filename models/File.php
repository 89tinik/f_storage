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
 * @property int|null $type
 * @property int|null $user_id
 *
 * @property User $user
 */
class File extends \yii\db\ActiveRecord
{
    const DEF = 1;
    const WORD = 2;
    const IMAGE = 3;

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
            [['size', 'parent_id', 'type', 'user_id'], 'integer'],
            [['created'], 'safe'],
            [['name', 'path', 'public_link'], 'string', 'max' => 255],
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
            'type' => 'Type',
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

    public function setTypeFyle($extention)
    {
        switch ($extention) {
            case 'png':
            case 'jpg':
            case 'jpeg':
                $this->type = self::IMAGE;
                break;
            case 'doc':
            case 'docx':
                $this->type = self::WORD;
                break;
            default:
                $this->type = self::DEF;
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
     * Gets query for [[Type0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getType0()
    {
        return $this->hasOne(Type::class, ['id' => 'type']);
    }
}
