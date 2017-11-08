<?php
//加载webuploader文件
$this->registerCssFile('@web/css/webuploader.css');
$this->registerJsFile('@web/css/webuploader.js',[
    //该文件依赖于jQuery，确定依赖关系
    'depends'=>\yii\web\JqueryAsset::className()
]);
//生成保存文件URL
$url = \yii\helpers\Url::to(['/brand/upload']);
$this->registerJs(
    <<<JS
    $("ul li").remove();
var uploader = WebUploader.create({
   // 是否自动上传。
    auto: true,

    // swf文件路径
    swf: '/js/Uploader.swf',

    // 文件接收服务端。
    server: '{$url}',

    // 选择文件的按钮。可选。
    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
    pick: '#filePicker',
    // 只允许选择图片文件。
    accept: {
        title: 'Images',
        extensions: 'gif,jpg,jpeg,bmp,png',
        mimeTypes: 'image/jpg,image/jpeg,image/png,img/gif',

    }
});
uploader.on( 'uploadSuccess',function( file ,response) {
    //发送ajax保存图片路径
    $.post('/goods/add-gallery.html',{"goods_id":'{$id}',"path":response},function(data) {
        //将图片地址赋值给img
            $("<tr id='"+data+"'><td><img src='"+response+"' width='600px'><td><button class='btn btn-danger'>删除</button></td></td></tr>").appendTo('table');  
    });
    
});
//删除
$("table").on("click","tr button",function() {
    var tr = $(this).closest("tr");
    $.post("/goods/delete-gallery.html",{"id":tr.attr('id')},function(data) {
        if (confirm('确认删除？')){
            if (data==1){
                tr.fadeOut();
            }else {
                alert(data);
            }
        } 
    })
})

JS
);
?>
<div class="container">
    <table class="table table-responsive">
        <tr>
            <td>
                <div id="uploader-demo">
                    <!--用来存放item-->
                    <div id="fileList" class="uploader-list"></div>
                    <div id="filePicker">选择图片</div>

                </div>
            </td>
            <td><a href="/goods/list.html" class="btn btn-default btn-lg">返回</a></td>
        </tr>
        <?php foreach ($model as $v):?>
            <tr id="<?=$v->id?>">
                <td><img src="<?=$v->path?> "  alt="" width="600px"></td>
                <td><button  class="btn btn-danger">删除</button></td>
            </tr>
        <?php endforeach;?>
    </table>
</div>

