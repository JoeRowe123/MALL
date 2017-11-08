<div class="container">
    <form action="/goods/list.html" class="form-inline" method="get">
        <input type="text" name="search[name]" class="form-control" placeholder="商品名">
        <input type="text" name="search[sn]" class="form-control" placeholder="货号">
        <input type="text" name="search[min]" class="form-control" placeholder="最低价格">
        <input type="text" name="search[max]" class="form-control" placeholder="最高价格">
        <button class="btn btn-primary"><span class="glyphicon glyphicon-search"></span>搜索</button>
    </form>
    <table class="table table-hover">
        <tr class="info">
            <th>商品名称</th>
            <th>LOGO</th>
            <th>货号</th>
            <th>商城价格</th>
            <th>库存</th>
            <th>是否在售</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        <?php foreach ($model as $v):?>
            <tr>
                <td><?=$v->name?></td>
                <td><img src="<?=$v->logo?>" width="70px" alt=""></td>
                <td><?=$v->sn?></td>
                <td><?=$v->shop_price?></td>
                <td><?=$v->stock?></td>
                <td><?=$v->is_on_sale==1?'在售':'下架'?></td>
                <td><?=$v->status==1?'正常':'回收站'?></td>
                <td>
                    <a href="/goods/gallery.html?id=<?=$v->id?>" class="btn btn-info btn-xs">相册</a>
                    <a href="/goods/show.html?id=<?=$v->id?>" class="btn btn-warning btn-xs">详情</a>
                    <a href="/goods/edit-goods.html?id=<?=$v->id?>" class="btn btn-success btn-xs">编辑</a>
                    <button id="<?=$v->id?>" class="btn btn-danger btn-xs">删除</button>
                </td>
            </tr>
        <?php endforeach;?>
    </table>
    <?php
    echo \yii\widgets\LinkPager::widget([
        'pagination'=>$pager
    ]);
    ?>
</div>
<?php
$this->registerJs(
    <<<JS
$("table").on('click','tr button',function () {
            if(confirm('是否删除？')){
                var tr = $(this).closest('tr');
                $.get('/goods/delete.html',{'id':$(this).attr('id')},function (data) {
                    if (data==1){
                        tr.fadeOut();
                    }else {
                        alert(data);
                    }
                })
            }
        })
JS

);

