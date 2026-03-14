<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;




$this->title = 'Kontakt';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-contact">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        Wenn Sie geschäftliche Anfragen oder andere Fragen haben, füllen Sie bitte das folgende Formular aus, um uns zu kontaktieren. Vielen Dank.
    </p>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger">
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>

            <?= $form->field($model, 'name')->textInput([
                    'value' => Yii::$app->user->identity ? Yii::$app->user->identity->getUsername() : '',
                    'readonly' => true
            ]) ?>

            <?= $form->field($model, 'email')->textInput([
                    'placeholder' => 'dein.name@example.com'
            ]) ?>

            <?= $form->field($model, 'subject')->textInput(['autofocus' => true]) ?>

            <?= $form->field($model, 'body')->textarea(['rows' => 6]) ?>

            <div class="form-group">
                <?= Html::submitButton('Absenden', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
