<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/9
 * Time: 11:05
 */

namespace backend\models;


use yii\base\Model;

class PermissionForm extends Model
{
    public $name;
    public $description;
    public $old_name;
    //场景，定义的场景需有验证规则。默认场景的规则对所有场景都生效。
    const SCENARIO_ADD = 'add';
    const SCENARIO_EDIT = 'edit';
    public function rules(){
        return [
            [['name','description'],'required'],
            //自定义验证规则，通过On关键字添加场景
            ['name','validatePermission','on'=>self::SCENARIO_ADD],
            ['name','validateEditPermission','on'=>self::SCENARIO_EDIT],
        ];
    }
    //设计验证自定义验证规则的方法
    public function validatePermission(){
        $auth = \Yii::$app->authManager;
        //查询新增权限是否存在
        $permission = $auth->getPermission($this->name);
        if ($permission){
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
            $permission = $auth->getPermission($this->name);
            if ($permission){
                //提示错误
                $this->addError('name','权限已存在');
            }
        }
    }
    public function update($name){
        $auth = \Yii::$app->authManager;
        $permission = $auth->getPermission($name);
        $permission->name = $this->name;
        $permission->description = $this->description;
        return $auth->update($name,$permission);
    }
    public function attributeLabels(){
        return [
            'name'=>'路由',
            'description'=>'权限名称',
        ];
    }

}