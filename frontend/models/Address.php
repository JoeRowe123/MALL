<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/13
 * Time: 16:42
 */

namespace frontend\models;


use yii\db\ActiveRecord;

class Address extends ActiveRecord
{
    public function rules(){
        return [
            [['consignee','address','tel','status','province','city','area'],'required'],
            [['tel','member_id','status'],'integer']
        ];
    }
}