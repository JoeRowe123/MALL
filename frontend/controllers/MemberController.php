<?php

namespace frontend\controllers;



use backend\models\Goods;
use frontend\components\Sms;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\LoginForm;
use frontend\models\Member;
use frontend\models\Order;
use frontend\models\OrderGoods;
use yii\db\Exception;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\Request;

class MemberController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * 登录验证
     * @return string|\yii\web\Response
     */
    public function actionLogin(){
        $model = new LoginForm();
        $request = \Yii::$app->request;
        if ($request->isPost){
            //获取登录信息
            $model->load($request->post(),'');
            if ($model->validate()){
                //验证用户信息及密码是否正确
                if ($model->login()){
                    //验证通过，跳转列表页
                    return $this->redirect(Url::to(['goods/index']));
                }
            }else{
                var_dump($model->getErrors());exit;
            }
        }
        //展示登录界面
        return $this->render('login');
    }

    /**
     * 注册
     * @return string|\yii\web\Response
     */
    public function actionSignUp(){
        $model = new Member();
        $request = new Request();
        if ($request->isPost){
            $model->load($request->post(),'');
            //生成auth_key（自动登录）
            $model->auth_key = \Yii::$app->security->generateRandomString();
            //加密密码
            $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
            if ($model->validate()){
//                var_dump($model);die;
                $model->save(false);
                return $this->redirect(Url::to(['goods/index']));
            }else{
                var_dump($model->getErrors());die;
            }
        }
        return $this->render('sign_up');
    }

    public function actionLogout(){
        //退出登录
        \Yii::$app->user->logout();
        //跳转页面
        return $this->redirect(Url::to(['member/login']));
    }
    /**
     * 短信验证码
     */
    public function actionSms(){
        $tel = $_POST['tel'];
        $code = rand(100000,999999);
        $response = Sms::sendSms(
            "夏天安静了", // 短信签名
            "SMS_109365465", // 短信模板编号
            $tel, // 短信接收者
            Array(  // 短信模板中字段的值
                "code"=>$code,
            )
        );
        if ($response->Code == 'OK'){
            //将验证码存入session
            $session = \Yii::$app->session;
            $session->open();
            //根据手机号保存验证码
            $session->set('code_'.$tel,$code);
            return "success";
        }else{
            return 'failed';
        }
    }

    /**
     * 前端验证手机验证码
     * @param $tel
     * @param $captcha
     * @return string
     */
    public function actionCheckCaptcha($tel,$captcha){
        $code = \Yii::$app->session->get('code_'.$tel);
//        var_dump($code);die;
        if ($code == $captcha){
            return 'true';
        }else{
            return 'false';
        }
    }
    /**
     * 验证注册的用户名唯一
     * @param $username
     * @return string
     */
    public function actionCheckName($username){
        $result = Member::findOne(['username'=>$username]);
        if($result){
            return 'false';
        }
        return 'true';
    }

    /**
     * 添加地址
     * @return string|\yii\web\Response
     */
    public function actionAddress(){
        $model = new Address();
        $request = \Yii::$app->request;
        //获取当前登录用户的id
        $model->member_id = \Yii::$app->user->id;
        if ($request->isPost){
            $model->load($request->post(),'');
            $model->status = $model->status?1:0;
            //若新增地址为默认地址则之前默认地址状态改为0
            if ($model->status == 1){
                Address::updateAll(['status'=>0],['status'=>1]);
            }
            if ($model->validate()){
                //验证通过保存数据库
                $model->save();
                //返回页面
                return $this->redirect(Url::to(['member/address']));
            }else{
                var_dump($model->getErrors());die;
            }
        }
        $address = Address::find()->where(['member_id'=>$model->member_id])->asArray()->all();
        return $this->render('address',['address'=>$address,'model'=>$model]);
    }

    /**
     * 删除地址
     * @param $id
     */
    public function actionDeleteAddress($id){
        $address = Address::findOne(['id'=>$id]);
        if ($address){
            $address->delete();
            echo "success";
        }else{
            echo "地址不存在，或已被删除";
        }
    }

    /**
     * 设置默认地址
     * @param $id
     * @return \yii\web\Response
     */
    public function actionStatus($id){
        Address::updateAll(['status'=>0],['status'=>1]);
        Address::updateAll(['status'=>1],['id'=>$id]);
        return $this->redirect(Url::to(['member/address']));
    }

    /**
     * 修改地址
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionUpdateAddress($id){
        $model = Address::findOne(['id'=>$id]);
        $request = \Yii::$app->request;
        if ($request->isPost){
            $model->load($request->post(),'');
            $model->status = $model->status?1:0;
            //若新增地址为默认地址则之前默认地址状态改为0
            if ($model->status == 1){
                Address::updateAll(['status'=>0],['status'=>1]);
            }
            if ($model->validate()){
                //验证通过保存数据库
                $model->save();
                //返回页面
                return $this->redirect(Url::to(['member/address']));
            }else{
                var_dump($model->getErrors());die;
            }
        }
        $address = Address::find()->where(['member_id'=>$model->member_id])->asArray()->all();
//        var_dump($model);die;
        return $this->render('address',['address'=>$address,'model'=>$model]);
    }
    /**
     * 购物车
     * @return string
     */
    //定义查询cookie中购物车的方法
    private function getCart(){
        $cookies = \Yii::$app->request->cookies;
        $carts = $cookies->getValue('carts');
        if ($carts){
            $carts = unserialize($carts);
        }else{
            $carts = [];
        }
        return $carts;
    }
    //展示购物车里的商品
    public function actionCartList(){
        //访客模式
        if (\Yii::$app->user->isGuest){
            //查询cookie中的购物车里的数据
            $carts = $this->getCart();
//            var_dump($carts);die;
            //获取购物车里的商品id
            $ids = array_keys($carts);
            //根据商品id查出相应商品信息
            $goods = Goods::find()->where(['in','id',$ids])->all();
            //展示购物车页面
            return $this->render('g_cart',['carts'=>$carts,'goods'=>$goods]);
        }
        //登录模式
        else{
            //查询cookie购物车中商品
            $carts = $this->getCart();
            //将cookie购物车中的商品保存到数据库
            foreach ($carts as $k=>$v){
                $cart = Cart::find()->andWhere(['member_id'=>\Yii::$app->user->id])->andWhere(['goods_id'=>$k])->one();
                if ($cart){
                    //若该用户已有该商品则修改商品数量
                    $total = $cart->amount + $v;
                    Cart::updateAll(['amount'=>$total],['id'=>$cart->id]);
                }
                else{
                    $model = new Cart();
                    //若该用户还没有这个商品，则新增
                    $model->member_id = \Yii::$app->user->id;
                    $model->amount = $v;
                    $model->goods_id = $k;
                    if ($model->validate()){
                        $model->save();
                    }else{
                        var_dump($model->getErrors());exit;
                    }
                }
            }
            //cookie中商品添加到数据库后删除cookie
            $cookies = \Yii::$app->response->cookies;
            $cookies->remove('carts');

            //展示购物车内的所有商品
            $goods = Cart::find()->where(['member_id'=>\Yii::$app->user->id])->all();
            return $this->render('cart',['goods'=>$goods]);
        }
    }
    //添加商品
    public function actionCart(){
        //未登录状态
        if (\Yii::$app->user->isGuest){
            //查询cookie中购物车信息
            $carts = $this->getCart();
            //获取表传递的商品信息
            $goods_id = \Yii::$app->request->post('goods_id');
            $amount = \Yii::$app->request->post('amount');
            //添加商品时，判断购物车中是否已存在该商品
            if(array_key_exists($goods_id,$carts)){
                //已存在则累计商品数量
                $carts[$goods_id] += $amount;
            }else{
                //购物车中不存在则新增商品,商品保存类型以商品id做键，商品数量当值的关联数组。[11=>1] --- id为11的商品1件
                $carts[$goods_id] = $amount;
            }
            //执行写操作的cookie
            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie();
            //将商品信息赋值给cookie对象的属性
            $cookie->name = 'carts';
            $cookie->value = serialize($carts);
            $cookie->expire = time()+3600*24*7;
            //保存cookie
            $cookies->add($cookie);
            return $this->redirect(['cart-list']);
        }
        else{
            $model = new Cart();
            $request = \Yii::$app->request;
            //接收表单提交的数据
            $model->member_id = \Yii::$app->user->id;
            //查询商品是否存在
            $model->load($request->post(),"");
            //查询当前登录用户所添加商品是否已在购物车
            $old = Cart::find()->andWhere(['goods_id'=>$model->goods_id])->andWhere(['member_id'=>$model->member_id])->one();
            //给用户id赋值
            if ($model->validate()){
                //验证通过保存数据
                //判断该商品数量，新增商品添加，已有商品修改数量
                if (!$old){
                    $model->save();
                }else{
                    //已存有当前商品，修改商品数量
                    $total = $old->amount + $model->amount;
                    Cart::updateAll(['amount'=>$total],['id'=>$old->id]);
                }
            }else{
                var_dump($model->getErrors());exit;
            }
            return $this->redirect(['cart-list']);
        }
    }

    /**
     * 修改购物车商品数量
     */
    public function actionChangeAmount(){
        //获取修改的商品的id和数量
        $id = \Yii::$app->request->post('id');
        $amount = \Yii::$app->request->post('amount');
        if (\Yii::$app->user->isGuest){
            //访客模式，获取操作类型
            $type = \Yii::$app->request->get('type');
            switch ($type){
                case 'change':
                    //取出cookie中的购物车信息
                    $carts = $this->getCart();
                    //修改购物车商品数量
                    $carts[$id] = $amount;
                    //执行写操作的cookie
                    $cookies = \Yii::$app->response->cookies;
                    $cookie = new Cookie();
                    //将商品信息赋值给cookie对象的属性
                    $cookie->name = 'carts';
                    $cookie->value = serialize($carts);
                    //保存cookie
                    $cookies->add($cookie);
                    break;
                case 'del':
                    $goods_id = \Yii::$app->request->get('goods_id');
                    $carts = $this->getCart();
                    unset($carts[$goods_id]);
                    $cookies = \Yii::$app->response->cookies;
                    $cookie = new Cookie();
                    //将商品信息赋值给cookie对象的属性
                    $cookie->name = 'carts';
                    $cookie->value = serialize($carts);
                    //保存cookie
                    $cookies->add($cookie);
                    echo 'success';
                    break;
            }
        }else{
            //登录模式，修改数据库商品数量
            Cart::updateAll(['amount'=>$amount],['id'=>$id]);
        }
    }

    /**
     * 删除购物车商品
     * @param $id
     * @return \yii\web\Response
     */
    public function actionCartDel(){
        $id = \Yii::$app->request->get('id');
        $goods = Cart::findOne(['id'=>$id]);
        $goods->delete();
        echo 'success';
    }
    //确认订单
    public function actionSettlement(){
        //访客确认订单时提示登录
        if (\Yii::$app->user->isGuest){
            return $this->redirect(['login']);
        }else{
            $member_id = \Yii::$app->user->id;
            //从地址表获取地址信息
            $address = Address::find()->where(['member_id'=>$member_id])->all();
            //从购物车中查出所有商品
            $goods = Cart::find()->where(['member_id'=>$member_id])->all();
            return $this->render('check_order',['address'=>$address,'goods'=>$goods]);
        }
    }
    //提交订单
    public function actionOrder(){
        $model = new Order();
        $request= \Yii::$app->request;
        if ($request->isPost){
            //获取表单提交的数据
            //将提交数据赋值给订单对象
            $model->load($request->post(),'');
//            var_dump($model->address_id);die;
            //根据地址id获取地址信息
            $address_detail = Address::findOne(['id'=>$model->address_id]);
            //将获取的地址信息赋值给订单对象。
            $model->name = $address_detail->consignee;
            $model->province = $address_detail->province;
            $model->city = $address_detail->city;
            $model->area = $address_detail->area;
            $model->address = $address_detail->address;
            $model->tel = $address_detail->tel;
            $model->member_id = \Yii::$app->user->id;
            //配送方式
            $model->delivery_name = Order::$deliveries[$model->delivery_id][0];
            $model->delivery_price = Order::$deliveries[$model->delivery_id][1];
            //支付方式
            $model->payment_name = Order::$payment[$model->payment_id][0];
            //第三方支付号
            $model->trade_no = \Yii::$app->security->generateRandomString();
            $model->status = 1;
            $model->create_time = time();
//            var_dump($model);die;
            $model->total = $model->delivery_price;
            //开启事务
            $transaction = \Yii::$app->db->beginTransaction();
            try{
                if($model->save()){
                    //将购物车中商品同订单加入订单商品表
                    //计算商品总金额
                    //查出登录用户购物车所有商品
                    $carts = Cart::findAll(['member_id'=>$model->member_id]);
                    //计算商品总金额
                    foreach ($carts as $cart){
                        if ($cart->amount > $cart->goods->stock){
                            throw new Exception($cart->goods->name,'商品不足');
                        }
                        $order_goods = new OrderGoods();
                        $order_goods->order_id = $model->id;
                        $order_goods->goods_id = $cart->goods_id;
                        $order_goods->goods_name = $cart->goods->name;
                        $order_goods->logo = $cart->goods->logo;
                        $order_goods->price = $cart->goods->shop_price;
                        $order_goods->amount = $cart->amount;
                        $order_goods->total = $cart->amount*$order_goods->price;
//                        var_dump($order_goods);die;
                        $order_goods->save();
                        //总金额
                        $model->total += $cart->amount*$cart->goods->shop_price;
                        //修改商品库存
                        $stock = $cart->goods->stock - $cart->amount;
                        Goods::updateAll(['stock'=>$stock],['id'=>$cart->goods_id]);
                        //商品提交订单完成删除购物车商品
                        $cart->delete();
                    }
//                    Cart::deleteAll(['member_id'=>$model->member_id]);
                    $model->save();
                }
                $transaction->commit();
            }catch (Exception $e){
                //出错回滚
                $transaction->rollBack();
//                var_dump($e);exit;
                //跳转回购物车
                return $this->redirect(['cart-list']);
            }
            return $this->redirect(['notice']);

        }
        //订单列表页
        $orders = Order::findAll(['member_id'=>\Yii::$app->user->id]);
        //获取订单商品的logo
        $logo = [];
        foreach ($orders as $order){
            $logo[$order->id] = OrderGoods::find()->where(['order_id'=>$order->id])->limit(3)->asArray()->all();
        }
//        var_dump($logo);die;
        return $this->render('order',['orders'=>$orders,'logo'=>$logo]);
    }
    //提交成功提示页
    public function actionNotice(){
        return $this->render('notice');
    }
}