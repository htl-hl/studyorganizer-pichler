<?php

namespace app\controllers;

use app\models\User;
use Yii;
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
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionUsers()
    {
        return $this->render('users', [
            'users' => User::find()->orderBy(['U_ID' => SORT_ASC])->all(),
            'currentUserId' => (int)Yii::$app->user->id,
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
}
