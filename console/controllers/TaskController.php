<?php
namespace console\controllers;
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/21
 * Time: 15:25
 */
class TaskController extends \yii\console\Controller
{
    //清除过期订单
    public function actionCancel(){
        set_time_limit(0);//临时设置脚本最大执行时间（0：无穷）
        while (true){
            $time = time();
            $sql = 'update `order` set status=0 WHERE status=1 and '.$time.'-create_time > 3600';
            \Yii::$app->db->createCommand($sql)->execute();
            //设置执行周期
            sleep(10);
            echo 'ok';
        }
        /*
         *  在命令行执行脚本方法，找到PHP文件，在其后配置参数执行。
         *  在框架中直接用yii + 路由  执行脚本。
         */
    }
}