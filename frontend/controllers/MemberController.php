<?php

namespace frontend\controllers;



use frontend\components\Sms;
use frontend\models\Address;
use frontend\models\LoginForm;
use frontend\models\Member;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Request;

class MemberController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * 登录验证
     * @return string|\yii\web\Response
     */
    public function actionLogin(){
        $model = new LoginForm();
        $request = \Yii::$app->request;
        if ($request->isPost){
            //获取登录信息
            $model->load($request->post(),'');
            if ($model->validate()){
                //验证用户信息及密码是否正确
                if ($model->login()){
                    //验证通过，跳转列表页
                    return $this->redirect(Url::to(['goods/index']));
                }
            }else{
                var_dump($model->getErrors());exit;
            }
        }
        //展示登录界面
        return $this->render('login');
    }

    /**
     * 注册
     * @return string|\yii\web\Response
     */
    public function actionSignUp(){
        $model = new Member();
        $request = new Request();
        if ($request->isPost){
            $model->load($request->post(),'');
            //生成auth_key（自动登录）
            $model->auth_key = \Yii::$app->security->generateRandomString();
            //加密密码
            $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
            if ($model->validate()){
//                var_dump($model);die;
                $model->save(false);
                return $this->redirect(Url::to(['goods/index']));
            }else{
                var_dump($model->getErrors());die;
            }
        }
        return $this->render('sign_up');
    }

    public function actionLogout(){
        //退出登录
        \Yii::$app->user->logout();
        //跳转页面
        return $this->redirect(Url::to(['member/login']));
    }
    /**
     * 短信验证码
     */
    public function actionSms(){
        $tel = $_POST['tel'];
        $code = rand(100000,999999);
        $response = Sms::sendSms(
            "夏天安静了", // 短信签名
            "SMS_109365465", // 短信模板编号
            $tel, // 短信接收者
            Array(  // 短信模板中字段的值
                "code"=>$code,
            )
        );
        if ($response->Code == 'OK'){
            //将验证码存入session
            $session = \Yii::$app->session;
            $session->open();
            //根据手机号保存验证码
            $session->set('code_'.$tel,$code);
            return "success";
        }else{
            return 'failed';
        }
    }

    /**
     * 前端验证手机验证码
     * @param $tel
     * @param $captcha
     * @return string
     */
    public function actionCheckCaptcha($tel,$captcha){
        $code = \Yii::$app->session->get('code_'.$tel);
//        var_dump($code);die;
        if ($code == $captcha){
            return 'true';
        }else{
            return 'false';
        }
    }
    /**
     * 验证注册的用户名唯一
     * @param $username
     * @return string
     */
    public function actionCheckName($username){
        $result = Member::findOne(['username'=>$username]);
        if($result){
            return 'false';
        }
        return 'true';
    }

    /**
     * 添加地址
     * @return string|\yii\web\Response
     */
    public function actionAddress(){
        $model = new Address();
        $request = \Yii::$app->request;
        //获取当前登录用户的id
        $model->member_id = \Yii::$app->user->id;
        if ($request->isPost){
            $model->load($request->post(),'');
            $model->status = $model->status?1:0;
            //若新增地址为默认地址则之前默认地址状态改为0
            if ($model->status == 1){
                Address::updateAll(['status'=>0],['status'=>1]);
            }
            if ($model->validate()){
                //验证通过保存数据库
                $model->save();
                //返回页面
                return $this->redirect(Url::to(['member/address']));
            }else{
                var_dump($model->getErrors());die;
            }
        }
        $address = Address::find()->where(['member_id'=>$model->member_id])->asArray()->all();
        return $this->render('address',['address'=>$address,'model'=>$model]);
    }

    /**
     * 删除地址
     * @param $id
     */
    public function actionDeleteAddress($id){
        $address = Address::findOne(['id'=>$id]);
        if ($address){
            $address->delete();
            echo "success";
        }else{
            echo "地址不存在，或已被删除";
        }
    }

    /**
     * 设置默认地址
     * @param $id
     * @return \yii\web\Response
     */
    public function actionStatus($id){
        Address::updateAll(['status'=>0],['status'=>1]);
        Address::updateAll(['status'=>1],['id'=>$id]);
        return $this->redirect(Url::to(['member/address']));
    }

    /**
     * 修改地址
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionUpdateAddress($id){
        $model = Address::findOne(['id'=>$id]);
        $request = \Yii::$app->request;
        if ($request->isPost){
            $model->load($request->post(),'');
            $model->status = $model->status?1:0;
            //若新增地址为默认地址则之前默认地址状态改为0
            if ($model->status == 1){
                Address::updateAll(['status'=>0],['status'=>1]);
            }
            if ($model->validate()){
                //验证通过保存数据库
                $model->save();
                //返回页面
                return $this->redirect(Url::to(['member/address']));
            }else{
                var_dump($model->getErrors());die;
            }
        }
        $address = Address::find()->where(['member_id'=>$model->member_id])->asArray()->all();
//        var_dump($model);die;
        return $this->render('address',['address'=>$address,'model'=>$model]);
    }
}