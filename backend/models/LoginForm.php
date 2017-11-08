<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/8
 * Time: 11:35
 */

namespace backend\models;


use yii\base\Model;
use yii\web\Cookie;

class LoginForm extends Model
{
    public $username;
    public $password;
    public $captcha;
    public $remember;
    public function rules(){
        return [
            [['username','password'],'required'],
            ['captcha','captcha'],
            ['remember','integer']
        ];
    }
    public function attributeLabels(){
        return [
            'username'=>'用户名',
            'password'=>'密码',
            'captcha'=>'验证码',
            'remember'=>'自动登录'
        ];
    }
    //验证登录

    /**
     * @return bool
     */
    public function login(){
        $user = User::findOne(['username'=>$this->username]);
        if ($user){
            //有该用户，进一步验证密码
            if (\Yii::$app->security->validatePassword($this->password,$user->password_hash)) {
                //密码验证通过,登录页面
                //更新最后登录时间与ip
                $user->last_login_time = time();
                $user->last_login_ip = ip2long(\Yii::$app->request->getUserIP());
                $user->save(false);
                //是否自动登录
                if ($this->remember==1){
                    //自动登录，在表中保存author_key，同时保存到cookie中
//                    $user->auth_key = \Yii::$app->security->authKeyInfo();
//                    $cookie = new Cookie();
//                    $cookie->name = 'auth_key';
//                    $cookie->value = $user->auth_key;
                    \Yii::$app->user->login($user,3600*24*14);
                }else{
                    \Yii::$app->user->login($user);
                }
                //通过验证返回true
                return true;
            }else{
                //密码错误
                $this->addError('password','密码错误');
            }
        }else{
            //不存在输入的用户
            $this->addError('username','用户名输入不正确');
        }
        return false;
    }
}