<?php
namespace frontend\filters;

use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;

class RbacFilter extends ActionFilter{
    public function beforeAction($action){
        if (!\Yii::$app->user->can($action->uniqueId)){
            if(\Yii::$app->user->isGuest){
                //send()确保立刻跳转
                return $action->controller->redirect(\Yii::$app->user->loginUrl)->send();
            }else{
                throw new ForbiddenHttpException('对不起,您没有该操作权限');
                return false;
             }
         }
        return parent::beforeAction($action);
    }

    public function afterAction($action,$result){
        return parent::afterAction($action,$result);
    }
}