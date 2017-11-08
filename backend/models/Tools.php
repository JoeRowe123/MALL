<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/6
 * Time: 10:46
 */

namespace backend\models;


use yii\base\Model;
// 引入鉴权类
use Qiniu\Auth;
// 引入上传类
use Qiniu\Storage\UploadManager;
use yii\web\UploadedFile;

class Tools extends Model
{
    public static function actionUploadLocal(){
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
}