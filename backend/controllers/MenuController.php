<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/10
 * Time: 11:54
 */

namespace backend\controllers;


use backend\models\Menu;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;

class MenuController extends Controller
{
    public function actionList(){
        $model = Menu::find()->orderBy(['parent'=>'ASC'])->all();
        return $this->render('list',['model'=>$model]);
    }

    /**
     * 添加菜单
     * @return string
     */
    public function actionAdd(){
        $model = new Menu();
        $request = \Yii::$app->request;
        $url = \Yii::$app->authManager->getPermissions();
        $permissions = ArrayHelper::map($url,'name','description');
        if ($request->isPost){
            //接收数据
            $model->load($request->post());
            if ($model->validate()){
                if ($model->parent==0){
                    $model->depth = 0;
                }else{
                    $model->depth = 1;
                }
                //保存数据并跳转
                $model->save();
                \Yii::$app->session->setFlash('success','添加菜单成功');
                return $this->redirect(Url::to('/menu/list.html'));
            }else{
                var_dump($model->getErrors());
            }
        }
        return $this->render('add',['model'=>$model,'permissions'=>$permissions]);
    }

    public function actionEdit($id){
        $model = Menu::findOne(['id'=>$id]);
        $request = \Yii::$app->request;
        //获取路由
        $url = \Yii::$app->authManager->getPermissions();
        $permissions = ArrayHelper::map($url,'name','description');
        if ($request->isPost) {
//接收数据
            $model->load($request->post());
            if ($model->validate()) {
                if ($model->parent == 0) {
                    $model->depth = 0;
                } else {
                    $model->depth = 1;
                }
                //保存数据并跳转
                $model->save();
                \Yii::$app->session->setFlash('success', '添加菜单成功');
                return $this->redirect(Url::to('/menu/list.html'));
            } else {
                var_dump($model->getErrors());
            }
        }
        return $this->render('add',['model'=>$model,'permissions'=>$permissions]);
    }

    public function actionDelete($id){
        $menu = Menu::findOne(['id'=>$id]);
        if ($menu){
            $menu->delete();
            echo 1;
        }else{
            echo '菜单不存在，或已经被删除';
        }
    }
}