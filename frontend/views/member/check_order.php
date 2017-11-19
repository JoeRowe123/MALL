<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>填写核对订单信息</title>
    <link rel="stylesheet" href="/css/base.css" type="text/css">
    <link rel="stylesheet" href="/css/global.css" type="text/css">
    <link rel="stylesheet" href="/css/header.css" type="text/css">
    <link rel="stylesheet" href="/css/fillin.css" type="text/css">
    <link rel="stylesheet" href="/css/footer.css" type="text/css">

    <script type="text/javascript" src="/js/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" src="/js/cart2.js"></script>

</head>
<body>
<!-- 顶部导航 start -->
<div class="topnav">
    <div class="topnav_bd w990 bc">
        <div class="topnav_left">

        </div>
        <div class="topnav_right fr">
            <ul>
                <li>您好，欢迎来到京西！[<a href="login.html">登录</a>] [<a href="register.html">免费注册</a>] </li>
                <li class="line">|</li>
                <li>我的订单</li>
                <li class="line">|</li>
                <li>客户服务</li>

            </ul>
        </div>
    </div>
</div>
<!-- 顶部导航 end -->

<div style="clear:both;"></div>

<!-- 页面头部 start -->
<div class="header w990 bc mt15">
    <div class="logo w990">
        <h2 class="fl"><a href="<?=\yii\helpers\Url::to(['goods/index'])?>"><img src="/images/logo.png" alt="京西商城"></a></h2>
        <div class="flow fr flow2">
            <ul>
                <li>1.我的购物车</li>
                <li class="cur">2.填写核对订单信息</li>
                <li>3.成功提交订单</li>
            </ul>
        </div>
    </div>
</div>
<!-- 页面头部 end -->

<div style="clear:both;"></div>

<!-- 主体部分 start -->
<div class="fillin w990 bc mt15">
    <div class="fillin_hd">
        <h2>填写并核对订单信息</h2>
    </div>
    <form action="<?=\yii\helpers\Url::to(['member/order'])?>" method="post">
    <div class="fillin_bd">
        <!-- 收货人信息  start-->
        <div class="address">
            <h3>收货人信息</h3>
            <div class="address_info">
                <?php foreach ($address as $v):?>
                <p><input type="radio" name="address_id" value="<?=$v->id?>" /><?=$v->consignee?> <?=$v->tel?>  <?=$v->province?> <?=$v->city?> <?=$v->area?> <?=$v->address?> </p>
                <?php endforeach;?>
            </div>


        </div>
        <!-- 收货人信息  end-->

        <!-- 配送方式 start -->
        <div class="delivery">
            <h3>送货方式 </h3>
            <div class="delivery_select">
                <table>
                    <tbody>
                    <?php foreach (\frontend\models\Order::$deliveries as $k=>$delivery):?>
                    <tr>
                        <td>
                            <input type="radio" name="delivery_id" value="<?=$k?>"/><span class="deName"><?=$delivery[0]?></span>
                        </td>
                        <td>￥<span class="price"><?=$delivery[1]?>.00</span></td>
                        <td><?=$delivery[2]?></td>
                    </tr>
                    <?php endforeach;?>
                    </tbody>
                    <thead>
                    <tr>
                        <th class="col1">送货方式</th>
                        <th class="col2">运费</th>
                        <th class="col3">运费标准</th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
        <!-- 配送方式 end -->

        <!-- 支付方式  start-->
        <div class="pay">
            <h3>支付方式 </h3>

            <div class="pay_select">
                <table>
                    <?php foreach (\frontend\models\Order::$payment as $k=>$v):?>
                    <tr>
                        <td class="col1"><input type="radio" name="payment_id" value="<?=$k?>"/><span class="away"><?=$v[0]?></span></td>
                        <td class="col2"><?=$v[1]?></td>
                    </tr>
                    <?php endforeach;?>
                </table>

            </div>
        </div>
        <!-- 支付方式  end-->

        <!-- 发票信息 end-->

        <!-- 商品清单 start -->
        <div class="goods">
            <h3>商品清单</h3>
            <table>
                <thead>
                <tr>
                    <th class="col1">商品</th>
                    <th class="col3">价格</th>
                    <th class="col4">数量</th>
                    <th class="col5">小计</th>
                </tr>
                </thead>
                <tbody>
                <?php $amount = 0;$total = 0;foreach ($goods as $v):?>
                <tr>
                    <td class="col1"><a href="<?=\yii\helpers\Url::to(['goods/goods','id'=>$v->goods_id])?>"><img src="<?=Yii::$app->params['backend_domain'].$v->goods->logo?>" alt="" /></a>  <strong><a href="<?=\yii\helpers\Url::to(['goods/goods','id'=>$v->goods_id])?>"><?=$v->goods->name?></a></strong></td>
                    <td class="col3">￥<?=$v->goods->shop_price?>.00</td>
                    <td class="col4"> <?=$v->amount?></td>
                    <td class="col5"><span>￥<?=$v->amount*$v->goods->shop_price?>.00</span></td>
                </tr>
                <?php $amount+=$v->amount;$total += $v->amount*$v->goods->shop_price;endforeach;?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="5">
                        <ul>
                            <li>
                                <span><?=$amount?> 件商品，总商品金额：</span>
                                <em>￥<?=$total?>.00</em>
                            </li>
