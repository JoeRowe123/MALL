<?php
namespace backend\models;

use yii\db\ActiveRecord;

class Brand extends ActiveRecord
{
    //保存logo信息属性
    public $logoFile;
    //标签名称
    public function attributeLabels(){
        return [
          'name'=>'品牌',
          'intro'=>'简介',
          'sort'=>'排序',
          'status'=>'状态',
          'logoFile'=>'LOGO',
        ];
    }
    //设置前段验证规则
    public function rules(){
        return [
            //不为空
          [['name','status','intro'],'required'],
            //数据安全
          [['intro','status','sort'],'safe'],
            //验证上传文件后缀名
          ['logoFile','file','extensions'=>['jpg','png','gif'],'skipOnEmpty'=>false],
        ];
    }
}