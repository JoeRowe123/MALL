<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/6
 * Time: 19:57
 */

namespace backend\models;


use yii\db\ActiveRecord;

class GoodsDayCount extends ActiveRecord
{
    public function rules(){
        return [
            [['day','count'],'required']
        ];
    }
    public function attributeLabels(){
        return [
            'day'=>'日期',
            'count'=>'商品数'
        ];
    }
}