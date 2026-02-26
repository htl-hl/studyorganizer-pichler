<?php

/** @var yii\web\View $this */
/** @var app\models\Homework $model */
/** @var array<int, string> $subjectOptions */
/** @var array<int, array<int, string>> $teacherOptionsBySubject */

use yii\helpers\Html;

$this->title = 'Edit Homework';
$this->params['breadcrumbs'][] = ['label' => 'My Homework', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Html::encode($model->title), 'url' => ['view', 'id' => $model->H_ID]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="homework-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'subjectOptions' => $subjectOptions,
        'teacherOptionsBySubject' => $teacherOptionsBySubject,
    ]) ?>
</div>
