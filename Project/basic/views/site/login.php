<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Anmelden';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-login">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white text-center py-4">
<<<<<<< HEAD
                    <h1 class="h3 mb-0">
                        <i class="fas fa-moon me-2"></i>
                        <?= Html::encode($this->title) ?>
                    </h1>
                </div>
                <div class="card-body p-4">

                    <p class="text-muted text-center mb-4">
                        <i class="fas fa-lock me-2"></i>
                        Bitte melde dich mit deinen Zugangsdaten an
                    </p>

                    <?php $form = ActiveForm::begin([
                            'id' => 'login-form',
                            'fieldConfig' => [
                                    'template' => "{label}\n{input}\n{error}",
                                    'labelOptions' => ['class' => 'form-label fw-semibold'],
                                    'inputOptions' => ['class' => 'form-control form-control-lg rounded-3'],
                                    'errorOptions' => ['class' => 'invalid-feedback d-block'],
                            ],
                    ]); ?>

                    <div class="mb-4">
                        <?= $form->field($model, 'username')
                                ->textInput([
                                        'autofocus' => true,
                                        'placeholder' => 'z.B. max.mustermann',
                                        'class' => 'form-control form-control-lg rounded-3'
                                ])
                                ->label('Benutzername') ?>
                    </div>

                    <div class="mb-4">
                        <?= $form->field($model, 'password')
                                ->passwordInput([
                                        'placeholder' => '••••••••',
                                        'class' => 'form-control form-control-lg rounded-3'
                                ])
                                ->label('Passwort') ?>
                    </div>

                    <div class="d-grid gap-2 mb-4">
                        <?= Html::submitButton(
                                '<i class="fas fa-sign-in-alt me-2"></i> Anmelden',
                                [
                                        'class' => 'btn btn-primary btn-lg rounded-3 py-3 fw-bold',
                                        'name' => 'login-button'
                                ]
                        ) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                    <div class="text-center mt-3">
                        <p class="mb-1">
                            <i class="fas fa-question-circle me-1 text-primary"></i>
                            Noch kein Konto?
                            <?= Html::a(
                                    '<i class="fas fa-user-plus me-1"></i>Jetzt registrieren',
                                    ['site/register'],
                                    ['class' => 'text-decoration-none fw-bold']
                            ) ?>
                        </p>
                        <p class="mb-0 mt-2">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>
                                Deine Daten sind bei uns sicher
                            </small>
                        </p>
                    </div>

                    <!-- Optional: Dark Mode Hinweis -->
                    <div class="text-center mt-3 pt-2 border-top border-secondary">
                        <small class="text-muted">
                            <i class="fas fa-moon me-1"></i>
                            Dark Mode ist standardmäßig aktiviert
                        </small>
                    </div>
=======
                    <h2 class="mb-0">Study Organizer</h2>
                    <p class="mb-0 mt-2">Log in with your username and password</p>
                </div>

                <div class="card-body p-4">
                    <?php $form = ActiveForm::begin([
                            'id' => 'login-form',
                            'fieldConfig' => [
                                    'template' => "{label}\n{input}\n{error}",
                                    'labelOptions' => ['class' => 'form-label fw-bold'],
                                    'inputOptions' => ['class' => 'form-control form-control-lg'],
                                    'errorOptions' => ['class' => 'invalid-feedback'],
                            ],
                    ]); ?>

                    <div class="mb-4">
                        <?= $form->field($model, 'username')
                                ->textInput([
                                        'autofocus' => true,
                                        'placeholder' => 'Enter your username',
                                        'class' => 'form-control form-control-lg'
                                ])
                                ->label('<i class="fas fa-user me-2"></i>Username') ?>
                    </div>

                    <div class="mb-4">
                        <?= $form->field($model, 'password')
                                ->passwordInput([
                                        'placeholder' => 'Enter your password',
                                        'class' => 'form-control form-control-lg'
                                ])
                                ->label('<i class="fas fa-lock me-2"></i>Password') ?>
                    </div>

                    <div class="d-grid gap-2">
                        <?= Html::submitButton(
                                '<i class="fas fa-sign-in-alt me-2"></i>Login',
                                [
                                        'class' => 'btn btn-primary btn-lg',
                                        'name' => 'login-button'
                                ]
                        ) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="mb-0">
                            No account yet?
                            <?= Html::a(
                                    'Register here',
                                    ['site/register'],
                                    ['class' => 'text-primary fw-bold text-decoration-none']
                            ) ?>.
                        </p>
                    </div>
                </div>

                <div class="card-footer text-center text-muted py-3">
                    <small>&copy; My Company 2026 - Powered by Yii Framework</small>
>>>>>>> 5f81c22 (commit)
                </div>
            </div>
        </div>
    </div>
</div>
