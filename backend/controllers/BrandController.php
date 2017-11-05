<?php
namespace backend\controllers;

use backend\models\Brand;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\Request;
use yii\web\UploadedFile;

class BrandController extends Controller
{
    //展示品牌列表
    public function actionList(){
        //创建查询器（对象）
        $query = Brand::find()->where(['>','status',-1]);
        //创建分页工具对象
        $pager = new Pagination();
        //统计品牌数据总条数
        $pager->totalCount = $query->count();
        //设置每页显示条数
        $pager->pageSize = 6;
        //按分页条件查询数据
        $model = $query->limit($query->limit)->offset($query->offset)->all();
//        var_dump($model);die;
        //将数据分配到视图，并显示
        return $this->render('list',['model'=>$model,'pager'=>$pager]);
    }
    //添加品牌
    public function actionAddBrand(){
        //创建品牌对象
        $model = new Brand();
        //创建请求对象
        $request = new Request();
        if ($request->isPost){
            //保存post提交的数据
            $model->load($request->post());
            $model->logoFile = UploadedFile::getInstance($model,'logoFile');
            //验证数据
            if ($model->validate()){
                //验证通过
                //获取文件后缀名
                $ext = $model->logoFile->extension;
                //设置完整文件路径
                $file = '/upload/brand/'.uniqid().'.'.$ext;

                //移动文件临时文件(永久化)
                $model->logoFile->saveAs(\Yii::getAlias('@webroot').$file,false);
                //将文件路径保存到model->logo中
                $model->logo = $file;
                //品牌信息保存到数据库
                $model->save();
                //提示并跳转
                \Yii::$app->session->setFlash('success','添加品牌成功');
                return $this->redirect('/brand/list.html');
            }else{
                var_dump($model->getErrors());die;
            }
        }
        //展示添加品牌视图
        return $this->render('add',['model'=>$model]);
    }
    //修改品牌信息
    public function actionEditBrand($id){
        //根据id查询品牌信息
        $model = Brand::findOne(['id'=>$id]);
        $request = new Request();
        if ($request->isPost){
            //保存post提交的数据
            $model->load($request->post());
            $model->logoFile = UploadedFile::getInstance($model,'logoFile');
            //验证数据
            if ($model->validate()){
                //验证通过
                //获取文件后缀名
                $ext = $model->logoFile->extension;
                //设置完整文件路径
                $file = '/upload/brand/'.uniqid().'.'.$ext;
                //移动文件临时文件(永久化)
                $model->logoFile->saveAs(\Yii::getAlias('@webroot').$file,false);
                //将文件路径保存到model->logo中
                $model->logo = $file;
                //品牌信息保存到数据库
                $model->save();
                //提示并跳转
                \Yii::$app->session->setFlash('success','修改信息成功');
                return $this->redirect('/brand/list.html');
            }else{
                var_dump($model->getErrors());die;
            }
        }
        //显示修改表单，并回显品牌信息
        return $this->render('add',['model'=>$model]);
    }
    public function actionDeleteBrand(){
        $request = new Request();
        $id = $_GET['id'];
        $model = Brand::findOne(['id'=>$id]);
        $model->status = -1;
        $model->save();
        echo 1;
    }
}