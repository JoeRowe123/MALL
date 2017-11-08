<?php
/**
 * @var $this \yii\web\View
 */
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
//商品分类
echo $form->field($model,'goods_category_id')->hiddenInput();
//ztree添加
//$this->registerJsFile('@web/zTree/js/jquery-1.4.4.js');
$this->registerJsFile('@web/zTree/js/jquery.ztree.core.js',[
    'depends'=>\yii\web\JqueryAsset::className()
]);
//$this->registerCssFile('@web/zTree/css/demo.css');
$this->registerCssFile('@web/zTree/css/zTreeStyle/zTreeStyle.css');
//需要有顶级分类用于添加次顶级分类
$nodes = \yii\helpers\Json::encode(\yii\helpers\ArrayHelper::merge([['id'=>0,'parent_id'=>0,'name'=>'商品分类']],\backend\models\GoodsCategory::getNodes()));

$this->registerJs(
    <<<JS
    var zTreeObj;
   // zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
   var setting = {
       data: {
		simpleData: {
			enable: true,
			idKey: "id",
			pIdKey: "parent_id",
			rootPId: 0
		}
	},
	callback:{
                onClick: function(event, treeId, treeNode){
                    //获取点击节点的id
                    var id= treeNode.id;
                    //将id写入parent_id的值
                    $("#goods-goods_category_id").val(id);
                }
            }
   };
   var zNodes = {$nodes};
   zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
   //展开所有节点
   zTreeObj.expandAll(true);
   //选中节点(回显)   
   //获取节点  ,根据节点的id搜索节点
  var node = zTreeObj.getNodeByParam("id", {$model->goods_category_id}, null); 
  //选中上级分类的节点
  zTreeObj.selectNode(node);
JS
);
//展示分类的区域
echo '<div>
    <ul id="treeDemo" class="ztree"></ul>
</div>';
echo $form->field($model,'brand_id')->dropDownList(['---请选择---']+\backend\models\Brand::getItems());
echo $form->field($model,'logo')->hiddenInput();
//加载webuploader文件
$this->registerCssFile('@web/css/webuploader.css');
$this->registerJsFile('@web/css/webuploader.js',[
    //该文件依赖于jQuery，确定依赖关系
    'depends'=>\yii\web\JqueryAsset::className()
]);
//生成保存文件URL
$url = \yii\helpers\Url::to(['logo']);
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
    $("#goods-logo").val(response);
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
echo $form->field($model,'is_on_sale',['inline'=>1])->radioList([1=>'在售',0=>'下架']);
echo $form->field($model,'status',['inline'=>1])->radioList([1=>'正常',0=>'回收站']);
echo $form->field($model,'market_price')->textInput();
echo $form->field($model,'shop_price')->textInput();
echo $form->field($model,'stock')->textInput();
echo $form->field($model,'sort')->textInput();
echo $form->field($model,'intro')->widget('kucha\ueditor\UEditor',[]);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();

