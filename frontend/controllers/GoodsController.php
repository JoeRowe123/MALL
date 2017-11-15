<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/14
 * Time: 14:34
 */

namespace frontend\controllers;


use backend\models\ArticleCategory;
use frontend\models\Goods;
use frontend\models\GoodsCategory;
use yii\data\Pagination;
use yii\web\Controller;

class GoodsController extends Controller
{
    /**
     * 商城首页
     * @return string
     */
    public function actionIndex(){
        //查询文章分类
        $article = ArticleCategory::find()->all();
        return $this->render('index',['article_category'=>$article]);
    }

    public function actionShow(){
        $category_id  = \Yii::$app->request->get('category_id');
//        var_dump($category_id);die;
        $categories = \backend\models\GoodsCategory::findOne(['id'=>$category_id]);
        if ($categories->depth==2){
            //查出三级分类下的商品
            $query = \backend\models\Goods::find()->where(['goods_category_id'=>$category_id]);
        }else{
            //查出一二级菜单下的三级分类的id
            $ids = $categories->children()->andWhere(['depth'=>2])->column();
            //根据三级分类id查出相应的商品
            $query = \backend\models\Goods::find()->where(['in','goods_category_id',$ids]);
        }
        //分页查询
        $pager = new Pagination();
        $pager->totalCount = $query->count();
        $pager->pageSize = 20;
        $models = $query->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('goods',['goods'=>$models,'pager'=>$pager]);
    }
}