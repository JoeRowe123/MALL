<?php

namespace frontend\controllers;



use frontend\components\Sms;
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
                    return $this->redirect(Url::to(['member/index']));
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
                $model->save(false);
                return $this->redirect(Url::to(['member/index']));
            }else{
                var_dump($model->getErrors());die;
            }
        }
        return $this->render('sign_up');
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

    public function actionIndex(){
        echo '首页';
    }
}