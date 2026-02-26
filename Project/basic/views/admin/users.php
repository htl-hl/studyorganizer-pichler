<?php

/** @var yii\web\View $this */
/** @var app\models\User[] $users */
/** @var int $currentUserId */
/** @var app\models\Subject[] $subjects */
/** @var app\models\User[] $teachers */
/** @var app\models\User_Subject[] $teacherAssignments */
/** @var array<int, int> $teacherSubjectCounts */

use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

$teacherOptions = [];
foreach ($teachers as $teacher) {
    $label = $teacher->U_username;
    if (!$teacher->isActive()) {
        $label .= ' (inactive)';
    }
    $teacherOptions[(int)$teacher->U_ID] = $label;
}

$subjectOptions = ArrayHelper::map($subjects, 'S_ID', 'S_name');
$subjectTeacherCounts = [];
foreach ($teacherAssignments as $assignment) {
    $subjectId = (int)$assignment->S_ID;
    if (!isset($subjectTeacherCounts[$subjectId])) {
        $subjectTeacherCounts[$subjectId] = 0;
    }
    $subjectTeacherCounts[$subjectId]++;
}

$this->title = 'Admin Panel';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-users">
    <h1><?= Html::encode($this->title) ?></h1>

    <h2 class="h4 mt-4">User Management</h2>
    <p class="text-muted">Only teachers can be set inactive. Inactive teachers keep their links, but cannot be used for new homework.</p>

    <?php if (empty($users)): ?>
        <div class="alert alert-info">No users found.</div>
    <?php else: ?>
        <div class="table-responsive mb-4">
            <table class="table table-striped table-bordered align-middle js-user-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th style="width: 130px;">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <?php $formId = 'user-form-' . (int)$user->U_ID; ?>
                    <?php $isTeacher = $user->normalizedRole() === User::ROLE_TEACHER; ?>
                    <tr>
                        <td><?= Html::encode($user->U_ID) ?></td>
                        <td>
                            <?= Html::encode($user->U_username) ?>
                            <?php if ((int)$user->U_ID === $currentUserId): ?>
                                <span class="badge text-bg-secondary ms-2">You</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= Html::dropDownList(
                                'role',
                                $user->normalizedRole(),
                                [
                                    User::ROLE_USER => 'User',
                                    User::ROLE_TEACHER => 'Teacher',
                                    User::ROLE_ADMIN => 'Admin',
                                ],
                                [
                                    'class' => 'form-select form-select-sm js-role-select',
                                    'form' => $formId,
                                ]
                            ) ?>
                        </td>
                        <td>
                            <div class="js-status-editor"<?= $isTeacher ? '' : ' style="display:none;"' ?>>
                                <?= Html::dropDownList(
                                    'is_active',
                                    (string)(int)$user->U_is_active,
                                    ['1' => 'Active', '0' => 'Inactive'],
                                    [
                                        'class' => 'form-select form-select-sm js-status-select',
                                        'form' => $formId,
                                        'disabled' => !$isTeacher,
                                    ]
                                ) ?>
                            </div>
                            <div class="js-status-readonly"<?= $isTeacher ? ' style="display:none;"' : '' ?>>
                                <span class="badge text-bg-success">Active</span>
                                <?= Html::hiddenInput('is_active', '1', [
                                    'class' => 'js-status-hidden',
                                    'form' => $formId,
                                    'disabled' => $isTeacher,
                                ]) ?>
                            </div>
                        </td>
                        <td><?= Html::encode($user->U_creation_date) ?></td>
                        <td>
                            <?= Html::beginForm(
                                ['admin/update-user', 'id' => $user->U_ID],
                                'post',
                                ['id' => $formId, 'class' => 'm-0']
                            ) ?>
                            <?= Html::submitButton('Save', ['class' => 'btn btn-sm btn-primary']) ?>
                            <?= Html::endForm() ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <h2 class="h4">Teacher Management</h2>
    <div class="card mb-4">
        <div class="card-body">
            <?= Html::beginForm(['admin/create-teacher'], 'post', ['class' => 'row g-2 align-items-end']) ?>
            <div class="col-md-3">
                <label for="teacher-username" class="form-label">Username</label>
                <?= Html::textInput('teacher_username', '', [
                    'id' => 'teacher-username',
                    'class' => 'form-control',
                    'required' => true,
                    'maxlength' => 255,
                ]) ?>
            </div>
            <div class="col-md-3">
                <label for="teacher-password" class="form-label">Password</label>
                <?= Html::passwordInput('teacher_password', '', [
                    'id' => 'teacher-password',
                    'class' => 'form-control',
                    'required' => true,
                    'minlength' => 6,
                ]) ?>
            </div>
            <div class="col-md-3">
                <label for="teacher-password-repeat" class="form-label">Repeat password</label>
                <?= Html::passwordInput('teacher_password_repeat', '', [
                    'id' => 'teacher-password-repeat',
                    'class' => 'form-control',
                    'required' => true,
                    'minlength' => 6,
                ]) ?>
            </div>
            <div class="col-md-2">
                <?= Html::hiddenInput('teacher_is_active', '0') ?>
                <div class="form-check mb-2">
                    <?= Html::checkbox('teacher_is_active', true, [
                        'class' => 'form-check-input',
                        'id' => 'teacher-is-active',
                        'value' => '1',
                    ]) ?>
                    <label class="form-check-label" for="teacher-is-active">Active</label>
                </div>
            </div>
            <div class="col-md-auto">
                <?= Html::submitButton('Create Teacher', ['class' => 'btn btn-success']) ?>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>

    <h2 class="h4">Subject Management</h2>
    <div class="card mb-4">
        <div class="card-body">
            <?= Html::beginForm(['admin/create-subject'], 'post', ['class' => 'row g-2 align-items-end mb-3']) ?>
            <div class="col-md-6">
                <label for="subject-name" class="form-label">New subject name</label>
                <?= Html::textInput('S_name', '', [
                    'id' => 'subject-name',
                    'class' => 'form-control',
                    'maxlength' => 255,
                    'required' => true,
                    'placeholder' => 'e.g. Math',
                ]) ?>
            </div>
            <div class="col-md-auto">
                <?= Html::submitButton('Create Subject', ['class' => 'btn btn-success']) ?>
            </div>
            <?= Html::endForm() ?>

            <?php if (empty($subjects)): ?>
                <div class="alert alert-info mb-0">No subjects available yet.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead>
                        <tr>
                            <th style="width: 90px;">ID</th>
                            <th>Subject</th>
                            <th style="width: 170px;">Linked teachers</th>
                            <th style="width: 280px;">Rename</th>
                            <th style="width: 120px;">Delete</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($subjects as $subject): ?>
                            <tr>
                                <td><?= Html::encode($subject->S_ID) ?></td>
                                <td><?= Html::encode($subject->S_name) ?></td>
                                <td><?= (int)($subjectTeacherCounts[(int)$subject->S_ID] ?? 0) ?></td>
                                <td>
                                    <?= Html::beginForm(['admin/update-subject', 'id' => $subject->S_ID], 'post', ['class' => 'd-flex gap-2']) ?>
                                    <?= Html::textInput('S_name', $subject->S_name, [
                                        'class' => 'form-control form-control-sm',
                                        'required' => true,
                                        'maxlength' => 255,
                                    ]) ?>
                                    <?= Html::submitButton('Save', ['class' => 'btn btn-sm btn-primary']) ?>
                                    <?= Html::endForm() ?>
                                </td>
                                <td>
                                    <?= Html::beginForm(['admin/delete-subject', 'id' => $subject->S_ID], 'post', ['class' => 'm-0']) ?>
                                    <?= Html::submitButton('Delete', [
                                        'class' => 'btn btn-sm btn-outline-danger',
                                        'data' => ['confirm' => 'Delete this subject? This is blocked if linked to teachers or homework.'],
                                    ]) ?>
                                    <?= Html::endForm() ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <h2 class="h4">Teacher Subject Links</h2>
    <div class="card">
        <div class="card-body">
            <?php if (empty($teachers) || empty($subjects)): ?>
                <div class="alert alert-warning mb-3">
                    You need at least one teacher and one subject before creating links.
                </div>
            <?php else: ?>
                <?= Html::beginForm(['admin/assign-teacher-subject'], 'post', ['class' => 'row g-2 align-items-end mb-3']) ?>
                <div class="col-md-4">
                    <label for="teacher-id" class="form-label">Teacher</label>
                    <?= Html::dropDownList('teacher_id', '', $teacherOptions, [
                        'id' => 'teacher-id',
                        'class' => 'form-select',
                        'prompt' => 'Select teacher',
                        'required' => true,
                    ]) ?>
                </div>
                <div class="col-md-4">
                    <label for="subject-id" class="form-label">Subject</label>
                    <?= Html::dropDownList('subject_id', '', $subjectOptions, [
                        'id' => 'subject-id',
                        'class' => 'form-select',
                        'prompt' => 'Select subject',
                        'required' => true,
                    ]) ?>
                </div>
                <div class="col-md-auto">
                    <?= Html::submitButton('Link Subject', ['class' => 'btn btn-primary']) ?>
                </div>
                <?= Html::endForm() ?>
            <?php endif; ?>

            <?php if (empty($teacherAssignments)): ?>
                <div class="alert alert-info mb-0">No teacher-subject links yet.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Teacher</th>
                            <th>Status</th>
                            <th>Subject</th>
                            <th style="width: 150px;">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($teacherAssignments as $assignment): ?>
                            <?php
                            $teacher = $assignment->user;
                            $subject = $assignment->subject;
                            $teacherId = (int)$assignment->U_ID;
                            $subjectId = (int)$assignment->S_ID;
                            ?>
                            <tr>
                                <td><?= Html::encode($teacher ? $teacher->U_username : 'Unknown teacher') ?></td>
                                <td>
                                    <?php if ($teacher && $teacher->isActive()): ?>
                                        <span class="badge text-bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge text-bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= Html::encode($subject ? $subject->S_name : 'Unknown subject') ?></td>
                                <td>
                                    <?= Html::beginForm(
                                        ['admin/unlink-teacher-subject', 'teacherId' => $teacherId, 'subjectId' => $subjectId],
                                        'post',
                                        ['class' => 'm-0']
                                    ) ?>
                                    <?= Html::submitButton('Unlink', [
                                        'class' => 'btn btn-sm btn-outline-danger',
                                        'data' => ['confirm' => 'Remove this teacher-subject link?'],
                                    ]) ?>
                                    <?= Html::endForm() ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
