<div class="container">
    <table class="table table-hover">
        <tr>
            <th>名字</th>
            <th>上级分类</th>
            <th>简介</th>
            <th>操作</th>
        </tr>
        <?php foreach ($model as $v):?>
            <tr>
                <td><?=$v->name?></td>
                <td><?=$v->parent_id==0?'顶级分类':$v->category->name?></td>
                <td><?=$v->intro?></td>
                <td>
                    <a href="/goods-category/edit.html?id=<?=$v->id?>" class="btn btn-success btn-xs">编辑</a>
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
/**
 * @var $this \yii\web\View
 */
$this->registerJs(
        <<<JS
$("table").on('click','tr button',function () {
            if(confirm('是否删除？')){
                var tr = $(this).closest('tr');
                $.get('/goods-category/delete.html',{'id':$(this).attr('id')},function (data) {
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