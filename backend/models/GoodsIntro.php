<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/6
 * Time: 15:04
 */

namespace backend\models;


use yii\db\ActiveRecord;

class GoodsIntro extends ActiveRecord
{
    public function getGoods(){
        return $this->hasOne(Goods::className(),['id'=>'goods_id']);
    }
    public function rules(){
        return [
            [['content','goods_id'],'required']
        ];
    }
    public function attributeLabels(){
        return [
            'content'=>'商品详情'
        ];
    }
}