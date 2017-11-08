<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/6
 * Time: 11:51
 */

namespace backend\models;

use yii\db\ActiveRecord;

class Goods extends ActiveRecord
{
    public $intro;
    public $condition;
    //定义规则
    public function rules(){
        return [
            [['name','stock','sort','intro','logo','market_price','shop_price','goods_category_id','brand_id'],'required'],
            [['sn','intro'], 'string'],
            [['stock','sort', 'status','is_on_sale'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['logo'], 'string', 'max' => 255],
            [['market_price','shop_price'],'number']
        ];
    }
    //设置标签
    public function attributeLabels(){
        return [
            'name'=>'商品名称',
            'sn'=>'货号',
            'logo'=>'LOGO',
            'goods_category_id'=>'商品分类',
            'brand_id'=>'品牌',
            'market_price'=>'市场价格',
            'shop_price'=>'商城价格',
            'stock'=>'库存',
            'is_on_sale'=>'是否在售',
            'status'=>'状态',
            'sort'=>'排序',
            'intro'=>'商品详情'
        ];
    }
}