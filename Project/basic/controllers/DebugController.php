<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class DebugController extends Controller
{
    public function actionIndex()
    {
        echo "<h1>Yii2 Debug</h1>";

        echo "<h2>Yii Version:</h2>";
        echo Yii::getVersion();

        echo "<h2>Session Status:</h2>";
        echo "Session ID: " . Yii::$app->session->getId() . "<br>";
        echo "Session active: " . (Yii::$app->session->isActive ? 'yes' : 'no') . "<br>";

        echo "<h2>User Component:</h2>";
        echo "User class: " . get_class(Yii::$app->user) . "<br>";

        echo "<h2>Identity Class:</h2>";
        $identityClass = Yii::$app->user->identityClass;
        echo "Identity class: " . $identityClass . "<br>";

        if ($identityClass) {
            echo "Class exists: " . (class_exists($identityClass) ? 'yes' : 'no') . "<br>";
        }

        exit;
    }
}
