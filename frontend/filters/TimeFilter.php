<?php
namespace frontend\filters;

use yii\base\ActionFilter;
use yii\web\HttpException;

class TimeFilter extends ActionFilter{
    //操作执行前
    public function beforeAction($action)
    {
        //拦截操作，禁止当前操作继续执行
        if ($action->uniqueId/*当前路由*/ == 'manager/delete') {
            throw new HttpException(403,'没有该操作权限');
            return false;
        }
        return parent::beforeAction($action);
    }

    //操作执行后
    public function afterAction($action,$result){
        return parent::afterAction($action,$result);
    }
}