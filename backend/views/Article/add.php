<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'article_category_id')->dropDownList(\backend\models\ArticleCategory::getItems());
echo $form->field($model,'status',['inline'=>1])->radioList([1=>'正常',0=>'隐藏']);
echo $form->field($model,'intro')->textarea(['rows'=>3]);
echo $form->field($model,'sort')->textInput();
echo $form->field($model,'content')->textarea(['rows'=>8]);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();
