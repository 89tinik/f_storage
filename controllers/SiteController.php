<?php

namespace app\controllers;

use app\models\Folder;
use app\models\UploadForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
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
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex_()
    {
        return $this->render('index');
    }

    /**
     * Lists all Folder models.
     *
     * @return string
     */
    public function actionIndex($id = '')
    {
        $currentFolder = (empty($id)) ? Folder::getRootFolder() : Folder::findOne($id);

        $folders = Folder::getChildrenFolder($currentFolder->id);
        $parents = Folder::getParentsFolder($currentFolder->parent_id);

        return $this->render('index', [
            'current' => $currentFolder,
            'folders' => $folders,
            'parents' => array_reverse($parents),
        ]);
    }

    /**
     * Displays a single Folder model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Folder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreateFolder($id = '')
    {
        $model = new Folder();
        if ($this->request->isPost) {
            if ($model->createFolder($this->request->post())) {
                return $this->redirect(['index', 'id' => $model->id]);
            }
        } else {
            $model->parent_id = intval(Yii::$app->request->get('id'));
            $model->user_id = intval(Yii::$app->user->id);
        }
        return $this->render('folder/create', [
            'model' => $model,
        ]);
    }

    public function actionUpload($folder_id)
    {
        $model = new UploadForm();
        $model->parent_id = $folder_id;
        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');
            try {
                $model->upload();
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        return $this->render('upload', ['model' => $model]);
    }


    /**
     * Updates an existing Folder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Folder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Folder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Folder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Folder::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
