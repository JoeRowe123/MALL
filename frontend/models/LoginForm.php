<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/12
 * Time: 19:23
 */

namespace frontend\models;


use yii\base\Model;

class LoginForm extends Model
{
    public $username;
    public $password;
    public $remember;
    public function rules(){
        return [
            [['username','password'],'required'],
            ['remember','safe']
        ];
    }
    public function login(){
        $user = Member::findOne(['username'=>$this->username]);
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
                    \Yii::$app->user->login($user,3600*24*14);
                }else{
                    \Yii::$app->user->login($user);
                }
                //通过验证返回true
                return true;
            }else{
                //密码错误
                return false;
            }
        }else{
            //不存在输入的用户
            return false;
        }
        return false;
    }
}