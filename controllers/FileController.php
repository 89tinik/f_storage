<?php

namespace app\controllers;

use app\models\File;
use app\models\Folder;
use app\models\UploadForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class FileController extends Controller
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
                    [
                        'allow' => true,
                        'actions' => ['view', 'download'],
                        'roles' => ['?'],
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
     * Displays a single Folder model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $fileInfo = $this->findFileModel($id);
        if($fileInfo->user_id == Yii::$app->user->identity->id || $fileInfo->public_link) {
            $fileInfo->addSessionDownload();
            $parents = Folder::getParentsFolder($fileInfo->parent_id);
            return $this->render('view', [
                'model' => $fileInfo,
                'parents' => array_reverse($parents),
            ]);
        } else {
            throw new HttpException(403, 'Доступ запрещён');
        }
    }

    /**
     * @param $folder_id
     * @return string|\yii\web\Response
     */
    public function actionUpload($folder_id)
    {
        $model = new UploadForm();
        $model->parent_id = $folder_id;
        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');
            try {
                $model->upload();
                return $this->redirect(['site/index', 'id' => $model->parent_id]);
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        $parents = Folder::getParentsFolder($folder_id);
        return $this->render('upload', ['parents' => array_reverse($parents),'model' => $model]);
    }

    /**
     * @param $id
     * @return \yii\console\Response|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDownload($id)
    {
        $fileInfo = $this->findFileModel($id);
        if($fileInfo->checkDownload()) {
            return Yii::$app->response->sendFile($fileInfo->path, $fileInfo->name);
        } else {
            throw new HttpException(403, 'Доступ запрещён');
        }
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
        $model = $this->findFileModel($id);
        if($model->user_id == Yii::$app->user->identity->id ) {
            if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
            $parents = Folder::getParentsFolder($model->parent_id);
            return $this->render('update', [
                'parents' => array_reverse($parents),
                'model' => $model,
            ]);
        } else {
            throw new HttpException(403, 'Доступ запрещён');
        }
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
        $this->findFileModel($id)->deleteFile();
        return $this->redirect(['site/index']);
    }

    /**
     * Finds the Folder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return File the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findFileModel($id)
    {
        if (($model = File::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
