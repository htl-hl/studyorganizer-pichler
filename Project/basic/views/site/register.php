<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\RegisterForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Register';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-register">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-success text-white text-center py-4">
                    <h2 class="mb-0">📝 Create Account</h2>
                    <p class="mb-0 mt-2">Join Study Organizer today</p>
                </div>

                <div class="card-body p-4">
                    <?php $form = ActiveForm::begin([
                            'id' => 'register-form',
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
                                        'placeholder' => 'Choose a username'
                                ])
                                ->label('<i class="fas fa-user me-2"></i>Username') ?>
                    </div>

                    <div class="mb-4">
                        <?= $form->field($model, 'email')
                                ->textInput([
                                        'placeholder' => 'Enter your email'
                                ])
                                ->label('<i class="fas fa-envelope me-2"></i>Email') ?>
                    </div>

                    <div class="mb-4">
                        <?= $form->field($model, 'password')
                                ->passwordInput([
                                        'placeholder' => 'Create a password'
                                ])
                                ->label('<i class="fas fa-lock me-2"></i>Password') ?>
                    </div>

                    <div class="mb-4">
                        <?= $form->field($model, 'passwordRepeat')
                                ->passwordInput([
                                        'placeholder' => 'Confirm your password'
                                ])
                                ->label('<i class="fas fa-lock me-2"></i>Confirm Password') ?>
                    </div>

                    <div class="d-grid gap-2">
                        <?= Html::submitButton(
                                '<i class="fas fa-user-plus me-2"></i>Register',
                                [
                                        'class' => 'btn btn-success btn-lg',
                                        'name' => 'register-button'
                                ]
                        ) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="mb-0">
                            Already registered?
                            <?= Html::a(
                                    'Log in here',
                                    ['site/login'],
                                    ['class' => 'text-success fw-bold text-decoration-none']
                            ) ?>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
