<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/9
 * Time: 10:34
 */
namespace backend\controllers;
use backend\models\PermissionForm;
use backend\models\RoleForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;
class AuthController extends Controller
{
    /**
     * 权限列表展示
     * @return string
     */
    public function actionPermissionList(){
        $auth = \Yii::$app->authManager;
        //获取所有权限记录
        $model = $auth->getPermissions();
        //分配视图显示
        return $this->render('permission_list',['model'=>$model]);
    }
    /**
     * 新增权限
     * @return string|\yii\web\Response
     */
    public function actionAddPermission(){
        $auth = \Yii::$app->authManager;
        $request = new Request();
        $model = new PermissionForm();
        //声明场景
        $model->scenario = PermissionForm::SCENARIO_ADD;
        if ($request->isPost){
            //接收表单提交的数据
            $model->load($request->post());
            if ($model->validate()){
                //验证通过
                //根据名字创建新的权限
                $permission = $auth->createPermission($model->name);
                $permission->description = $model->description;
                //添加新权限
                $auth->add($permission);
                //跳转列表页
                \Yii::$app->session->setFlash('success','新增权限成功');
                return $this->redirect('permission-list.html');
            }else{
//                var_dump($model->getErrors());exit;
            }
        }
        //显示添加权限表单
        return $this->render('add_permission',['model'=>$model]);
    }

    /**
     * 修改权限
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionEditPermission(){
        $name = \Yii::$app->request->get('name');
        $auth = \Yii::$app->authManager;
        $permission = $auth->getPermission($name);
        //若权限不存在则抛出404错误
        if ($permission==null){
            throw new NotFoundHttpException('权限不存在');
        }
        $model = new PermissionForm();
        //声明场景
        $model->scenario = PermissionForm::SCENARIO_EDIT;
        //获取旧的权限名
        $model->old_name = $name;
        $request = new Request();
        //回显权限
        $model->name = $permission->name;
        $model->description = $permission->description;
        if($request->isPost){
            $model->load($request->post());
            if ($model->validate()&&$model->update($name)){
                //验证通过
                //跳转列表页
                \Yii::$app->session->setFlash('success','修改权限成功');
                return $this->redirect('permission-list.html');
            }else{
//                var_dump($model->getErrors());exit;
            }
        }
        //显示修改表单
        return $this->render('add_permission',['model'=>$model]);
    }
    /**
     * 删除权限
     */
    public function actionDeletePermission(){
        $name = \Yii::$app->request->get('name');
        $auth = \Yii::$app->authManager;
        $permission = $auth->getPermission($name);
        if ($permission){
            $auth->remove($permission);
            echo 1;
        }else{
            echo '权限不存在，或已经删除！';
        }
    }
    /**
     * 角色管理
     */
    public function actionRoleList(){
        $auth = \Yii::$app->authManager;
        //获取所有角色信息
        $model = $auth->getRoles();
        //展示角色列表
        return $this->render('role_list',['model'=>$model]);
    }
    /**
     * 添加角色
     * @return string|\yii\web\Response
     */
    public function actionAddRole(){
        $auth = \Yii::$app->authManager;
        $request = new Request();
        $model = new RoleForm();
        //声明场景
        $model->scenario = RoleForm::SCENARIO_ADD;
        if ($request->isPost){
            //获取表单提交的数据
            $model->load($request->post());
            if ($model->validate()){
                //验证通过，创建角色
                $role = $auth->createRole($model->name);
                $role->description = $model->description;
                //保存角色
                $auth->add($role);
                //给角色分配权限
                foreach ($model->permissions as $permissionName){
                    //根据权限名获取权限对象
                    $permission = $auth->getPermission($permissionName);
                    //给角色添加权限
                    $auth->addChild($role,$permission);
                }
                //添加完成，跳转角色列表
                \Yii::$app->session->setFlash('success','添加角色成功');
                return $this->redirect(Url::to('/auth/role-list.html'));
            }else{
//                var_dump($model->getErrors());exit;
            }
        }
        //获取所有权限并展示在角色表单中
        $permissions = $auth->getPermissions();
        $permissions = ArrayHelper::map($permissions,'name','description');
        return $this->render('add_role',['model'=>$model,'permissions'=>$permissions]);
    }
    /**
     * 修改角色
     * @param $name
     * @return string|\yii\web\Response
     */
    public function actionEditRole($name){
        $auth = \Yii::$app->authManager;
        $model = new RoleForm();
        //声明场景
        $model->scenario = RoleForm::SCENARIO_EDIT;
        $model->old_name = $name;
        $request = new Request();
        //根据角色名获取角色对象
        $role = $auth->getRole($name);
        //将角色信息赋值给表单对象属性
        $model->name = $role->name;
        $model->description = $role->description;
        //根据角色名获取角色拥有的权限
        $permission = $auth->getPermissionsByRole($name);
        //将权限保存到表单对象的属性中
        foreach ($permission as $v){
            $model->permissions[] = $v->name;
            $model->permissions[] = $v->description;
        }
        if($request->isPost){
            //获取表单提交的数据
            $model->load($request->post());
            if ($model->validate()&&$model->update($name)){
                //验证通过,跳转角色列表
                \Yii::$app->session->setFlash('success','修改角色成功');
                return $this->redirect(Url::to('/auth/role-list.html'));
            }else{
//                var_dump($model->getErrors());exit;
            }
        }
        $permissions = $auth->getPermissions();
        $permissions = ArrayHelper::map($permissions,'name','description');
        return $this->render('add_role',['model'=>$model,'permissions'=>$permissions]);
    }
    /**
     * 角色删除
     */
    public function actionDeleteRole(){
        $name = \Yii::$app->request->get();
        $auth = \Yii::$app->authManager;
        $role = $auth->getRole($name);
        if ($role){
            $auth->remove($role);
            echo 1;
        }else{
            echo '该角色不存在，或已被删除！';
        }
    }
    public function behaviors(){
        return [
            'rbac'=>[
                'class'=>RbacFilter::className(),
            ]
        ];
    }
}