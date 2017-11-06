<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/5
 * Time: 16:49
 */

namespace backend\controllers;

use backend\models\GoodsCategory;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\Request;

class GoodsCategoryController extends Controller
{
    /**
     * 展示商品分类
     * @return string
     */
    public function actionList(){
        $query = GoodsCategory::find();
        //创建分页工具对象
        $pager = new Pagination();
        //统计品牌数据总条数
        $pager->totalCount = $query->count();
        //设置每页显示条数
        $pager->pageSize = 6;
        //按分页条件查询数据
        $model = $query->limit($query->limit)->offset($query->offset)->all();
        //将数据分配到视图，并显示
        return $this->render('list',['model'=>$model,'pager'=>$pager]);
    }

    /**
     * 添加商品分类
     * @return string|\yii\web\Response
     */
    public function actionAddCategory(){
        $model = new GoodsCategory();
        //设置默认parent_id,不设置分类无法显示（修改表单需要父节点id）
        $model->parent_id = 0;
        $request = new Request();
        if ($request->isPost){
            $model->load($request->post());
            //文本框过滤
            $model->intro = htmlspecialchars($model->intro);
            if ($model->validate()){
                if ($model->parent_id==0){
                    //创建根节点,nested-set中的保存方式
                    $model->makeRoot();//该方法来自行为注入的组件
                }else{
                    //创建子节点
//                    $newZeeland = new Menu(['name' => 'New Zeeland']);
//                    $newZeeland->insertBefore($australia);
                    //根据父id找到上一级
                    $parent = GoodsCategory::findOne(['id'=>$model->parent_id]);
                    //添加子节点
                    $model->prependTo($parent);
                }
                return $this->redirect('list.html');
            }
        }
        return $this->render('add',['model'=>$model]);
    }

    /**
     * 修改商品分类
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionEdit($id){
        $model = GoodsCategory::findOne(['id'=>$id]);
        $request = \Yii::$app->request;
        if ($request->isPost){
            $model->load($request->post());
            //文本框过滤
            $model->intro = htmlspecialchars($model->intro);
            if ($model->validate()){
                if ($model->parent_id==0){
                    //创建根节点,nested-set中的保存方式
                    $model->makeRoot();//该方法来自行为注入的组件
                }else{
                    //创建子节点
//                    $newZeeland = new Menu(['name' => 'New Zeeland']);
//                    $newZeeland->insertBefore($australia);
                    //根据父id找到上一级
                    $parent = GoodsCategory::findOne(['id'=>$model->parent_id]);
                    //添加子节点
                    $model->prependTo($parent);
                }
                return $this->redirect('list.html');
            }
        }
        return $this->render('add',['model'=>$model]);
    }

    public function actionDelete($id){
        $category = GoodsCategory::findOne(['id'=>$id]);
        if($category){
            $category->delete();
            echo json_encode(1);
        }else{
            return '记录不存在，或已被删除！';
        }
    }
}