<!--                            <li>-->
<!--                                <span>返现：</span>-->
<!--                                <em>-￥240.00</em>-->
<!--                            </li>-->
                            <li>
                                <span>运费：</span>
                                <em>￥10.00</em>
                            </li>
                            <li>
                                <span>应付总额：</span>
                                <em>￥<?=$total?>.00</em>
                            </li>
                        </ul>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
        <!-- 商品清单 end -->

    </div>

    <div class="fillin_ft">
<!--        <button type="submit" id="submit">提交订单</button>-->
        <a id="submit"><button type="submit" style="cursor: pointer;border: none;background-color: rgba(255,255,255,0); width: 135px;height: 36px"></button></a>
        <p>应付总额：<strong>￥<?=$total?>.00元</strong></p>
    </div>
    </form>
</div>
<!-- 主体部分 end -->

<div style="clear:both;"></div>
<!-- 底部版权 start -->
<div class="footer w1210 bc mt15">
    <p class="links">
        <a href="">关于我们</a> |
        <a href="">联系我们</a> |
        <a href="">人才招聘</a> |
        <a href="">商家入驻</a> |
        <a href="">千寻网</a> |
        <a href="">奢侈品网</a> |
        <a href="">广告服务</a> |
        <a href="">移动终端</a> |
        <a href="">友情链接</a> |
        <a href="">销售联盟</a> |
        <a href="">京西论坛</a>
    </p>
    <p class="copyright">
        © 2005-2013 京东网上商城 版权所有，并保留所有权利。  ICP备案证书号:京ICP证070359号
    </p>
    <p class="auth">
        <a href=""><img src="/images/xin.png" alt="" /></a>
        <a href=""><img src="/images/kexin.jpg" alt="" /></a>
        <a href=""><img src="/images/police.jpg" alt="" /></a>
        <a href=""><img src="/images/beian.gif" alt="" /></a>
    </p>
</div>
<!-- 底部版权 end -->
<script>
//    $(function () {
//        $("#submit").click(function () {
//            //配送方式选择器
//            var delivery = $('input:radio[name="delivery"]:checked');
//            var payment =  $('input:radio[name="pay"]:checked');
//            $.post("<?//=\yii\helpers\Url::to(['member/order'])?>//",{
//                'address_id':$('input:radio[name="address_id"]:checked').val(),
//                'delivery_id':delivery.val(),
//                'delivery_name':delivery.parent().find(".deName").text(),
//                'delivery_price':delivery.closest('tr').find('.price').text(),
//                'payment_id':payment.val(),
//                'payment_name':payment.closest('tr').find('.away').text()
//            })
//        })
//    })
</script>
</body>
</html>

