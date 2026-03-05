<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\RegisterForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'except' => ['login', 'register', 'error', 'captcha'], // 'index' und 'about' entfernt
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    // Wenn nicht eingeloggt, zur Login-Seite weiterleiten
                    if ($action->id !== 'login') {
                        return $this->redirect(['site/login']);
                    }
                    return null;
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Dashboard/Hauptseite für eingeloggte Benutzer.
     * Diese Action ist NUR für eingeloggte Benutzer zugänglich.
     *
     * @return string|Response
     */
    public function actionIndex()
    {
        // Prüfen ob Benutzer eingeloggt ist
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        // Eingeloggte User werden zum homework/index weitergeleitet
        return $this->redirect(['homework/index']);
    }

    /**
     * Login action - Jetzt die Standard-Startseite für nicht eingeloggte User.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        // Wenn schon eingeloggt, zum homework/index weiterleiten
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['homework/index']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['homework/index']);
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Register action.
     *
     * @return Response|string
     */
    public function actionRegister()
    {
        // Wenn schon eingeloggt, zum homework/index weiterleiten
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['homework/index']);
        }

        $model = new RegisterForm();
        if ($model->load(Yii::$app->request->post()) && $model->register()) {
            Yii::$app->session->setFlash('success', 'Account created. You can now log in.');
            return $this->redirect(['site/login']);
        }

        return $this->render('register', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        // Nach Logout zur Login-Seite weiterleiten
        return $this->redirect(['site/login']);
    }

    /**
     * Displays contact page.
     * Diese Seite sollte nur für eingeloggte User zugänglich sein.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        // Prüfen ob Benutzer eingeloggt ist
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->contact(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Danke, Ihre Nachricht wurde gesendet.');
            } else {
                Yii::$app->session->setFlash('error', 'Nachricht konnte nicht gesendet werden. Bitte prüfen Sie Ihre Eingaben.');
            }

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     * Diese Seite ist öffentlich zugänglich (bleibt wie gehabt).
     *
     * @return string
     */
    public function actionAbout()
    {
        // About-Seite ist öffentlich, aber wir können einen Hinweis zeigen
        return $this->render('about');
    }
}
