<?php

/** @var yii\web\View $this */
/** @var app\models\User[] $users */
/** @var int $currentUserId */

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Json;

$this->title = 'Admin Panel';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-users">
    <h1><?= Html::encode($this->title) ?></h1>
    <p class="text-muted">Manage user roles and active status. Only teachers can be set inactive.</p>

    <?php if (empty($users)): ?>
        <div class="alert alert-info">No users found.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
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
</div>
<?php
$teacherRole = Json::htmlEncode(User::ROLE_TEACHER);
$js = <<<JS
(function () {
    const teacherRole = $teacherRole;
    const rows = document.querySelectorAll('.admin-users tbody tr');

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
