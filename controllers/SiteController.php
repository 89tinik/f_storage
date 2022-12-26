<?php

namespace app\controllers;

use app\models\Folder;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\HttpException;
use yii\filters\VerbFilter;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
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
     * @param $id
     * @return string
     * @throws HttpException
     */
    public function actionIndex($id = '')
    {
        $currentFolder = (empty($id)) ? Folder::getRootFolder() : Folder::findOne($id);
        if($currentFolder->user_id == Yii::$app->user->identity->id ) {
            $folders = Folder::getChildrenFolder($currentFolder->id);
            $parents = Folder::getParentsFolder($currentFolder->parent_id);

            return $this->render('index', [
                'current' => $currentFolder,
                'folders' => $folders,
                'parents' => array_reverse($parents),
            ]);
        } else {
            throw new HttpException(403, 'Доступ запрещён');
        }
    }

    /**
     *  Creates a new Folder model.
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionCreateFolder( $id = '')
    {
        $model = new Folder();
        if ($this->request->isPost) {
            if ($model->createFolder($this->request->post())) {
                return $this->redirect(['index', 'id' => $model->id]);
            }
        } else {
            $model->parent_id = intval($id);
            $model->user_id = intval(Yii::$app->user->id);
            $parents = Folder::getParentsFolder($id);
        }
        return $this->render('folder/create', [
            'model' => $model,
            'parents' => array_reverse($parents),
        ]);
    }

}