$teacherRole = Json::htmlEncode(User::ROLE_TEACHER);
$js = <<<JS
(function () {
    const teacherRole = $teacherRole;
    const rows = document.querySelectorAll('.js-user-table tbody tr');

    rows.forEach((row) => {
        const roleSelect = row.querySelector('.js-role-select');
        const statusEditor = row.querySelector('.js-status-editor');
        const statusReadonly = row.querySelector('.js-status-readonly');
        const statusSelect = row.querySelector('.js-status-select');
        const statusHidden = row.querySelector('.js-status-hidden');
        if (!roleSelect || !statusEditor || !statusReadonly || !statusSelect || !statusHidden) {
            return;
        }

        const syncStatusControl = () => {
            const isTeacher = roleSelect.value === teacherRole;

            if (isTeacher) {
                statusEditor.style.display = '';
                statusReadonly.style.display = 'none';
                statusSelect.disabled = false;
                statusHidden.disabled = true;
                return;
            }

            statusSelect.value = '1';
            statusSelect.disabled = true;
            statusHidden.value = '1';
            statusHidden.disabled = false;
            statusEditor.style.display = 'none';
            statusReadonly.style.display = '';
        };

        roleSelect.addEventListener('change', syncStatusControl);
        syncStatusControl();
    });
})();
JS;
$this->registerJs($js);
?>
