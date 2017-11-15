<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'frontend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    //设置语言
    'language'=>'zh-CN',
    //设置布局文件
    'layout'=>false,
    //默认路由
    'defaultRoute'=>'goods/index',
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            //指定实现认证接口的类
            'identityClass' => 'frontend\models\Member',
            'enableAutoLogin' => true,
            //  存入cookie
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
            //设置默认登录地址
            'loginUrl'=>['member/login']
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,//开启网址美化
            'showScriptName' => false,//是否显示脚本文件index.php
            'suffix'=>'.html',//后缀
            'rules' => [
            ],
        ],

    ],
    'params' => $params,
];
