<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/14
 * Time: 14:34
 */

namespace frontend\controllers;


use backend\models\ArticleCategory;
use backend\models\Goods;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use frontend\models\GoodsCategory;
use frontend\models\SphinxClient;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class GoodsController extends Controller
{
    /**
     * 商城首页
     * @return string
     */
    public function actionIndex(){
//        $article = ArticleCategory::find()->all();
//        return $this->renderPartial('index',['article_category'=>$article]);
        return $this->redirect('/index.html');
    }
    /**
     * 生成静态首页
     */
    public function actionStaticIndex(){
        $article = ArticleCategory::find()->all();
        //获取首页内容
        $content = $this->renderPartial('@frontend/views/goods/index',['article_category'=>$article]);
        //获取静态文件路径
        $fileName = \Yii::getAlias('@frontend/web/index.html');
        //将内容放入静态文件
        file_put_contents($fileName,$content);
        return $this->redirect('/index.html');
}
    /**
     * 商品搜索
     */
    public function actionSearch($keywords){
        $cl = new SphinxClient();
        //设置sphinx的searchd服务信息
        $cl->SetServer ( '127.0.0.1', 9312);
        $cl->SetConnectTimeout ( 10 );
        $cl->SetArrayResult ( true );
// $cl->SetMatchMode ( SPH_MATCH_ANY);
        //设置匹配模式  all（各分词集合取并）
        $cl->SetMatchMode ( SPH_MATCH_EXTENDED2);
        $cl->SetLimits(0, 1000);
        //查询关键字
        $info = $keywords;
        //第二个参数：使用在配置文件中配置的索引
        $res = $cl->Query($info, 'goods');//shopstore_search
        if (isset($res['matches'])){
            //根据匹配到的id去获取，存入一个数组
            $ids = ArrayHelper::map($res['matches'],'id','id');
            //根据id查询商品信息
            $goods = Goods::find()->where(['in','id',$ids])->all();
        }else{
            $goods = [];
        }
        return $this->render('list',['goods'=>$goods]);
    }
    /**
     * 商品列表
     * @return string
     */
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
        return $this->render('list',['goods'=>$models,'pager'=>$pager]);
    }

    /**
     * 展示商品详情
     * @param $id
     * @return string
     */
    public function actionGoods($id){
        $redis = new \Redis();
        $redis->connect('127.0.0.1');
        //查询点击查看的商品详情
        $goods = \backend\models\Goods::find()->andWhere(['id'=>$id])->andWhere(['status'=>1])->one();
        //查询redis中是否保存该商品的浏览次数
        $view_times = $redis->get('TIMES_'.$id);
        if ($view_times){
            //有则增加浏览次数
            $redis->incr('TIMES_'.$id);
        }else{
            //没有则保存到redis
            $redis->set('TIMES_'.$id,$goods->view_times+1);
        }
        $goods->view_times = $view_times+1;
        $gallery = GoodsGallery::find()->where(['goods_id'=>$id])->all();
        $goods_intro = GoodsIntro::findOne(['goods_id'=>$id]);
        return $this->render('goods',['goods'=>$goods,'gallery'=>$gallery,'detail'=>$goods_intro]);
    }
}