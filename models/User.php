<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $auth_key
 * @property int|null $storage_size
 * @property int|null $max_storage_size
 * @property int|null $max_file_size
 * @property int|null $blocked
 * @property int|null $admin
 *
 * @property File[] $files
 * @property Folder[] $folders
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'email', 'password'], 'required'],
            [['storage_size', 'max_storage_size', 'max_file_size', 'blocked', 'admin'], 'integer'],
            [['name', 'email', 'password', 'auth_key'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'email' => 'Email',
            'password' => 'Пароль',
            'max_size_storage' => 'Максимальный размер хранилища',
            'max_file_size' => 'Максимальный размер файла',
            'blocked' => 'Заблокирован',
            'admin' => 'Администратор',
            'storage_size' => 'Занятое место хранилища',
        ];
    }


    /**
     * Gets query for [[Files]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany(File::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Folders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFolders()
    {
        return $this->hasMany(Folder::class, ['user_id' => 'id']);
    }

    /**
     * @param $token
     * @param $type
     * @return void|IdentityInterface|null
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {

    }

    /**
     * @param string $password
     * @return void
     * @throws \yii\base\Exception
     */
    public function setPassword($password)
    {
        $this->password = \Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * @return void
     * @throws \yii\base\Exception
     */
    public function generateAuthKey()
    {
        $this->auth_key = \Yii::$app->security->generateRandomString();
    }

    /**
     * @return true
     */
    public function setVerification()
    {
        Yii::$app->session->set('vCode', rand(1000, 9999));
        Yii::$app->session->set('uId', $this->id);
        Yii::$app->session->set('contact', substr_replace($this->email, '****', 1, 4));

        return true;
    }

    /**
     * @return string[]|true
     */
    public function sendVerification()
    {
        $vCode = Yii::$app->session->get('vCode');
        $mail = Yii::$app->mailer->compose()
            ->setFrom('noreply@send.rgmek.ru')
            ->setTo($this->email)
            ->setSubject('Подтверждение почты')
            ->setHtmlBody('<h2>Добрый день!</h2><p>Вы получили настоящее письмо так как указали этот адрес электронной почты при регистрации в личном кабинете небытовых потребителей компании ООО «РГМЭК».</p><p>Код подтверждения:<b>' . $vCode . '</b>.</p><p>Если Вы не отправляли запрос на регистрацию просто удалите это письмо.</p>')
            ->send();
        if (!$mail) {
            return ['error' => 'Не удалось отправить письмо - повторите попытку регистрации позже.'];
        }
        return true;
    }

    /**
     * @return $this|false
     */
    public function activation()
    {
        $this->blocked = 0;
        if ($this->save()) {
            Yii::$app->session->remove('vCode');
            Yii::$app->session->remove('uId');
            Yii::$app->session->remove('contact');
            return $this;
        }
        return false;

    }


    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        if (!empty($this->password)) {
            return \Yii::$app->security->validatePassword($password, $this->password);
        } else {
            return false;
        }
    }

}
