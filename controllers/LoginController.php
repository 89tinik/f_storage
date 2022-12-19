<?php

namespace app\controllers;

use app\models\Folder;
use app\models\LoginForm;
use app\models\RegisterForm;
use app\models\RepasswordForm;
use app\models\User;
use app\models\VerificationForm;
use yii\filters\VerbFilter;
use yii\web\Controller;
use Yii;

class LoginController extends Controller
{
    public $layout = 'login';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $this->ifNotGuest();
        $loginForm = new LoginForm();
        if ($loginForm->load(Yii::$app->request->post()) && $loginForm->login()) {
            return $this->goHome();
        }
        return $this->render('index', compact('loginForm'));
    }

    public function actionRegistration()
    {
        $this->ifNotGuest();
        $model = new RegisterForm();
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            if ($model->signup()) {
                return $this->redirect(['login/verification']);
            }
        }
        return $this->render('registration', compact('model'));
    }

    public function actionRepassword()
    {
        $this->ifNotGuest();
        $model = new RepasswordForm;
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            if ($model->setNewPassword()) {
                Yii::$app->session->setFlash('success', 'Пароль изменён. Логин для входа <b>' . $model->email . '</b>.');
                return $this->redirect(['login/index']);
            } else {
                Yii::$app->session->setFlash('error', 'Логина <b>' . $model->email . '</b> - не найдено.');
            }

        }
        return $this->render('repassword', compact('model'));
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect('/login');
    }

    public function actionVerification()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $verificationForm = new VerificationForm();
        if ($verificationForm->load(Yii::$app->request->post())) {
            if ($user = $verificationForm->activate()) {
                $folder = new Folder;
                $folder->createFolder(['Folder' => ['name' => 'Моё хранилище', 'user_id' => $user->id, 'parent_id' => 0]]);
                Yii::$app->session->setFlash('success', 'Регистрация завершена. Логин для входа <b>' . $user->email . '</b>.');
                return $this->redirect('/login');
            }
        } else {
            $user = User::findOne(['id' => Yii::$app->session->get('uId')]);
            if ($user) {
                $send = $user->sendVerification();
                if ($send === true) {
                    Yii::$app->session->setFlash('message', 'Код отправлен на e-mail ' . Yii::$app->session->get('contact') . '<br/>Его нужно использовать в течение 10 минут');
                } else {
                    Yii::$app->session->setFlash('error', $send['error']);
                }
            } else {
                Yii::$app->session->setFlash('error', 'Ваша сессия просрочена - заполните форму регистрации заново.');
            }
        }
        return $this->render('verification', compact('verificationForm'));
    }

    protected function ifNotGuest()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
    }


}