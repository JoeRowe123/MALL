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
    <table id="myTable" class="table table-hover">
        <thead>
        <tr class="info">
            <th>菜单名称</th>
            <th>路由</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($model as $v):?>
            <tr>
                <td><?=$v->name = str_repeat('——',$v->depth*2).$v->name;?></td>
                <td><?=$v->url?></td>
                <td>
                    <a href="<?=\yii\helpers\Url::to('/menu/edit.html')?>?id=<?=$v->id?>" class="btn btn-success btn-xs">修改</a>
                    <button id="<?=$v->id?>"  class="btn btn-danger btn-xs">删除</button>
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
            if(confirm('是否删除角色？')){
                var td=$(this).closest('tr');
                $.get('<?=\yii\helpers\Url::to(['delete'])?>',{'id':$(this).attr('id')},function (data) {
                    if (data==1){
                        $(td).fadeOut();
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