<?php


namespace app\models;


use yii\base\Model;

class RegisterForm extends Model
{

    public $name;
    public $email;
    public $password;

    public function rules()
    {
        return [
            [['name', 'email', 'password'], 'required'],
            [['name'], 'string'],
            [['email'], 'email'],
            [['email'], 'unique', 'targetClass' => 'app\models\User', 'targetAttribute' => 'email']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return ['name' => 'Имя', 'email' => 'E-mail', 'password' => 'Пароль'];
    }

    /**
     * @return true|void
     */
    public function signup()
    {
        if ($this->validate()) {
            $user = new User();
            $user->email = $this->email;
            $user->name = $this->name;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            if ($user->save() && $user->setVerification()) {
                return true;
            }
        }
    }
}