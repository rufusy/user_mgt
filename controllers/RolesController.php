<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 * @date: 5/8/2023
 * @time: 8:19 PM
 */

namespace app\controllers;

use app\helpers\SmisHelper;
use app\models\AuthItem;
use app\models\search\RolesSearch;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use Yii;
use yii\base\UserException;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

class RolesController extends BaseController
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
            $rolesSearchModel = new RolesSearch();
            $rolesDataProvider = $rolesSearchModel->search(Yii::$app->request->queryParams);

            return $this->render('index', [
                'title' => $this->createPageTitle('roles'),
                'rolesSearchModel' => $rolesSearchModel,
                'rolesDataProvider' => $rolesDataProvider
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
    public function actionEdit(): string
    {
        try{
            $get = Yii::$app->request->get();
            $name = $get['name'];

            if(empty($name)){
                throw new UserException('Missing url parameters');
            }

            $role = AuthItem::find()->select(['name', 'description'])->where(['type' => 1 , 'name' => $name])
                ->asArray()->one();

//            $permissions = AuthItem::find()->select(['name', 'description'])->where(['type' => 2, 'name' => $name])
//                ->asArray()->all();

            return $this->render('edit', [
                'title' => $this->createPageTitle('edit role ' . $name),
                'role' => $role,
                'permissions' => []
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
    public function actionSave(): Response
    {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $post = Yii::$app->request->post();
            $name = $post['name'];
            $description = $post['description'];

            $roleCount = AuthItem::find()->where(['name' => $name, 'type' => 1])->count();
            if($roleCount > 0){
                throw new UserException('This role already exists');
            }

            $authItem = new AuthItem();
            $authItem->name = $name;
            $authItem->description = $description;
            $authItem->type = 1;
            $time = time();
            $authItem->created_at = $time;
            $authItem->updated_at = $time;

            if(!$authItem->save()){
                if(!$authItem->validate()){
                    throw new Exception(SmisHelper::getModelErrors($authItem->getErrors()));
                }else{
                    throw new Exception('The role was not created.');
                }
            }
            $transaction->commit();
            $this->setFlash('success', 'Roles', 'Roles created successfully.');
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
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