<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/8
 * Time: 10:17
 */

namespace backend\controllers;

use backend\models\LoginForm;
use backend\models\PasswordForm;
use backend\models\User;
use yii\captcha\CaptchaAction;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
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
        $auth = \Yii::$app->authManager;
        if ($request->isPost){
            //接收表单数据
            $model->load($request->post());
            //生成auth_key（自动登录）
            $model->auth_key = \Yii::$app->security->generateRandomString();
            //加密密码
            $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
            $model->created_at = time();
            //验证数据
            if ($model->validate()){
                //保存数据并跳转
                $model->save();
                //给用户分配角色
                foreach ($model->role as $roleName){
                    //根据角色名称获取角色对象
                    $role = $auth->getRole($roleName);
                    $auth->assign($role,$model->id);
                }
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(Url::to('list.html'));
            }else{
                var_dump($model->getErrors());exit;
            }
        }
        //获取所有角色
        $roles = $auth->getRoles();
        $roles = ArrayHelper::map($roles,'name','description');
        //显示视图
        return $this->render('add',['model'=>$model,'roles'=>$roles]);

    }

    /**
     * 修改用户
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionEdit($id){
        $model = User::findOne(['id'=>$id]);
        $password = $model->password_hash;
        //密码置空，修改时密码输入框为空则不修改密码。
        $model->password_hash = '';
        $request = new Request();
        $auth = \Yii::$app->authManager;
        if ($request->isPost){
            //接收表单数据
            $model->load($request->post());
            //验证数据
            if ($model->validate()){
                //更新auth_key（自动登录）
                $model->auth_key = \Yii::$app->security->generateRandomString();
                //判断是否修改了密码，输入框为空则不修改密码。
                if ($model->password_hash == ''){
                    //未修改密码
                    $model->password_hash = $password;
                }else{
                    //修改密码
                    $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
                }
                //保存数据并跳转
                $model->save();
                //删除用户拥有的角色
                $auth->revokeAll($id);
                //给用户分配角色
                foreach ($model->role as $roleName){
                    //根据角色名称获取角色对象
                    $role = $auth->getRole($roleName);
                    $auth->assign($role,$model->id);
                }
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect('list.html');
            }else{
                var_dump($model->getErrors());exit;
            }
        }
        //根据用户名获取拥有的角色
        $roles = $auth->getRolesByUser($id);
        //将角色 赋值到模型的属性
        foreach ($roles as $v){
            $model->role[] = $v->name;
            $model->role[] = $v->description;
        }
        //获取所有角色
        $roles = $auth->getRoles();
        $roles = ArrayHelper::map($roles,'name','description');
        //显示视图
        return $this->render('add',['model'=>$model,'roles'=>$roles]);
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

    public function actionUpdatePwd(){
        $model = new PasswordForm();
        $request = \Yii::$app->request;
        if ($request->isPost){
            //接收表单提交数据
            $model->load($request->post());
            if ($model->validate()){
                //通过验证
                //获取hash加密的旧密码
                $pwd_hash = \Yii::$app->user->identity->password_hash;
                if (\Yii::$app->security->validatePassword($model->opwd,$pwd_hash)){
                    //若验证旧密码通过，修改密码
                    User::updateAll(
                        ['password_hash'=>\Yii::$app->security->generatePasswordHash($model->npwd)],
                        ['id'=>\Yii::$app->user->id]
                    );
                    //修改后退出登录
                    \Yii::$app->user->logout();
                    //跳转到登录页面
                    \Yii::$app->session->setFlash('success','修改密码成功，请重新登录');
                    return $this->redirect(Url::to('/user/login.html'));
                }else{
                    //验证失败，显示提示消息
                    $model->addError('opwd','旧密码错误！');
                }
            }
        }
        //展示修改密码视图
        return $this->render('password',['model'=>$model]);
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

    /**
     * 过滤
     * @return array
     */
    public function behaviors(){
        return [
            'acf'=>[
                'class'=>AccessControl::className(),
                'only'=>['list','password','add'],
                'rules'=>[
                    [
                        'allow'=>true,//允许
//                        'actions'=>['/user/list','/user/add'],
                        'roles'=>['@'],//角色 ?未登录 @已登录
                    ],
                    [
                        'allow'=>true,
                        'actions'=>['/user/list','/user/add','/user/delete'],
                        'matchCallback'=>function(){
                            return \Yii::$app->user->identity->username=='admin';
                        }
                    ]
                ]
            ]
        ];
    }
}