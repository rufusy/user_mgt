<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 * @date: 5/8/2023
 * @time: 10:13 PM
 */

namespace app\controllers;

use app\helpers\SmisHelper;
use app\models\Employee;
use app\models\ForgotPasswordForm;
use app\models\LoginForm;
use app\models\User;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use Yii;
use yii\base\UserException;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

class SiteController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    #[ArrayShape(['access' => "array"])]
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    #[ArrayShape(['error' => "string[]"])]
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        if(parent::beforeAction($action)) {
            if ($action->id == 'error') {
                $this->layout = 'error';
            }
            return true;
        }
        return false;
    }

    /**
     * @return Response
     */
    public function actionIndex(): Response
    {
        if(Yii::$app->user->isGuest){
            return $this->redirect(['/site/login']);
        }
        return $this->redirect(['/users/index']);
    }

    /**
     * @return string|\yii\console\Response|Response
     * @throws ServerErrorHttpException
     */
    public function actionLogin(): Response|string|\yii\console\Response
    {
        try {
            if (Yii::$app->user->isGuest) {
                $this->layout = 'login';
                return $this->render('login', [
                    'title' => $this->createPageTitle('login'),
                    'model' => new LoginForm()
                ]);
            } else {
                return Yii::$app->response->redirect(['/users/index']);
            }
        }catch(Exception $ex){
            $message = $ex->getMessage();
            if(YII_ENV_DEV){
                $message .= ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine();
            }
            throw new ServerErrorHttpException($message, 500);
        }
    }

    /**
     * @return Response|string|\yii\console\Response
     * @throws ServerErrorHttpException
     */
    public function actionProcessLogin(): Response|string|\yii\console\Response
    {
        try {
            $model = new LoginForm();
            if($model->load(Yii::$app->request->post())){
                if($model->validate()){
                    $user = User::findByUsername($model->username);

                    if(empty($user) || empty($user->password) || !$user->validatePassword($model->password)){
                        $this->setFlash('danger', 'Login', 'Incorrect username or password.');
                        return $this->redirect(['/site/login']);
                    }

                    if(Yii::$app->user->login($user)){
                        $user->last_login_at = SmisHelper::formatDate('now', 'Y-m-d h:i:s');
                        if(!$user->save()){
                            throw new Exception('Failed to update login time.');
                        }

                        $this->setFlash('success', 'Login', 'Logged in successfully.');
                        return Yii::$app->response->redirect(['/users/index']);
                    }else{
                        throw new Exception('An error occurred while trying to log in.');
                    }
                }else{
                    $this->setFlash('danger', 'Login', 'Incorrect username or password.');
                    return $this->redirect(['/site/login']);
                }
            }
            return $this->redirect(['/site/login']);
        }catch(Exception $ex){
            $message = $ex->getMessage();
            if(YII_ENV_DEV){
                $message .= ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine();
            }
            throw new ServerErrorHttpException($message, 500);
        }
    }

    /**
     * @return Response
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Display page for reset password
     * @throws ServerErrorHttpException
     */
    public function actionResetPassword(): string
    {
        try {
            $this->layout = 'login';
            return $this->render('resetPassword', [
                'title' => $this->createPageTitle('Reset password'),
                'model' => new ForgotPasswordForm()
            ]);
        }catch(Exception $ex){
            $message = $ex->getMessage();
            if(YII_ENV_DEV){
                $message .= ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine();
            }
            throw new ServerErrorHttpException($message, 500);
        }
    }

    /**
     * @return Response
     * @throws ServerErrorHttpException
     */
    public function actionPasswordChangeCode(): Response
    {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $post = Yii::$app->request->post();
            $username = $post['ForgotPasswordForm']['username'];

            $user = User::find()->where(['username' => $username])->one();

            if($user){
                $passwordChangeCode = Yii::$app->getSecurity()->generateRandomString(64);
                $user->password_change_code = $passwordChangeCode;

                if($user->save()){
                    $employee = Employee::find()->select(['email', 'title', 'surname', 'other_names'])
                        ->where(['payroll_number' => $username])->asArray()->one();
                    $name = $employee['title'] . ' ' . $employee['other_names'];
                    $emails = [
                        'recipientEmail' => $employee['email'],
                        'subject' => 'PASSWORD RESET',
                        'params' => [
                            'recipient' => $name,
                            'username' => $username,
                            'code' => $passwordChangeCode
                        ]
                    ];
                    $layout = '@app/mail/layouts/html';
                    $view = '@app/mail/views/passwordReset';
                    SmisHelper::sendEmails([$emails], $layout, $view);
                }else{
                    if(!$user->validate()){
                        throw new Exception(SmisHelper::getModelErrors($user->getErrors()));
                    }else{
                        throw new Exception('The user was not created.');
                    }
                }
            }else{
                throw new UserException('User not found.');
            }

            $transaction->commit();
            $this->setFlash('success', 'Password reset', 'Follow instructions sent to your email.');
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        }catch (Exception $ex){
            $transaction->rollBack();
            $message = $ex->getMessage();
            if(YII_ENV_DEV){
                $message .= ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine();
            }
            throw new ServerErrorHttpException($message, 500);
        }
    }

    /**
     * @return string
     * @throws ServerErrorHttpException
     */
    public function actionEditPassword(): string
    {
        try {
            $get = Yii::$app->request->get();
            $username = $get['username'];
            $code = $get['token'];

            $user = User::find()->where(['username' => $username])->one();
            if(!$user || $user->password_change_code !== $code){
                throw new UserException('Missing url parameters.');
            }

            $this->layout = 'login';
            return $this->render('editPassword', [
                'title' => $this->createPageTitle('Change password'),
                'username' => $username,
                'code' => $code,
            ]);
        }catch (Exception $ex){
            $message = $ex->getMessage();
            if(YII_ENV_DEV){
                $message .= ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine();
            }
            throw new ServerErrorHttpException($message, 500);
        }
    }

    /**
     * @return Response
     */
    public function actionUpdatePassword(): Response
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $post = Yii::$app->request->post();
            $username = $post['username'];
            $code = $post['code'];

            $user = User::find()->where(['username' => $username])->one();
            if(!$user || $user->password_change_code !== $code){
                throw new UserException('Missing url parameters.');
            }

            $user->password = Yii::$app->getSecurity()->generatePasswordHash($post['newPassword']);
            $user->password_changed_at = SmisHelper::formatDate('now', 'Y-m-d h:i:s');
            $user->password_change_code = null;
            if(!$user->save()){
                if(!$user->validate()){
                    $transaction->rollBack();
                    $errorMessage = SmisHelper::getModelErrors($user->getErrors());
                    return $this->asJson(['success' => false, 'message' => $errorMessage]);
                }else{
                    throw new Exception('Password not updated.');
                }
            }
            $transaction->commit();
            return $this->asJson(['success' => true, 'message' => 'Password changed successfully.']);
        }catch (Exception $ex){
            $transaction->rollBack();
            $message = $ex->getMessage();
            if(YII_ENV_DEV){
                $message .= ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine();
            }
            return $this->asJson(['success' => false, 'message' => $message]);
        }
    }

    /**
     * @throws Exception
     */
    public function actionRbac()
    {
        $auth = Yii::$app->authManager;

//        // add "createPost" permission
//        $createPost = $auth->createPermission('createPost');
//        $createPost->description = 'Create a post';
//        $auth->add($createPost);
//
//        // add "author" role and give this role the "createPost" permission
//        $author = $auth->createRole('author');
//        $auth->add($author);
//        $auth->addChild($author, $createPost);

//        // add "updatePost" permission
//        $updatePost = $auth->createPermission('updatePost');
//        $updatePost->description = 'Update post';
//        $auth->add($updatePost);
//
//        // add "admin" role and give this role the "updatePost" permission
//        // as well as the permissions of the "author" role
//        $admin = $auth->createRole('admin');
//        $auth->add($admin);
//        $auth->addChild($admin, $updatePost);

//        $author = $auth->getRole('author');
//        $admin = $auth->getRole('admin');
//        $auth->addChild($admin, $author);

        if (\Yii::$app->user->can('createPost')) {
            dd('yes');
        }else{
            dd('no');
        }
    }
}
