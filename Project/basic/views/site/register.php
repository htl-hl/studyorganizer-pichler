<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\RegisterForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Registrieren';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-register">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-user-plus me-2"></i>
                        <?= Html::encode($this->title) ?>
                    </h1>
                </div>
                <div class="card-body p-4">

                    <p class="text-muted text-center mb-4">
                        <i class="fas fa-address-card me-2"></i>
                        Erstelle einen neuen Account
                    </p>

                    <?php $form = ActiveForm::begin([
                            'id' => 'register-form',
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
                        <small class="text-muted form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Min. 3 Zeichen, nur Buchstaben und Zahlen
                        </small>
                    </div>

                    <div class="mb-4">
                        <?= $form->field($model, 'email')
                                ->textInput([
                                        'type' => 'email',
                                        'placeholder' => 'max.mustermann@example.com',
                                        'class' => 'form-control form-control-lg rounded-3'
                                ])
                                ->label('E-Mail Adresse') ?>
                    </div>

                    <div class="mb-4">
                        <?= $form->field($model, 'password')
                                ->passwordInput([
                                        'placeholder' => '••••••••',
                                        'class' => 'form-control form-control-lg rounded-3'
                                ])
                                ->label('Passwort') ?>
                        <small class="text-muted form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Min. 8 Zeichen, mit Groß-/Kleinbuchstaben und Zahlen
                        </small>
                    </div>

                    <div class="mb-4">
                        <?= $form->field($model, 'passwordRepeat')
                                ->passwordInput([
                                        'placeholder' => '••••••••',
                                        'class' => 'form-control form-control-lg rounded-3'
                                ])
                                ->label('Passwort bestätigen') ?>
                    </div>

                    <div class="mb-4 form-check">
                        <?= Html::checkbox('terms', false, [
                                'id' => 'terms-checkbox',
                                'class' => 'form-check-input',
                                'required' => true
                        ]) ?>
                        <label class="form-check-label text-muted" for="terms-checkbox">
                            Ich akzeptiere die
                            <?= Html::a('Nutzungsbedingungen', ['site/terms'], ['class' => 'text-decoration-none']) ?>
                            und
                            <?= Html::a('Datenschutzerklärung', ['site/privacy'], ['class' => 'text-decoration-none']) ?>
                        </label>
                    </div>

                    <div class="d-grid gap-2 mb-4">
                        <?= Html::submitButton(
                                '<i class="fas fa-user-plus me-2"></i> Jetzt registrieren',
                                [
                                        'class' => 'btn btn-primary btn-lg rounded-3 py-3 fw-bold',
                                        'name' => 'register-button'
                                ]
                        ) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                    <div class="text-center mt-3">
                        <p class="mb-1">
                            <i class="fas fa-sign-in-alt me-1 text-primary"></i>
                            Bereits registriert?
                            <?= Html::a(
                                    'Hier anmelden',
                                    ['site/login'],
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
                </div>
            </div>
        </div>
    </div>
</div>
