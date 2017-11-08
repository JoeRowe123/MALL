<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/8
 * Time: 10:17
 */

namespace backend\controllers;

use backend\models\LoginForm;
use backend\models\User;
use yii\captcha\CaptchaAction;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\Request;

class UserController extends Controller
{
    /**
     * 用户列表展示
     * @return string
     */
    public function actionList(){
        $query = User::find();
        $pager = new Pagination();
        //设置每页显示条数
        $pager->pageSize = 6;
        //统计数据表总条数
        $pager->totalCount = $query->count();
        //分页查询
        $model = $query->limit($pager->limit)->offset($pager->offset)->all();
        //分配数据显示视图
        return $this->render('list',['model'=>$model]);
    }

    /**
     * 用户添加
     * @return string|\yii\web\Response
     */
    public function actionAdd(){
        $model = new User();
        $request = \Yii::$app->request;
        if ($request->isPost){
            //接收表单数据
            $model->load($request->post());
            //加密密码
            $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
            //设置添加时间
            $model->created_at = time();
            //验证数据
            if ($model->validate()){
                //保存数据并跳转
                $model->save();
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect('list.html');
            }else{
                var_dump($model->getErrors());exit;
            }
        }
        //显示视图
        return $this->render('add',['model'=>$model]);

    }

    /**
     * 修改用户
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionEdit($id){
        $model = User::findOne(['id'=>$id]);
        $password = $model->password_hash;
        $request = new Request();
        if ($request->isPost){
            //接收表单数据
            $model->load($request->post());
            //验证数据
            if ($model->validate()){
                //加密密码
                //判断是否修改了密码
                if ($password == $model->password_hash){
                    //未修改密码
                    $model->password = $password;
                }else{
                    //修改密码
                    $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
                }
                //保存数据并跳转
                $model->save();
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect('list.html');
            }else{
                var_dump($model->getErrors());exit;
            }
        }
        //显示视图
        return $this->render('add',['model'=>$model]);
    }

    /**
     * 删除用户
     * @param $id
     */
    public function actionDelete($id){
        $result = User::updateAll(['status'=>0],['id'=>$id]);
        if ($result){
            echo 1;
        }else{
            echo '账号已被禁用，请勿重复操作';
        }
    }

    public function actionUpdate(){
        echo '改密码找超管';
    }

    /**
     * 登录验证
     * @return string|\yii\web\Response
     */
    public function actionLogin(){
        $model = new LoginForm();
        $request = \Yii::$app->request;
        if ($request->isPost){
            //获取登录信息
            $model->load($request->post());
            if ($model->validate()){
                //验证用户信息及密码是否正确
                if ($model->login()){
                    //验证通过，跳转列表页
                    \Yii::$app->session->setFlash('success','登录成功');
                    return $this->redirect('/user/list.html');
                }
            }
        }
        //展示登录界面
        return $this->render('login',['model'=>$model]);
    }

    /**
     * 注销
     * @return \yii\web\Response
     */
    public function actionLogout(){
        //退出登录
        \Yii::$app->user->logout();
        //跳转页面
        return $this->redirect('login.html');
    }

}