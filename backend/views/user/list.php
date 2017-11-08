<div class="container">
    <table class="table table-hover">
        <tr class="info">
            <th>ID</th>
            <th>用户名</th>
            <th>邮箱</th>
            <th>最后登录ip</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        <?php foreach ($model as $v):?>
            <tr>
                <td><?=$v->id?></td>
                <td><?=$v->username?></td>
                <td><?=$v->email?></td>
                <td><?=long2ip($v->last_login_ip)?></td>
                <td class="status"><?=$v->status==1?'启用':'禁用'?></td>
                <td>
                    <a href="<?=\yii\helpers\Url::to('edit').'.html?id='.$v->id?>" class="btn btn-success btn-xs">编辑</a>
                    <button id="<?=$v->id?>" class="btn btn-danger btn-xs">禁用</button>
                </td>
            </tr>
        <?php endforeach;?>
    </table>
</div>
<script type="text/javascript">
    <?php $this->beginBlock('js')?>
    $(function () {
        $("table").on('click','tr button',function () {
            if(confirm('是否删除？')){
                var td=$(this).closest('tr').children('td').eq(4);
                $.get('<?=\yii\helpers\Url::to('delete.html')?>',{'id':$(this).attr('id')},function (data) {
                    if (data==1){
                        $(td).text('禁用');
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
