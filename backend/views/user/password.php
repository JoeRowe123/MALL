<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'opwd')->passwordInput();
echo $form->field($model,'npwd')->passwordInput();
echo $form->field($model,'rpwd')->passwordInput();
echo \yii\bootstrap\Html::submitButton('确认',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();