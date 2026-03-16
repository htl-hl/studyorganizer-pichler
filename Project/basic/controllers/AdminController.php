<?php

namespace app\controllers;

use app\models\Homework;
use app\models\Subject;
use app\models\User;
use app\models\User_Subject;
use Yii;
use yii\db\IntegrityException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AdminController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return $this->isCurrentUserAdmin();
                        },
                    ],
                ],
                'denyCallback' => function () {
                    throw new ForbiddenHttpException('Admin access only.');
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'update-user' => ['post'],
                    'create-teacher' => ['post'],
                    'create-subject' => ['post'],
                    'update-subject' => ['post'],
                    'delete-subject' => ['post'],
                    'assign-teacher-subject' => ['post'],
                    'unlink-teacher-subject' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionUsers()
    {
        $users = User::find()->orderBy(['U_ID' => SORT_ASC])->all();
        $subjects = Subject::find()->orderBy(['S_name' => SORT_ASC])->all();
        $teachers = User::find()
            ->where(['U_role' => User::ROLE_TEACHER])
            ->orderBy(['U_username' => SORT_ASC])
            ->all();
        $teacherAssignments = User_Subject::find()
            ->alias('us')
            ->innerJoin(['u' => User::tableName()], 'u.U_ID = us.U_ID')
            ->innerJoin(['s' => Subject::tableName()], 's.S_ID = us.S_ID')
            ->where(['u.U_role' => User::ROLE_TEACHER])
            ->with(['user', 'subject'])
            ->orderBy(['u.U_username' => SORT_ASC, 's.S_name' => SORT_ASC])
            ->all();

        $teacherSubjectCounts = [];
        foreach ($teacherAssignments as $assignment) {
            $teacherId = (int)$assignment->U_ID;
            if (!isset($teacherSubjectCounts[$teacherId])) {
                $teacherSubjectCounts[$teacherId] = 0;
            }

            $teacherSubjectCounts[$teacherId]++;
        }

        return $this->render('users', [
            'users' => $users,
            'currentUserId' => (int)Yii::$app->user->id,
            'subjects' => $subjects,
            'teachers' => $teachers,
            'teacherAssignments' => $teacherAssignments,
            'teacherSubjectCounts' => $teacherSubjectCounts,
        ]);
    }

    /**
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionUpdateUser($id)
    {
        $user = User::findOne(['U_ID' => (int)$id]);
        if ($user === null) {
            throw new NotFoundHttpException('User not found.');
        }

        $role = strtolower((string)Yii::$app->request->post('role', ''));
        $isActiveRaw = Yii::$app->request->post('is_active');
        $isActive = (int)$isActiveRaw;
        $allowedRoles = [User::ROLE_USER, User::ROLE_ADMIN, User::ROLE_TEACHER];

        if (!in_array($role, $allowedRoles, true)) {
            Yii::$app->session->setFlash('error', 'Invalid role value.');
            return $this->redirect(['users']);
        }

        if (!in_array((string)$isActiveRaw, ['0', '1'], true)) {
            Yii::$app->session->setFlash('error', 'Invalid active value.');
            return $this->redirect(['users']);
        }

        if ($role !== User::ROLE_TEACHER && $isActive !== 1) {
            Yii::$app->session->setFlash('error', 'Only teachers can be set inactive.');
            return $this->redirect(['users']);
        }

        $currentUserId = (int)Yii::$app->user->id;
        if ((int)$user->U_ID === $currentUserId) {
            if ($role !== User::ROLE_ADMIN) {
                Yii::$app->session->setFlash('error', 'You cannot remove your own admin role.');
                return $this->redirect(['users']);
            }

            if ($isActive !== 1) {
                Yii::$app->session->setFlash('error', 'You cannot deactivate your own account.');
                return $this->redirect(['users']);
            }
        }

        if ($this->wouldRemoveLastActiveAdmin($user, $role, $isActive)) {
            Yii::$app->session->setFlash('error', 'At least one active admin must remain.');
            return $this->redirect(['users']);
        }

        $user->U_role = $role;
        $user->U_is_active = $isActive;

        if ($user->save()) {
            Yii::$app->session->setFlash('success', 'User updated successfully.');
        } else {
            $errors = $user->getFirstErrors();
            $message = reset($errors) ?: 'Could not update user.';
            Yii::$app->session->setFlash('error', $message);
        }

        return $this->redirect(['users']);
    }

    /**
     * @return Response
     */
    public function actionCreateTeacher()
    {
        $username = trim((string)Yii::$app->request->post('teacher_username', ''));
        $password = (string)Yii::$app->request->post('teacher_password', '');
        $passwordRepeat = (string)Yii::$app->request->post('teacher_password_repeat', '');
        $isActiveRaw = (string)Yii::$app->request->post('teacher_is_active', '1');

        if ($username === '' || $password === '' || $passwordRepeat === '') {
            Yii::$app->session->setFlash('error', 'Username, password and password confirmation are required.');
            return $this->redirect(['users']);
        }

        if (mb_strlen($password) < 6) {
            Yii::$app->session->setFlash('error', 'Teacher password must be at least 6 characters.');
            return $this->redirect(['users']);
        }

        if (!hash_equals($password, $passwordRepeat)) {
            Yii::$app->session->setFlash('error', 'Teacher passwords do not match.');
            return $this->redirect(['users']);
        }

        if (User::find()->where(['U_username' => $username])->exists()) {
            Yii::$app->session->setFlash('error', 'This username is already taken.');
            return $this->redirect(['users']);
        }

        $isActive = in_array($isActiveRaw, ['0', '1'], true) ? (int)$isActiveRaw : 1;
        $teacher = new User();
        $teacher->U_username = $username;
        $teacher->setPassword($password);
        $teacher->U_role = User::ROLE_TEACHER;
        $teacher->U_is_active = $isActive;
        $teacher->U_creation_date = date('Y-m-d H:i:s');

        if ($teacher->save()) {
            Yii::$app->session->setFlash('success', 'Teacher created.');
        } else {
            Yii::$app->session->setFlash('error', $this->firstError($teacher->getFirstErrors(), 'Could not create teacher.'));
        }

        return $this->redirect(['users']);
    }

    /**
     * @return Response
     */
    public function actionCreateSubject()
    {
        $model = new Subject();
        $model->S_name = trim((string)Yii::$app->request->post('S_name', ''));

        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Subject created.');
        } else {
            Yii::$app->session->setFlash('error', $this->firstError($model->getFirstErrors(), 'Could not create subject.'));
        }

        return $this->redirect(['users']);
    }

    /**
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionUpdateSubject($id)
    {
        $subject = Subject::findOne(['S_ID' => (int)$id]);
        if ($subject === null) {
            throw new NotFoundHttpException('Subject not found.');
        }

        $subject->S_name = trim((string)Yii::$app->request->post('S_name', ''));
        if ($subject->save()) {
            Yii::$app->session->setFlash('success', 'Subject updated.');
        } else {
            Yii::$app->session->setFlash('error', $this->firstError($subject->getFirstErrors(), 'Could not update subject.'));
        }

        return $this->redirect(['users']);
    }

    /**
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDeleteSubject($id)
    {
        $subject = Subject::findOne(['S_ID' => (int)$id]);
        if ($subject === null) {
            throw new NotFoundHttpException('Subject not found.');
        }

        $isUsedByHomework = Homework::find()
            ->where(['S_ID' => (int)$subject->S_ID])
            ->exists();
        if ($isUsedByHomework) {
            Yii::$app->session->setFlash('error', 'Cannot delete subject because homework entries use it.');
            return $this->redirect(['users']);
        }

        $isLinkedToTeachers = User_Subject::find()
            ->where(['S_ID' => (int)$subject->S_ID])
            ->exists();
        if ($isLinkedToTeachers) {
            Yii::$app->session->setFlash('error', 'Cannot delete subject while teachers are still linked.');
            return $this->redirect(['users']);
        }

        try {
            if ($subject->delete() === false) {
                Yii::$app->session->setFlash('error', 'Could not delete subject.');
                return $this->redirect(['users']);
            }
        } catch (IntegrityException $exception) {
            Yii::$app->session->setFlash('error', 'Cannot delete subject because it is still referenced.');
            return $this->redirect(['users']);
        }

        Yii::$app->session->setFlash('success', 'Subject deleted.');
        return $this->redirect(['users']);
    }

    /**
     * @return Response
     */
    public function actionAssignTeacherSubject()
    {
        $teacherId = (int)Yii::$app->request->post('teacher_id', 0);
        $subjectId = (int)Yii::$app->request->post('subject_id', 0);

        if ($teacherId <= 0 || $subjectId <= 0) {
            Yii::$app->session->setFlash('error', 'Teacher and subject are required.');
            return $this->redirect(['users']);
        }

        $teacher = User::findOne(['U_ID' => $teacherId]);
        if ($teacher === null || !$teacher->isTeacher()) {
            Yii::$app->session->setFlash('error', 'Selected user is not a teacher.');
            return $this->redirect(['users']);
        }

        $subject = Subject::findOne(['S_ID' => $subjectId]);
        if ($subject === null) {
            Yii::$app->session->setFlash('error', 'Selected subject not found.');
            return $this->redirect(['users']);
        }

        $assignment = new User_Subject();
        $assignment->U_ID = $teacherId;
        $assignment->S_ID = $subjectId;

        if ($assignment->save()) {
            Yii::$app->session->setFlash('success', 'Teacher linked to subject.');
        } else {
            Yii::$app->session->setFlash('error', $this->firstError($assignment->getFirstErrors(), 'Could not link teacher to subject.'));
        }

        return $this->redirect(['users']);
    }

    /**
     * @param int $teacherId
     * @param int $subjectId
     * @return Response
     */
    public function actionUnlinkTeacherSubject($teacherId, $subjectId)
    {
        $teacherId = (int)$teacherId;
        $subjectId = (int)$subjectId;

        $assignment = User_Subject::findOne([
            'U_ID' => $teacherId,
            'S_ID' => $subjectId,
        ]);
        if ($assignment === null) {
            Yii::$app->session->setFlash('error', 'Assignment not found.');
            return $this->redirect(['users']);
        }

        $teacher = User::findOne(['U_ID' => $teacherId]);
        if ($teacher === null || !$teacher->isTeacher()) {
            Yii::$app->session->setFlash('error', 'Selected user is not a teacher.');
            return $this->redirect(['users']);
        }

        $isUsedInHomework = Homework::find()
            ->where([
                'Teacher_U_ID' => $teacherId,
                'S_ID' => $subjectId,
            ])
            ->exists();
        if ($isUsedInHomework) {
            Yii::$app->session->setFlash('error', 'Cannot remove assignment because it is used by homework entries.');
            return $this->redirect(['users']);
        }

        if ($assignment->delete() === false) {
            Yii::$app->session->setFlash('error', 'Could not remove assignment.');
            return $this->redirect(['users']);
        }

        Yii::$app->session->setFlash('success', 'Assignment removed.');
        return $this->redirect(['users']);
    }

    /**
     * @return bool
     */
    private function isCurrentUserAdmin()
    {
        $identity = Yii::$app->user->identity;
        if ($identity === null || !method_exists($identity, 'getRole')) {
            return false;
        }

        return strtolower((string)$identity->getRole()) === User::ROLE_ADMIN;
    }

    /**
     * @param User $user
     * @param string $newRole
     * @param int $newIsActive
     * @return bool
     */
    private function wouldRemoveLastActiveAdmin(User $user, $newRole, $newIsActive)
    {
        if (!$user->isAdmin() || !$user->isActive()) {
            return false;
        }

        if ($newRole === User::ROLE_ADMIN && $newIsActive === 1) {
            return false;
        }

        $otherActiveAdmins = (int)User::find()
            ->where([
                'U_role' => User::ROLE_ADMIN,
                'U_is_active' => 1,
            ])
            ->andWhere(['<>', 'U_ID', $user->U_ID])
            ->count();

        return $otherActiveAdmins === 0;
    }

    /**
     * @param array $errors
     * @param string $fallback
     * @return string
     */
    private function firstError(array $errors, $fallback)
    {
        $message = reset($errors);
        if ($message === false || $message === null || $message === '') {
            return $fallback;
        }

        return (string)$message;
    }
}
