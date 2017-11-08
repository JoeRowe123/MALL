<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username')->textInput();
echo $form->field($model,'password')->passwordInput();
echo $form->field($model,'remember')->checkbox([1=>'自动登录']);
echo $form->field($model,'captcha')->widget(
    \yii\captcha\Captcha::className(),[
        'template'=>'<div class="row"><span class="col-lg-2">{input}</span><span class="col-lg-2">{image}</span></div>'
    ]
);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();