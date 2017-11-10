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
    public function rules(){
        return [
            [['name','description'],'required'],
        ];
    }
    public function attributeLabels(){
        return [
            'name'=>'路由',
            'description'=>'权限名称',
        ];
    }

}