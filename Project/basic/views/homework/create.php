<?php






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
