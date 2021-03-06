<div class="container">
    <table class="table table-hover">
        <tr class="info">
            <th>文章分类</th>
            <th>状态</th>
            <th>简介</th>
            <th>操作</th>
        </tr>
        <?php foreach ($model as $v):?>
            <tr>
                <td><?=$v->name?></td>
                <td><?=$v->status==1?'正常':'隐藏'?></td>
                <td><?=$v->intro?></td>
                <td><a href="/article-category/edit.html?id=<?=$v->id?>" class="btn btn-success btn-xs">编辑</a>&emsp;
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
<script type="text/javascript">
    <?php $this->beginBlock('js')?>
    $(function () {
        $("table").on('click','tr button',function () {
            if(confirm('是否删除？')){
                var tr = $(this).closest('tr');
                $.get('/article-category/delete.html',{'id':$(this).attr('id')},function (data) {
                    if (data==1){
                        tr.fadeOut();
                    }else {
                        alert(data);
                    }
                })
            }
        })
    })
    <?php $this->endBlock()?>
    <?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</script>

