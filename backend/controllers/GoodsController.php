<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/6
 * Time: 11:19
 */

namespace backend\controllers;


use backend\models\Goods;
use backend\models\GoodsDayCount;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\Request;
use yii\web\UploadedFile;

class GoodsController extends Controller
{
    //允许图片上传
    public $enableCsrfValidation = false;
    public function actionList(){
        $conditions = [];
        //判断搜索是否有值传送
        if (isset($_GET['search'])){
            $serch = $_GET['search'];
            $conditions["name"] = isset($serch['name'])?$serch['name']:'';
            $conditions["sn"] = isset($serch['sn'])?$serch['sn']:'';
            $conditions["min"] = isset($serch['min'])?$serch['min']:0;
            $conditions["max"] = $serch['max'];
            //搜索最高金额时，若没有传值，则不添加搜索条件，否则搜索金额低于0的商品,则全部不满足条件
            if (isset($serch['max'])){
                $query = Goods::find()->andWhere(['status'=>1])->andWhere(['like','name',$conditions['name']])->andWhere(['like','sn',$conditions['sn']])->andWhere(['>=','shop_price',$conditions['min']]);
            }else{
                $query = Goods::find()->andWhere(['status'=>1])->andWhere(['like','name',$conditions['name']])->andWhere(['like','sn',$conditions['sn']])->andWhere(['>=','shop_price',$conditions['min']])->andWhere(['<=','shop_price',$conditions['max']]);
            }
        }else{
            $query = Goods::find()->andWhere(['status'=>1]);
        }
        //创建分页工具对象
        $pager = new Pagination();
        //统计品牌数据总条数
        $pager->totalCount = $query->count();
        //设置每页显示条数
        $pager->pageSize = 5;
        //按分页条件查询数据
        $model = $query->limit($query->limit)->offset($query->offset)->all();
        //自动添加货号
        //将数据分配到视图，并显示
        return $this->render('list',['model'=>$model,'pager'=>$pager]);
    }

    /**
     * 显示商品详情
     * @param $id
     * @return string
     */
    public function actionShow($id){
        $detail = GoodsIntro::findOne(['goods_id'=>$id]);
        return $this->render('show',['detail'=>$detail]);
    }
    /**
     * 添加商品
     */
    public function actionAddGoods(){
        $model = new Goods();
        $intro = new GoodsIntro();
        $count = new GoodsDayCount();
        //设置默认parent_id,不设置分类无法显示（修改表单需要父节点id）
        $model->goods_category_id = 0;
        $request = new Request();
        if ($request->isPost){
            //接收提交的数据
            $model->load($request->post());
            //获取商品详情保存到表中
            $intro->content = $model->intro;
            $model->create_time = time();
            if ($model->validate()){
                //处理数据并保存
                //获取每日添加的货号和日期
                 $count->day = date('Y-m-d',$model->create_time);
                //通过数据库中的组件判断当天是否为第一次添加
                $result = GoodsDayCount::findOne(['day'=>$count->day]);
                //若为当天第一次添加则保存数据库
                if (!$result){
                    $count->count = 1;
                    $count->save();
                }else{
                    //若当天第n次添加则修改数据
                    $count->count = $result->count + 1;
                    GoodsDayCount::updateAll(['count'=>$count->count],['day'=>$count->day]);
                }
                $model->sn = date('Ymd',time()).sprintf("%06d",$count->count);
                $model->save();
                //id统一
                $intro->goods_id = $model->id;
//                var_dump($intro->content);die;
                $intro->save();
                //跳转到列表页面
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect('list.html');
            }else{
                var_dump($model->getErrors());die;
            }
        }
        //展示添加商品的表单
        return $this->render('add-goods',['model'=>$model]);
    }

    /**
     * 修改
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionEditGoods($id){
        $model = Goods::findOne(['id'=>$id]);
        $intro = GoodsIntro::findOne(['goods_id'=>$id]);
        $request = new Request();
        if ($request->isPost){
            //接收提交的数据
            $model->load($request->post());
            //获取商品详情保存到表中
            $intro->content = $model->intro;
            $model->create_time = time();
            if ($model->validate()){
                //处理数据并保存
                //获取每日添加的货号
                $model->save();
                //id统一
                $intro->goods_id = $model->id;
                $intro->save();
                //跳转到列表页面
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect('list.html');
            }else{
                var_dump($model->getErrors());die;
            }
        }
        //展示添加商品的表单
        $model->intro  = $intro->content;
        return $this->render('add-goods',['model'=>$model]);
    }

    /**
     * 相册展示
     * @return string
     */
    public function actionGallery($id){
        $model = GoodsGallery::find()->where(['goods_id'=>$id])->all();
        return $this->render('gallery',['model'=>$model,'id'=>$id]);
    }

    /**
     * 上传相册
     * @return string
     */
    public function actionAddGallery(){
        if (\Yii::$app->request->isPost){
            //创建相册对象
            $gallery = new GoodsGallery();
            if (!empty($_POST)){
                $gallery->goods_id = $_POST['goods_id'];
                $gallery->path = $_POST['path'];
//                var_dump($gallery);die;
                $result = $gallery->save();
                if ($result){
                    echo json_encode($gallery->id);
                }
            }
        }
    }

    /**
     *删除相册
     */
    public function actionDeleteGallery(){
        $id = $_POST['id'];
        $gallery = GoodsGallery::findOne(['id'=>$id]);
        $result = $gallery->delete();
        if ($result){
            echo 1;
        }else{
            echo "图片不存在";
        }
    }
    public function actionDelete($id){
        $result= Goods::updateAll(['status'=>0],['id'=>$id]);
        if ($result){
            echo 1;
        }else{
            echo '商品不存在，或已被删除！';
        }
    }
    /**
     * 文件上传
     * @return string 文件路径
     */
    public function actionLogo()
    {
        //判断上传方式
        if (\Yii::$app->request->isPost) {
            $logoFile = UploadedFile::getInstanceByName('file');
            //确认上传
            if ($logoFile) {
                //获取后缀名
                $ext = $logoFile->extension;
                //生成文件路径
                $file = '/upload/goods/' . uniqid() . '.' . $ext;
                //文件保存
                $logoFile->saveAs(\Yii::getAlias('@webroot' . $file), false);
                return json_encode($file);
            }
        }
    }
    public function actions()
    {
        return [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
            ]
        ];
    }
}