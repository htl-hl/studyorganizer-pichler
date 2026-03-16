<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-login">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white text-center py-4">
                    <br>
                    <br>
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
                </div>
            </div>
        </div>
    </div>
</div>
