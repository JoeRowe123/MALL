<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/15
 * Time: 16:57
 */

namespace frontend\models;

use yii\db\ActiveRecord;

class Cart extends ActiveRecord
{
    public function rules(){
        return [
            [['goods_id','amount','member_id'],'required'],
            [['goods_id','amount','member_id'],'integer']
        ];
    }
    //与商品建立关系
    public function getGoods(){
        return $this->hasOne(\backend\models\Goods::className(),['id'=>'goods_id']);
    }
}