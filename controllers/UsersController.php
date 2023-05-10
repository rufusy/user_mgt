<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 * @date: 4/26/2023
 * @time: 7:04 PM
 */

namespace app\controllers;

use app\helpers\SmisHelper;
use app\models\Employee;
use app\models\search\UsersSearch;
use app\models\User;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

class UsersController extends BaseController
{
    /**
     * Configure controller behaviours
     * @return array[]
     */
    #[ArrayShape(['access' => "array"])]
    public function behaviors(): array
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
            ],
        ];
    }

    /**
     * @return string
     * @throws ServerErrorHttpException
     */
    public function actionIndex(): string
    {
        try{
            $usersSearchModel = new UsersSearch();
            $usersDataProvider = $usersSearchModel->search(Yii::$app->request->queryParams);

            return $this->render('index', [
                'title' => $this->createPageTitle('users'),
                'usersSearchModel' => $usersSearchModel,
                'usersDataProvider' => $usersDataProvider
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
     * @return string
     * @throws ServerErrorHttpException
     */
    public function actionCreate(): string
    {
        try{
            return $this->render('create', [
                'title' => $this->createPageTitle('create a new user'),
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
     * @todo Add default user status
     */
    public function actionSave(): Response
    {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $post = Yii::$app->request->post();
            $username = $post['username'];

            $userCount = User::find()->where(['username' => $username])->count();
            if($userCount > 0){
                $this->setFlash('danger', 'New user', 'Username is already taken.');
                return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
            }

            $user = new User();
            $user->username = $post['username'];
            $passwordChangeCode = Yii::$app->getSecurity()->generateRandomString(64);
            $user->password_change_code = $passwordChangeCode;
            $user->last_login_at = null;
            $time = SmisHelper::formatDate('now', 'Y-m-d h:i:s');
            $user->created_at = $time;
            $user->updated_at = $time;

            if($user->save()){
                $employee = Employee::find()->select(['email', 'title', 'surname', 'other_names'])
                    ->where(['payroll_number' => $username])->asArray()->one();
                $name = $employee['title'] . ' ' . $employee['other_names'];
                $emails = [
                    'recipientEmail' => $employee['email'],
                    'subject' => 'NEW USER ACCOUNT',
                    'params' => [
                        'recipient' => $name,
                        'username' => $username,
                        'code' => $passwordChangeCode
                    ]
                ];
                $layout = '@app/mail/layouts/html';
                $view = '@app/mail/views/newUser';
                SmisHelper::sendEmails([$emails], $layout, $view);
            }else{
                if(!$user->validate()){
                    throw new Exception(SmisHelper::getModelErrors($user->getErrors()));
                }else{
                    throw new Exception('The user was not created.');
                }
            }
            $transaction->commit();
            return $this->asJson(['success' => true, 'message' => 'User created successfully.']);
        }catch (Exception $ex){
            $transaction->rollBack();
            $message = $ex->getMessage();
            if(YII_ENV_DEV){
                $message .= ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine();
            }
            return $this->asJson(['success' => false, 'message' => $message]);
        }
    }
}