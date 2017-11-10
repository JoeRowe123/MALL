<?php
namespace backend\controllers;

use backend\models\ArticleCategory;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\Request;

class ArticleCategoryController extends Controller
{
    public function actionList(){
        //创建查询器（对象）
        $query = ArticleCategory::find()->where(['>','status',-1]);
        //创建分页工具对象
        $pager = new Pagination();
        //统计品牌数据总条数
        $pager->totalCount = $query->count();
        //设置每页显示条数
        $pager->pageSize = 6;
        //按分页条件查询数据
        $model = $query->limit($pager->limit)->offset($pager->offset)->all();
        //将数据分配到视图，并显示
//        var_dump($model);die;
        return $this->render('/article-category/list',['model'=>$model,'pager'=>$pager]);
    }
    public function actionAdd(){
        $model = new ArticleCategory();
        $request = new Request();
        if ($request->isPost){
            $model->load($request->post());
            //文本框过滤
            $model->intro = htmlspecialchars($model->intro);
            if ($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect('list.html');
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    //修改分类
    public function actionEdit($id){
        $model = ArticleCategory::findOne(['id'=>$id]);
        $request = new Request();
        if ($request->isPost){
            //加载表单提交的数据
            $model->load($request->post());
            //文本框过滤
            $model->intro = htmlspecialchars($model->intro);
            //验证数据
            if ($model->validate()){
                //将提交的分类信息保存数据库
                $model->save();
                //设置提示消息并跳转
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect('list.html');
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    //删除
    public function actionDelete($id){
        $result = ArticleCategory::updateAll(['status'=>-1],['id'=>$id]);
        if ($result){
            echo 1;
        }else{
            echo json_encode('删除失败');
        }
    }
}