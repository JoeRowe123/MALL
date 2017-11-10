<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username')->textInput();
echo $form->field($model,'password_hash')->passwordInput();
echo $form->field($model,'email')->textInput();
echo $form->field($model,'role',['inline'=>true])->checkboxList($roles);
echo \yii\bootstrap\Html::submitButton('确认',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();