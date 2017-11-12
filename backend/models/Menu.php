<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/10
 * Time: 11:50
 */

namespace backend\models;


use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class Menu extends ActiveRecord
{
    public function rules(){
       return [
            [['name','parent','sort'],'required'],
            [['url',],'safe'],
        ];
    }
    public function attributeLabels(){
           return [
                'name'=>'菜单名称',
                'url'=>'路由',
                'parent'=>'上级菜单',
                'sort'=>'排序',
            ];
    }
    //获取菜单分类数组
    public static function getItems(){
        return ArrayHelper::map(self::find()->asArray()->where(['<','parent',1])->all(),'id','name');
    }
    //上级菜单与子级菜单建立关系
    public function getChildren(){
        return $this->hasMany(self::className(),['parent'=>'id']);
    }
}