<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/9
 * Time: 19:32
 */

namespace backend\models;


use yii\base\Model;

class PasswordForm extends Model
{
    public $opwd;
    public $npwd;
    public $rpwd;
    public function rules(){
        return [
            [['opwd','npwd','rpwd'],'required'],
            [['opwd','npwd','rpwd'],'string','max'=>100],
            ['rpwd','compare','compareAttribute'=>'npwd'],
        ];
    }
    public function attributeLabels(){
        return [
            'opwd'=>'旧密码',
            'npwd'=>'新密码',
            'rpwd'=>'确认密码',
        ];
    }
}