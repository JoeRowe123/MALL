<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/17
 * Time: 14:11
 */

namespace frontend\models;

use yii\db\ActiveRecord;

class Order extends ActiveRecord
{
    public $address_id;
    //模拟配送方式数据库
    public static $deliveries = [
        1=>['普通快递送货上门','10','每张订单不满499.00元,运费15.00元, 订单4...'],
        2=>['特快专递','40','每张订单不满499.00元,运费15.00元, 订单4...'],
        3=>['加急快递送货上门','40','每张订单不满499.00元,运费15.00元, 订单4...'],
        4=>['平邮','10','每张订单不满499.00元,运费15.00元, 订单4...'],
    ];
    public static $payment = [
        1=>['货到付款','送货上门后再收款，支持现金、POS机刷卡、支票支付'],
        2=>['在线支付','即时到帐，支持绝大数银行借记卡及部分银行信用卡'],
        3=>['上门自提','自提时付款，支持现金、POS刷卡、支票支付'],
        4=>['邮局汇款','通过快钱平台收款 汇款后1-3个工作日到账'],
    ];
    public function rules(){
        return [
            [['address_id','member_id','name','province','city','area','address','tel', 'delivery_id','delivery_name','delivery_price','payment_id', 'payment_name','total','status','trade_no','create_time'],'required']
        ];
    }
    public function getOrderGoods(){
        return $this->hasMany(OrderGoods::className(),['order_id'=>'id']);
    }
}