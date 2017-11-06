<?php
namespace backend\controllers;

use backend\models\Brand;
use yii\data\Pagination;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;
use yii\web\UploadedFile;
// 引入鉴权类
use Qiniu\Auth;

// 引入上传类
use Qiniu\Storage\UploadManager;

class BrandController extends Controller
{
    public $enableCsrfValidation = false;
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
            //文本框过滤
            $model->intro = htmlspecialchars($model->intro);
            //验证数据
            if ($model->validate()){
                //验证通过
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
            //文本框过滤
            $model->intro = htmlspecialchars($model->intro);
            //验证数据
            if ($model->validate()){
                //验证通过
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
    //文件自动上传处理
    public function actionUpload(){
        //判断上传方式
        if (\Yii::$app->request->isPost){
            $logoFile = UploadedFile::getInstanceByName('file');
            //确认上传
            if ($logoFile) {
                //获取后缀名
                $ext = $logoFile->extension;
                //生成文件路径
                $file = '/upload/brand/'.uniqid().'.'.$ext;
                //文件保存
                $logoFile->saveAs(\Yii::getAlias('@webroot'.$file),false);
                //上传七牛云
                $accessKey ="1AirGLtQeu_2dspTMdLzayNaK12dKEPkEs3ZOLdB";
                $secretKey = "R0pPWoff00zLPQIB7bk-5KwtLrTiXrr_ijU5MXVN";
                $bucket = "mall";

                // 构建鉴权对象
                $auth = new Auth($accessKey, $secretKey);

                // 生成上传 Token
                $token = $auth->uploadToken($bucket);

                // 要上传文件的本地路径
                $filePath = \Yii::getAlias('@webroot').$file;

                // 上传到七牛后保存的文件名
                $key = $file;

                // 初始化 UploadManager 对象并进行文件的上传。
                $uploadMgr = new UploadManager();

                // 调用 UploadManager 的 putFile 方法进行文件的上传。
                list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
                if ($err !== null) {
                    //失败返回错误信息
                    return Json::encode(['error'=>$err]);
                } else {
                    //上传成功，返回文件路径
                    return Json::encode('http://oyy6mn13p.bkt.clouddn.com/'.$file);
                }

            }

        }
    }

    public function actionTest(){


// 需要填写你的 Access Key 和 Secret Key
        $accessKey ="1AirGLtQeu_2dspTMdLzayNaK12dKEPkEs3ZOLdB";
        $secretKey = "R0pPWoff00zLPQIB7bk-5KwtLrTiXrr_ijU5MXVN";
        $bucket = "mall";

        // 构建鉴权对象
        $auth = new Auth($accessKey, $secretKey);

        // 生成上传 Token
        $token = $auth->uploadToken($bucket);

        // 要上传文件的本地路径
        $filePath = \Yii::getAlias('@webroot').'/upload/brand/59ff22f0d2c51.jpg';

        // 上传到七牛后保存的文件名
        $key = '59ff22f0d2c51.jpg';

        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();

        // 调用 UploadManager 的 putFile 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        echo "\n====> putFile result: \n";
        if ($err !== null) {
            var_dump($err);
        } else {
            var_dump($ret);
        }

    }
    //远端访问（七牛云）方式
    //  oyy6mn13p.bkt.clouddn.com(域名)+/59ff22f0d2c51.jpg(key)

}