<?php
namespace backend\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class ArticleCategory extends ActiveRecord
{
    //获取id与分类的对应关系数组用于文章分类的选择
    public static function getItems(){
        return ArrayHelper::map(self::find()->asArray()->all(),'id','name');
    }
    public function rules(){
        return [
            [['name','intro','sort','status'],'required']
        ];
    }
    public function attributeLabels(){
        return [
          'name'=>'文章分类',
          'intro'=>'简介',
          'sort'=>'排序',
          'status'=>'状态',
        ];
    }
}