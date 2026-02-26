<?php

/** @var yii\web\View $this */
/** @var app\models\Homework $model */
/** @var array<int, string> $subjectOptions */
/** @var array<int, array<int, string>> $teacherOptionsBySubject */

$this->title = 'Create Homework';
$this->params['breadcrumbs'][] = ['label' => 'My Homework', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="homework-create">
    <h1><?= yii\helpers\Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'subjectOptions' => $subjectOptions,
        'teacherOptionsBySubject' => $teacherOptionsBySubject,
    ]) ?>
</div>
