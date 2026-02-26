<?php

namespace app\controllers;

use app\models\Homework;
use app\models\Subject;
use app\models\User;
use app\models\User_Subject;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class HomeworkController extends Controller
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
                    ],
                ],
                'denyCallback' => function () {
                    return $this->redirect(['site/login']);
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'mark-done' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $currentUserId = $this->currentUserId();
        $selectedSubjectId = (int)Yii::$app->request->get('subject_id', 0);

        $query = Homework::find()
            ->where(['U_ID' => $currentUserId])
            ->with(['subject', 'teacher'])
            ->orderBy(['due_at' => SORT_ASC, 'H_ID' => SORT_DESC]);

        if ($selectedSubjectId > 0) {
            $query->andWhere(['S_ID' => $selectedSubjectId]);
        }

        return $this->render('index', [
            'homeworks' => $query->all(),
            'subjectOptions' => $this->subjectOptions(),
            'selectedSubjectId' => $selectedSubjectId,
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Homework();
        $model->U_ID = $this->currentUserId();
        $model->is_done = 0;

        if ($model->load(Yii::$app->request->post())) {
            $model->U_ID = $this->currentUserId();
            $this->normalizeDueAt($model);
            $this->validateTeacherSubject($model);

            if (!$model->hasErrors() && $model->save()) {
                Yii::$app->session->setFlash('success', 'Homework created.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'subjectOptions' => $this->subjectOptions(),
            'teacherOptionsBySubject' => $this->teacherOptionsBySubject(),
        ]);
    }

    /**
     * @param int $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findOwnedHomework((int)$id);
        if ((int)$model->is_done === 1) {
            Yii::$app->session->setFlash('error', 'Done homework cannot be edited.');
            return $this->redirect(['view', 'id' => $model->H_ID]);
        }

        if ($model->load(Yii::$app->request->post())) {
            $model->U_ID = $this->currentUserId();
            $this->normalizeDueAt($model);
            $this->validateTeacherSubject($model);

            if (!$model->hasErrors() && $model->save()) {
                Yii::$app->session->setFlash('success', 'Homework updated.');
                return $this->redirect(['view', 'id' => $model->H_ID]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'subjectOptions' => $this->subjectOptions(),
            'teacherOptionsBySubject' => $this->teacherOptionsBySubject(),
        ]);
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findOwnedHomework((int)$id),
        ]);
    }

    /**
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionMarkDone($id)
    {
        $model = $this->findOwnedHomework((int)$id);
        if ((int)$model->is_done === 1) {
            Yii::$app->session->setFlash('info', 'Homework is already marked as done.');
            return $this->redirect(['index']);
        }

        $model->is_done = 1;
        if ($model->save(false, ['is_done', 'updated_at'])) {
            Yii::$app->session->setFlash('success', 'Homework marked as done.');
        } else {
            Yii::$app->session->setFlash('error', 'Could not mark homework as done.');
        }

        return $this->redirect(['index']);
    }

    /**
     * @param int $id
     * @return Homework
     * @throws NotFoundHttpException
     */
    private function findOwnedHomework($id)
    {
        $model = Homework::find()
            ->where([
                'H_ID' => $id,
                'U_ID' => $this->currentUserId(),
            ])
            ->with(['subject', 'teacher'])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException('Homework not found.');
        }

        return $model;
    }

    /**
     * @return int
     */
    private function currentUserId()
    {
        return (int)Yii::$app->user->id;
    }

    /**
     * @return array
     */
    private function subjectOptions()
    {
        return ArrayHelper::map(
            Subject::find()->orderBy(['S_name' => SORT_ASC])->all(),
            'S_ID',
            'S_name'
        );
    }

    /**
     * @return array<int, array<int, string>>
     */
    private function teacherOptionsBySubject()
    {
        $assignments = User_Subject::find()
            ->alias('us')
            ->innerJoin(['u' => User::tableName()], 'u.U_ID = us.U_ID')
            ->where([
                'u.U_role' => User::ROLE_TEACHER,
                'u.U_is_active' => 1,
            ])
            ->with('user')
            ->orderBy(['u.U_username' => SORT_ASC])
            ->all();

        $teachersBySubject = [];
        foreach ($assignments as $assignment) {
            $subjectId = (int)$assignment->S_ID;
            $teacherId = (int)$assignment->U_ID;
            $teacher = $assignment->user;
            if ($teacher === null) {
                continue;
            }

            $teachersBySubject[$subjectId][$teacherId] = $teacher->U_username;
        }

        return $teachersBySubject;
    }

    /**
     * @param Homework $model
     * @return void
     */
    private function normalizeDueAt(Homework $model)
    {
        $value = trim((string)$model->due_at);
        if ($value === '') {
            $model->addError('due_at', 'Due date is required.');
            return;
        }

        $normalized = str_replace('T', ' ', $value);
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $normalized) === 1) {
            $normalized .= ':00';
        }

        $timestamp = strtotime($normalized);
        if ($timestamp === false) {
            $model->addError('due_at', 'Invalid due date format.');
            return;
        }

        $model->due_at = date('Y-m-d H:i:s', $timestamp);
    }

    /**
     * @param Homework $model
     * @return void
     */
    private function validateTeacherSubject(Homework $model)
    {
        $hasAssignment = User_Subject::find()
            ->alias('us')
            ->innerJoin(['u' => User::tableName()], 'u.U_ID = us.U_ID')
            ->where([
                'us.U_ID' => (int)$model->Teacher_U_ID,
                'us.S_ID' => (int)$model->S_ID,
                'u.U_role' => User::ROLE_TEACHER,
                'u.U_is_active' => 1,
            ])
            ->exists();

        if (!$hasAssignment) {
            $model->addError('Teacher_U_ID', 'Selected teacher is not assigned to this subject.');
        }
    }
}
