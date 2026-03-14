<?php

use app\assets\AppAsset;
use app\models\User;
use app\widgets\Alert;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);

$isGuest = Yii::$app->user->isGuest;
$identity = Yii::$app->user->identity;
$logoutLabel = 'Logout';
$isAdmin = false;
$brandUrl = ['/site/login'];

if (!$isGuest && $identity !== null) {
    $logoutLabel = 'Logout (' . $identity->getUsername() . ')';
    $isAdmin = method_exists($identity, 'getRole')
        && strtolower((string)$identity->getRole()) === User::ROLE_ADMIN;
    $brandUrl = ['/homework/index'];
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => $brandUrl,
        'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top'],
    ]);

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => [
            ['label' => 'My Homework', 'url' => ['/homework/index'], 'visible' => !$isGuest],
            ['label' => 'Admin', 'url' => ['/admin/users'], 'visible' => $isAdmin],
            ['label' => 'Login', 'url' => ['/site/login'], 'visible' => $isGuest],
            ['label' => 'Register', 'url' => ['/site/register'], 'visible' => $isGuest],
            [
                'label' => $logoutLabel,
                'url' => ['/site/logout'],
                'visible' => !$isGuest,
                'linkOptions' => ['data-method' => 'post'],
            ],
        ],
    ]);
    NavBar::end();
    ?>
</header>

<main id="main" class="flex-shrink-0" role="main">
    <div class="container">
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer id="footer" class="mt-auto py-3 bg-light">
    <div class="container">
        <div class="row text-muted">
            <div class="col-md-6 text-center text-md-start">&copy; Study Organizer <?= date('Y') ?></div>
            <div class="col-md-6 text-center text-md-end"><?= Yii::powered() ?></div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
