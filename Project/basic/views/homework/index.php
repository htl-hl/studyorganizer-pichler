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

<div class="homework-wrapper">
    <br>
    <br>
    <br>
    <br>
    <!-- Header mit Titel und Create Button -->
    <div class="homework-header">
        <div class="header-top">
            <h1 class="page-title">
                <span class="title-icon">📚</span>
                <?= Html::encode($this->title) ?>
            </h1>

            <?= Html::a(
                    '<span class="btn-icon">➕</span> Create Homework',
                    ['create'],
                    ['class' => 'btn btn-primary']
            ) ?>
        </div>

        <!-- Filter Card -->
        <br>
        <div class="filter-card">
            <?= Html::beginForm(['index'], 'get', ['class' => 'filter-form']) ?>
            <div class="filter-group">
                <label for="subject-filter" class="filter-label">
                    <span class="label-icon">🔍</span>
                    Filter by subject
                </label>
                <br>
                <br>

                <div class="select-wrapper">
                    <?= Html::dropDownList(
                            'subject_id',
                            $selectedSubjectId > 0 ? (string)$selectedSubjectId : '',
                            $subjectOptions,
                            [
                                    'prompt' => 'All subjects',
                                    'class' => 'filter-select',
                                    'id' => 'subject-filter',
                            ]
                    ) ?>
                </div>
            </div>
            <br>

            <div class="filter-actions">
                <?= Html::submitButton('Apply', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Reset', ['index'], ['class' => 'btn btn-secondary']) ?>
            </div>

            <?= Html::endForm() ?>
        </div>
    </div>

    <!-- Homework Content -->
    <div class="homework-content">
        <?php if (empty($homeworks)): ?>
            <!-- Empty State mit schönem Design und Create Button -->
            <div class="empty-state-card">
                <br>
                <h2 class="empty-state-title">No homework found</h2>
                <p class="empty-state-text">for your account</p>


            </div>
        <?php else: ?>
            <!-- Tabelle mit Hausübungen -->
            <div class="table-responsive">
                <table class="homework-table">
                    <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th>Title</th>
                        <th>Due date</th>
                        <th>Status</th>
                        <th style="width: 280px;">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($homeworks as $homework): ?>
                        <?php
                        // Status-Klasse für die Zeile berechnen
                        $rowClass = '';
                        if ((int)$homework->is_done === 1) {
                            $rowClass = 'row-done';
                        } else {
                            $dueTimestamp = strtotime((string)$homework->due_at);
                            if ($dueTimestamp !== false) {
                                $secondsLeft = $dueTimestamp - $nowTimestamp;
                                if ($secondsLeft < 86400) { // < 24h
                                    $rowClass = 'row-due-critical';
                                } elseif ($secondsLeft < 604800) { // < 7 days
                                    $rowClass = 'row-due-warning';
                                } elseif ($secondsLeft < 1209600) { // < 14 days
                                    $rowClass = 'row-due-notice';
                                }
                            }
                        }

                        // Due date formatieren
                        $dueDate = date('d.m.Y H:i', strtotime((string)$homework->due_at));
                        ?>
                        <tr class="<?= $rowClass ?>">
                            <td data-label="Subject">
                                    <span class="subject-badge">
                                        <?= Html::encode($homework->subject ? $homework->subject->S_name : '-') ?>
                                    </span>
                            </td>
                            <td data-label="Teacher">
                                <?= Html::encode($homework->teacher ? $homework->teacher->U_username : '-') ?>
                            </td>
                            <td data-label="Title">
                                <strong><?= Html::encode($homework->title) ?></strong>
                            </td>
                            <td data-label="Due date">
                                    <span class="due-date <?= $rowClass ?>">
                                        <?= Html::encode($dueDate) ?>
                                    </span>
                            </td>
                            <td data-label="Status">
                                <?php if ((int)$homework->is_done === 1): ?>
                                    <span class="status-badge status-done">Done</span>
                                <?php else: ?>
                                    <span class="status-badge status-open">Open</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Actions" class="homework-actions">
                                <?= Html::a('View', ['view', 'id' => $homework->H_ID], [
                                        'class' => 'btn btn-outline btn-sm'
                                ]) ?>

                                <?php if ((int)$homework->is_done !== 1): ?>
                                    <?= Html::a('Edit', ['update', 'id' => $homework->H_ID], [
                                            'class' => 'btn btn-primary btn-sm'
                                    ]) ?>

                                    <?= Html::beginForm(['mark-done', 'id' => $homework->H_ID], 'post', [
                                            'class' => 'd-inline'
                                    ]) ?>
                                    <?= Html::submitButton('Mark done', [
                                            'class' => 'btn btn-success btn-sm',
                                            'data' => [
                                                    'confirm' => 'Mark this homework as done? It can no longer be edited.',
                                            ],
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
</div>
