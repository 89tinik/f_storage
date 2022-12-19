<?php

namespace app\models;


use Yii;
use yii\base\Model;

class RepasswordForm extends Model
{
    public $email;
    public $password;

    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            [['email'], 'email']
        ];
    }


    /**
     * @return array
     */
    public function attributeLabels()
    {
        return ['email' => 'E-mail', 'password' => 'Пароль'];
    }

    /**
     * @return bool|void
     * @throws \yii\base\Exception
     */
    public function setNewPassword()
    {
        if ($this->validate()) {
            $user = User::findOne(['email' => $this->email]);
            if ($user){
                $user->setPassword($this->password);
                if ($user->save()){
                    return true;
                }
            }else{
                return false;
            }
        }
    }
}