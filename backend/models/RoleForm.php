<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/9
 * Time: 12:57
 */

namespace backend\models;


use yii\base\Model;

class RoleForm extends Model
{
    public $name;
    public $old_name;
    public $description;
    public $permissions;
    //场景，定义的场景需有验证规则。默认场景的规则对所有场景都生效。
    const SCENARIO_ADD = 'add';
    const SCENARIO_EDIT = 'edit';
    public function rules(){
        return [
            [['name','description'],'required'],
            ['permissions','safe'],
            ['name','validatePermission','on'=>self::SCENARIO_ADD],
            ['name','validateEditPermission','on'=>self::SCENARIO_EDIT],
        ];
    }
    //设计验证自定义验证规则的方法
    public function validatePermission(){
        $auth = \Yii::$app->authManager;
        //查询新增权限是否存在
        $role = $auth->getRole($this->name);
        if ($role){
            //提示错误
            $this->addError('name','权限已存在');
        }
    }
    public function validateEditPermission(){
        //若修改后的权限已存在则抛出错误
        $auth = \Yii::$app->authManager;
        //若未修改权限不操作
        if ($this->old_name != $this->name){
            //查询新增权限是否存在
            $role = $auth->getRole($this->name);
            if ($role){
                //提示错误
                $this->addError('name','权限已存在');
            }
        }
    }
    //修改角色
    public function update($name){
        $auth = \Yii::$app->authManager;
        //获取角色对象
        $role = $auth->getRole($name);
        //删除角色的权限
        $auth->removeChildren($role);
        //给对象赋值新的属性
        $role->name = $this->name;
        $role->description = $this->description;
        //修改角色
        if ($auth->update($name,$role)){
            //给角色分配权限
            foreach ($this->permissions as $permissionName){
                //根据权限名获取权限对象
                $permission = $auth->getPermission($permissionName);
                //给角色添加权限
                $auth->addChild($role,$permission);
            }
            return true;
        }
        return false;
    }
    public function attributeLabels(){
        return [
            'name'=>'角色',
            'description'=>'描述',
            'permissions'=>'权限',
        ];
    }

}