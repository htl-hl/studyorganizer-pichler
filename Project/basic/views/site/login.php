<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-login">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h1 class="h3 mb-0"><?= Html::encode($this->title) ?></h1>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted text-center mb-4">Sign in with your username and password.</p>

                    <?php $form = ActiveForm::begin([
                        'id' => 'login-form',
                        'fieldConfig' => [
                            'template' => "{label}\n{input}\n{error}",
                            'labelOptions' => ['class' => 'form-label fw-semibold'],
                            'inputOptions' => ['class' => 'form-control form-control-lg'],
                            'errorOptions' => ['class' => 'invalid-feedback d-block'],
                        ],
                    ]); ?>

                    <div class="mb-4">
                        <?= $form->field($model, 'username')->textInput([
                            'autofocus' => true,
                            'placeholder' => 'Enter your username',
                        ]) ?>
                    </div>

                    <div class="mb-4">
                        <?= $form->field($model, 'password')->passwordInput([
                            'placeholder' => 'Enter your password',
                        ]) ?>
                    </div>

                    <div class="d-grid gap-2 mb-3">
                        <?= Html::submitButton('Login', [
                            'class' => 'btn btn-primary btn-lg',
                            'name' => 'login-button',
                        ]) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                    <div class="text-center">
                        <span class="text-muted">No account yet?</span>
                        <?= Html::a('Register', ['site/register'], ['class' => 'fw-semibold text-decoration-none']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
