<?php

/** @var yii\web\View $this */
/** @var app\models\Homework $model */
/** @var array<int, string> $subjectOptions */
/** @var array<int, array<int, string>> $teacherOptionsBySubject */

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Json;

$dueAtValue = '';
if (!empty($model->due_at)) {
    $timestamp = strtotime((string)$model->due_at);
    if ($timestamp !== false) {
        $dueAtValue = date('Y-m-d\TH:i', $timestamp);
    }
}

$currentSubjectId = (string)(int)$model->S_ID;
$currentTeacherId = (string)(int)$model->Teacher_U_ID;
$currentTeacherOptions = $teacherOptionsBySubject[(int)$model->S_ID] ?? [];
?>

    <div class="homework-form">
        <?php $form = ActiveForm::begin(['options' => ['class' => 'styled-form']]); ?>

        <div class="form-row">
            <div class="form-col">
                <?= $form->field($model, 'S_ID')->dropDownList(
                        $subjectOptions,
                        [
                                'prompt' => 'Select subject',
                                'id' => 'homework-subject',
                                'class' => 'form-control',
                        ]
                ) ?>
            </div>

            <div class="form-col">
                <?= $form->field($model, 'Teacher_U_ID')->dropDownList(
                        $currentTeacherOptions,
                        [
                                'prompt' => 'Select teacher',
                                'id' => 'homework-teacher',
                                'class' => 'form-control',
                        ]
                ) ?>
            </div>
        </div>

        <div class="form-row">
            <div class="form-col">
                <?= $form->field($model, 'title')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Enter homework title',
                        'class' => 'form-control',
                ]) ?>
            </div>
        </div>

        <div class="form-row">
            <div class="form-col">
                <?= $form->field($model, 'description')->textarea([
                        'rows' => 6,
                        'placeholder' => 'Enter homework description',
                        'class' => 'form-control',
                ]) ?>
            </div>
        </div>

        <div class="form-row">
            <div class="form-col">
                <?= $form->field($model, 'due_at')->input('datetime-local', [
                        'value' => $dueAtValue,
                        'class' => 'form-control',
                ]) ?>
            </div>
        </div>

        <div class="form-actions">
            <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Cancel', ['homework/index'], ['class' => 'btn btn-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

<?php
$teachersBySubjectJson = Json::htmlEncode($teacherOptionsBySubject);
$currentSubjectIdJson = Json::htmlEncode($currentSubjectId);
$currentTeacherIdJson = Json::htmlEncode($currentTeacherId);
$js = <<<JS
(function () {
    const teachersBySubject = $teachersBySubjectJson;
    const currentSubjectId = $currentSubjectIdJson;
    const currentTeacherId = $currentTeacherIdJson;
    const subjectSelect = document.getElementById('homework-subject');
    const teacherSelect = document.getElementById('homework-teacher');

    if (!subjectSelect || !teacherSelect) {
        return;
    }

    const createOption = (value, label) => {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = label;
        return option;
    };

    const renderTeacherOptions = (isInitialRender) => {
        const subjectId = isInitialRender ? (subjectSelect.value || currentSubjectId) : subjectSelect.value;
        const teachers = teachersBySubject[subjectId] || {};
        const previousValue = teacherSelect.value;

        teacherSelect.innerHTML = '';
        teacherSelect.appendChild(createOption('', 'Select teacher'));

        Object.entries(teachers).forEach(([teacherId, teacherName]) => {
            teacherSelect.appendChild(createOption(teacherId, teacherName));
        });

        if (previousValue && teachers[previousValue]) {
            teacherSelect.value = previousValue;
            return;
        }

        if (isInitialRender && currentTeacherId && teachers[currentTeacherId]) {
            teacherSelect.value = currentTeacherId;
            return;
        }

        teacherSelect.value = '';
    };

    subjectSelect.addEventListener('change', () => renderTeacherOptions(false));
    renderTeacherOptions(true);
})();
JS;
$this->registerJs($js);
?>
