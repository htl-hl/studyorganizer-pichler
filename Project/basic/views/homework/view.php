<?php

/** @var yii\web\View $this */
/** @var app\models\Homework $model */

use yii\helpers\Html;

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'My Homework', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="homework-view">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Back', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        <?php if ((int)$model->is_done !== 1): ?>
            <?= Html::a('Edit', ['update', 'id' => $model->H_ID], ['class' => 'btn btn-primary']) ?>
            <?= Html::beginForm(['mark-done', 'id' => $model->H_ID], 'post', ['class' => 'd-inline']) ?>
            <?= Html::submitButton('Mark as done', [
                'class' => 'btn btn-success',
                'data' => ['confirm' => 'Mark this homework as done? You will not be able to edit it anymore.'],
            ]) ?>
            <?= Html::endForm() ?>
        <?php else: ?>
            <span class="badge text-bg-success">Done</span>
        <?php endif; ?>
    </p>

    <table class="table table-bordered">
        <tbody>
        <tr>
            <th style="width: 160px;">Subject</th>
            <td><?= Html::encode($model->subject ? $model->subject->S_name : '-') ?></td>
        </tr>
        <tr>
            <th>Teacher</th>
            <td><?= Html::encode($model->teacher ? $model->teacher->U_username : '-') ?></td>
        </tr>
        <tr>
            <th>Due date</th>
            <td><?= Html::encode($model->due_at) ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><?= (int)$model->is_done === 1 ? 'Done' : 'Open' ?></td>
        </tr>
        <tr>
            <th>Description</th>
            <td><?= Html::tag('div', Html::encode($model->description), ['class' => 'homework-description']) ?></td>
        </tr>
        </tbody>
    </table>
</div>
