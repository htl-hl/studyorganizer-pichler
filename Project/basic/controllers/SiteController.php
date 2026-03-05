<?php

namespace app\controllers;

use app\models\Forums;
use app\models\Produkte;
use Yii;
use yii\base\DynamicModel;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\SignupForm;
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
                'only' => ['logout', 'contact'], // 'contact' hinzugefügt
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['contact'],
                        'allow' => true,
                        'roles' => ['@'], // Nur eingeloggte Benutzer dürfen contact aufrufen
                    ],
                ],
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
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new DynamicModel();
        $searchModel->addRule(['text'], 'string', ['max' => 128]);
        $forumsQuery = Forums::find();

        $searchModel->load(Yii::$app->request->post());
        if ($searchModel->text != null) {
            $forumsQuery->where(['LIKE','F_title', $searchModel->text]);
            $forumsQuery->orWhere(['LIKE', 'F_description', $searchModel->text]);
        }


        $forums = $forumsQuery->all();
        return $this->render('index', [
            'forums' => $forums,
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
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
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            $user = $model->signup();
            if ($user !== null && Yii::$app->user->login($user)) {
                return $this->goHome();
            }
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

        return $this->goHome();
    }

    /**
     * Displays contact page.
     * Nur für eingeloggte Benutzer zugänglich.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            // Benutzerdaten aus der Session holen
            $user = Yii::$app->user->identity;

            // E-Mail an Admin senden
            $sent = Yii::$app->mailer->compose()
                ->setTo(Yii::$app->params['adminEmail'])
                ->setFrom([$user->email => $user->U_username])
                ->setSubject($model->subject)
                ->setTextBody($model->body)
                ->send();

            if ($sent) {
                Yii::$app->session->setFlash('success', 'Vielen Dank für Ihre Nachricht. Wir werden uns bald bei Ihnen melden.');
            } else {
                Yii::$app->session->setFlash('error', 'Es gab ein Problem beim Senden Ihrer Nachricht.');
            }

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
