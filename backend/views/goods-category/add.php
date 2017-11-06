<?php
/**
 * @var $this \yii\web\View
 */
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'parent_id')->hiddenInput();
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
                    $("#goodscategory-parent_id").val(id);
                }
            }
   };
   var zNodes = {$nodes};
   zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
   //展开所有节点
   zTreeObj.expandAll(true);
   //
  var node = zTreeObj.getNodeByParam("id", {$model->parent_id}, null); 
//   //选中上级分类的节点
  zTreeObj.selectNode(node);
JS
);
//展示分类的区域
echo '<div>
    <ul id="treeDemo" class="ztree"></ul>
</div>';
echo $form->field($model,'intro')->textarea(['rows'=>8]);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();