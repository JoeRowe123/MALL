<?php
/**
 * @var $this \yii\web\View
 */
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'sort')->textInput();
echo $form->field($model,'status',['inline'=>1])->radioList([1=>'正常',0=>'隐藏']);
echo $form->field($model,'logo')->hiddenInput();
//加载webuploader文件
$this->registerCssFile('@web/css/webuploader.css');
$this->registerJsFile('@web/css/webuploader.js',[
        //该文件依赖于jQuery，确定依赖关系
        'depends'=>\yii\web\JqueryAsset::className()
]);
//生成保存文件URL
$url = \yii\helpers\Url::to(['upload']);
$this->registerJs(
    <<<JS
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

    // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
    // resize: false
    //上传成功
});
uploader.on( 'uploadSuccess', function( file ,response) {
    //$( '#'+file.id ).addClass('upload-state-done');
    // console.log(response);
    //将图片地址赋值给img
    $('#img').attr('src',response);
    //将图片地址写入logo
    $("#brand-logo").val(response);
});

JS
);
?>
    <div id="uploader-demo">
        <!--用来存放item-->
        <div id="fileList" class="uploader-list"></div>
        <div id="filePicker">选择图片</div>
    </div>
    <div><img id="img" src="<?=isset($model['logo'])?$model['logo']:''?>" width="80px" /></div>

<?php
echo $form->field($model,'intro')->textarea(['rows'=>8]);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();
