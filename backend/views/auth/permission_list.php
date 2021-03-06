<?php
/**
 * @var $this \yii\web\View
 */
$this->registerCssFile('@web/DataTables/css/jquery.dataTables.css');
$this->registerJsFile('@web/DataTables/js/jquery.dataTables.js',[
    //该文件依赖于jQuery，确定依赖关系
    'depends'=>\yii\web\JqueryAsset::className()
]);
?>
<div class="container">
    <table id="myTable" class="table table-hover display">
        <thead>
            <tr class="info">
                <th>路由</th>
                <th>权限名称</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($model as $v):?>
                <tr>
                    <td><?=$v->name?></td>
                    <td><?=$v->description?></td>
                    <td>
                        <a href="<?=\yii\helpers\Url::to('/auth/edit-permission.html')?>?name=<?=$v->name?>" class="btn btn-success btn-xs">修改</a>
                        <button id="<?=$v->name?>"  class="btn btn-danger btn-xs">删除</button>
                    </td>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</div>
<script>
    <?php $this->beginBlock('js')?>
    $(function () {
        $('#myTable').DataTable();
        $("table").on('click','tr button',function () {
            if(confirm('是否删除？')){
                var td=$(this).closest('tr');
                $.get('<?=\yii\helpers\Url::to('delete-permission.html')?>',{'name':$(this).attr('id')},function (data) {
                    if (data==1){
                        $(td).remove();
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
