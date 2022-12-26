<?php

namespace app\controllers;

use app\models\File;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;

class AjaxController extends Controller
{
    public $layout = 'ajax';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'info' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * @return string|void
     * @throws HttpException
     */
    public function actionFileInfo()
    {
        if ($fileInfo = $this->isAccess()) {
            $fileInfo->addSessionDownload();
            return $this->render('file/info', [
                'file' => $fileInfo,
            ]);
        }
    }
    /**
     * @return string|void
     * @throws HttpException
     */
    public function actionFileShare()
    {
        if ($fileInfo = $this->isAccess()) {
            $fileInfo->changeShare();
            return $this->render('file/info', [
                'file' => $fileInfo,
            ]);
        }
    }

    /**
     * @return File|null
     * @throws HttpException
     */
    protected function isAccess()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $fileInfo = File::findOne($data['id']);
            if ($fileInfo->user_id == Yii::$app->user->identity->id) {
                return $fileInfo;
            }
        }
        throw new HttpException(403, 'Доступ запрещён');
    }
}