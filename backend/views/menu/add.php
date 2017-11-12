<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'sort')->textInput();
echo $form->field($model,'parent')->dropDownList([''=>'---请选择---',0=>'顶级菜单']+\backend\models\Menu::getItems());
echo $form->field($model,'url')->dropDownList([''=>'---请选择---']+$permissions);
echo \yii\bootstrap\Html::submitButton('确认',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();