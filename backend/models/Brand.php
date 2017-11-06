<?php
namespace backend\models;

use yii\db\ActiveRecord;

class Brand extends ActiveRecord
{
    //保存logo信息属性
    public $logoFile;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'brand';
    }
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
            [['name','sort','intro','logo'],'required'],
            [['intro'], 'string'],
            [['sort', 'status'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['logo'], 'string', 'max' => 255],
        ];
    }
}