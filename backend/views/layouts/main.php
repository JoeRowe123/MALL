<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title>商城管理系统</title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => '商城管理',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    //菜单栏
    $menuItems = [
//        ['label' => 'Home', 'url' => ['/user/list']],
        ['label'=>'管理员','items'=>[
            ['label'=>'管理员列表','url'=>['/user/list']],
            ['label'=>'添加管理员','url'=>['/user/add']],
        ]],
        ['label'=>'管理员权限管理','items'=>[
            ['label'=>'权限列表','url'=>[\yii\helpers\Url::to('/auth/permission-list')]],
            ['label'=>'添加权限','url'=>[\yii\helpers\Url::to('/auth/add-permission')]],
            ['label'=>'角色列表','url'=>[\yii\helpers\Url::to('/auth/role-list')]],
            ['label'=>'添加角色','url'=>[\yii\helpers\Url::to('/auth/add-role')]],
        ]],
        ['label'=>'商品管理','items'=>[
                ['label'=>'商品分类列表','url'=>['/goods-category/list']],
                ['label'=>'添加商品分类','url'=>['/goods-category/add-category']],
                ['label'=>'商品列表','url'=>['/goods/list']],
                ['label'=>'添加商品','url'=>['/goods/add-goods']],
        ]],
        ['label'=>'品牌管理','items'=>[
            ['label'=>'商品分类列表','url'=>['/brand/list']],
            ['label'=>'品牌添加','url'=>['/brand/add-brand']],
        ]],
        ['label'=>'菜单管理','items'=>[
            ['label'=>'商品分类列表','url'=>['/menu/list']],
            ['label'=>'菜单添加','url'=>['/menu/add']],
        ]],
        ['label'=>'文章管理','items'=>[
            ['label'=>'文章分类列表','url'=>['/article-category/list']],
            ['label'=>'添加文章分类','url'=>['/article-category/add']],
            ['label'=>'文章列表','url'=>['/article/list']],
            ['label'=>'添加文章','url'=>['/article/add']],
        ]],
        ['label'=>'修改密码','url'=>[\yii\helpers\Url::to('/user/update-pwd')]],
    ];
    if (Yii::$app->user->isGuest) {
        $menuItems = [
                //访客模式展示菜单
        ];
        $menuItems[] = ['label' => '登录', 'url' => ['/user/login']];
    } else {
        $menuItems = Yii::$app->user->identity->Menu;
        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                '注销 (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
