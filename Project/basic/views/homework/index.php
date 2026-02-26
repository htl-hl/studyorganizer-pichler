<?php

/** @var yii\web\View $this */
/** @var app\models\Homework[] $homeworks */
/** @var array<int, string> $subjectOptions */
/** @var int $selectedSubjectId */

use yii\helpers\Html;

$this->title = 'My Homework';
$this->params['breadcrumbs'][] = $this->title;

$nowTimestamp = time();
?>
<div class="homework-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="d-flex flex-wrap gap-2 mb-3">
        <?= Html::a('Create Homework', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <?= Html::beginForm(['index'], 'get', ['class' => 'row g-2 align-items-end']) ?>
            <div class="col-md-4">
                <label for="subject-filter" class="form-label">Filter by subject</label>
                <?= Html::dropDownList(
                    'subject_id',
                    $selectedSubjectId > 0 ? (string)$selectedSubjectId : '',
                    $subjectOptions,
                    [
                        'prompt' => 'All subjects',
                        'class' => 'form-select',
                        'id' => 'subject-filter',
                    ]
                ) ?>
            </div>
            <div class="col-md-auto">
                <?= Html::submitButton('Apply', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>

    <?php if (empty($homeworks)): ?>
        <div class="alert alert-info mb-0">No homework found for your account.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle table-homework">
                <thead>
                <tr>
                    <th>Subject</th>
                    <th>Teacher</th>
                    <th>Title</th>
                    <th>Due date</th>
                    <th>Status</th>
                    <th style="width: 260px;">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($homeworks as $homework): ?>
                    <?php
                    $rowClass = '';
                    if ((int)$homework->is_done === 1) {
                        $rowClass = 'homework-row-done';
                    } else {
                        $dueTimestamp = strtotime((string)$homework->due_at);
                        if ($dueTimestamp !== false) {
                            $secondsLeft = $dueTimestamp - $nowTimestamp;
                            if ($secondsLeft < 86400) {
                                $rowClass = 'homework-due-red';
                            } elseif ($secondsLeft < 604800) {
                                $rowClass = 'homework-due-yellow';
                            } elseif ($secondsLeft < 1209600) {
                                $rowClass = 'homework-due-blue';
                            }
                        }
                    }
                    ?>
                    <tr class="<?= Html::encode($rowClass) ?>">
                        <td><?= Html::encode($homework->subject ? $homework->subject->S_name : '-') ?></td>
                        <td><?= Html::encode($homework->teacher ? $homework->teacher->U_username : '-') ?></td>
                        <td><?= Html::encode($homework->title) ?></td>
                        <td><?= Html::encode($homework->due_at) ?></td>
                        <td>
                            <?php if ((int)$homework->is_done === 1): ?>
                                <span class="badge text-bg-success">Done</span>
                            <?php else: ?>
                                <span class="badge text-bg-secondary">Open</span>
                            <?php endif; ?>
                        </td>
                        <td class="homework-actions">
                            <?= Html::a('View', ['view', 'id' => $homework->H_ID], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
                            <?php if ((int)$homework->is_done !== 1): ?>
                                <?= Html::a('Edit', ['update', 'id' => $homework->H_ID], ['class' => 'btn btn-sm btn-primary']) ?>
                                <?= Html::beginForm(['mark-done', 'id' => $homework->H_ID], 'post', ['class' => 'd-inline']) ?>
                                <?= Html::submitButton('Mark done', [
                                    'class' => 'btn btn-sm btn-success',
                                    'data' => ['confirm' => 'Mark this homework as done? It can no longer be edited.'],
                                ]) ?>
                                <?= Html::endForm() ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
