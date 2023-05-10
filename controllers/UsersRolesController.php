<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 * @date: 5/2/2023
 * @time: 9:55 AM
 */

namespace app\controllers;

use app\helpers\SmisHelper;
use app\models\AuthAssignment;
use app\models\AuthItem;
use app\models\User;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;
use Yii;
use yii\base\UserException;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

class UsersRolesController extends BaseController
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
    public function actionCreate(): string
    {
        try{
            $get = Yii::$app->request->get();
            $username = $get['username'];

            $user = $this->getUser($username);
            if(empty($user)){
                throw new UserException('The user to grant selected roles was not found.');
            }

            $assignedRolesQuery = AuthAssignment::find()->select(['item_name'])->where(['user_id' => $user['id']]);

            $roles = AuthItem::find()->where(['type' => 1])->andWhere(['NOT IN', 'name', $assignedRolesQuery])
                ->asArray()->all();

            $assignedRoles = $assignedRolesQuery->asArray()->all();

            return $this->render('create', [
                'title' => $this->createPageTitle('assign and manage roles'),
                'roles' => $roles,
                'assignedRoles' => $assignedRoles,
                'username' => $username
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
            $roles = $post['roles'];
            $username = $post['username'];

            $user = $this->getUser($username);
            if(empty($user)){
                throw new UserException('The user to grant selected roles was not found.');
            }

            if(!empty($roles)){
                $this->saveRoleAssignments($user['id'], $roles);
            }else{
                throw new Exception('No user roles have been selected to assign the user.');
            }

            $transaction->commit();
            $this->setFlash('success', 'User roles', 'User roles assigned successfully.');
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

    /**
     * @return Response
     * @throws Throwable
     */
    public function actionDelete(): Response
    {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $post = Yii::$app->request->post();
            $roles = $post['roles'];
            $username = $post['username'];

            $user = $this->getUser($username);
            if(empty($user)){
                throw new UserException('The user to grant selected roles was not found.');
            }

            if(!empty($roles)){
                $this->deleteRoleAssignments($user['id'], $roles);
            }else{
                throw new Exception('No user roles have been selected for removal from the user.');
            }

            $transaction->commit();
            $this->setFlash('success', 'User roles', 'Roles removed from user successfully.');
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

    /**
     * @return Response
     */
    public function actionSaveFromExcel(): Response
    {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            if(empty($_FILES)){
                $this->setFlash('danger', 'User roles', 'No documents selected for upload.');
                return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
            }

            $userRoles = $this->getUserRolesFromExcelFile($_FILES);

            foreach ($userRoles as $username => $roles){
                // Check if user exists
                $user = $this->getUser($username);
                if(empty($user)){
                    continue;
                }

                if(empty($roles)){
                    continue;
                }
                $this->saveRoleAssignments($user['id'], $roles);
            }

            $transaction->commit();
            $this->setFlash('success', 'User roles', 'User roles assigned successfully.');
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

    /**
     * @return Response
     * @throws Throwable
     */
    public function actionDeleteFromExcel(): Response
    {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            if(empty($_FILES)){
                $this->setFlash('danger', 'User roles', 'No documents selected for upload.');
                return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
            }

            $userRoles = $this->getUserRolesFromExcelFile($_FILES);

            foreach ($userRoles as $username => $roles){
                // Check if user exists
                $user = $this->getUser($username);
                if(empty($user)){
                    continue;
                }

                if(empty($roles)){
                    continue;
                }

                $this->deleteRoleAssignments($user['id'], $roles);
            }

            $transaction->commit();
            $this->setFlash('success', 'User roles', 'Roles removed from users successfully.');
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

    /**
     * @return \yii\console\Response|Response
     * @throws ServerErrorHttpException
     */
    public function actionDownloadUserRoleTemplate(): Response|\yii\console\Response
    {
        try{
            $filepath = Yii::getAlias('@userRolesUploadUrl') . 'user_roles_template.xlsx';
            return Yii::$app->response->sendFile($filepath, 'user_roles_template', ['inline' => true]);
        }catch(Exception $ex){
            $message = $ex->getMessage();
            if(YII_ENV_DEV){
                $message .= ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine();
            }
            throw new ServerErrorHttpException($message, 500);
        }
    }

    /**
     * @param string $username
     * @return array|ActiveRecord|null
     */
    private function getUser(string $username): array|ActiveRecord|null
    {
        return User::find()->select(['id'])->where(['username' => $username])->asArray()->one();
    }

    /**
     * @param string $userId
     * @param array $roles
     * @return void
     * @throws Exception
     */
    private function saveRoleAssignments(string $userId, array $roles): void
    {
        foreach ($roles as $role){
            // Check if role exists
            $roleCount = AuthItem::find()->where(['type' => 1, 'name' => $role])->count();
            if($roleCount < 1){
                continue;
            }

            // Check if user has this role
            $authAssignmentCount = AuthAssignment::find()->where(['item_name' => $role, 'user_id' => $userId])->count();
            if($authAssignmentCount > 0){
                continue;
            }

            $authAssignment = new AuthAssignment();
            $authAssignment->item_name = $role;
            $authAssignment->user_id = strval($userId);
            $authAssignment->created_at = time();
            if(!$authAssignment->save()){
                if(!$authAssignment->validate()){
                    throw new Exception(SmisHelper::getModelErrors($authAssignment->getErrors()));
                }else{
                    throw new Exception('The role ' . $role . ' failed to be assigned to user.');
                }
            }
        }
    }

    /**
     * @param string $userId
     * @param array $roles
     * @return void
     * @throws Throwable
     * @throws StaleObjectException
     */
    private function deleteRoleAssignments(string $userId, array $roles): void
    {
        foreach ($roles as $role){
            $authAssignment = AuthAssignment::find()->where(['item_name' => $role, 'user_id' => $userId])->one();
            if($authAssignment){
                if(!$authAssignment->delete()){
                    throw new Exception('The role ' . $role . ' was not removed from the user.');
                }
            }
        }
    }

    /**
     * @param array $files
     * @return string
     * @throws Exception
     */
    private function uploadFile(array $files): string
    {
        $path = Yii::getAlias('@userRolesUploadUrl');
        if(!is_dir($path)){
            if(!mkdir($path, 0777, true)){
                throw new Exception('Failed to create uploads directory.');
            }
        }

        $file = $files['user-roles'];

        if($file['error'] !== 0){
            throw new Exception('An error occurred while trying to upload files.');
        }

        $validExtensions = ['xlsx'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if(!in_array($ext, $validExtensions)){
            throw new Exception('File extension not allowed.');
        }

        $newFileName = strtolower(pathinfo($file['name'], PATHINFO_FILENAME));
        $newFileName = preg_replace('/\s/','_', $newFileName);
        $newFileName .= '_' . time() . '.' . $ext;

        $destinationFile = $path . $newFileName;

        if(!move_uploaded_file($file['tmp_name'], $destinationFile)){
            throw new Exception('Failed to move uploaded file.');
        }

        return $newFileName;
    }

    /**
     * @param string $filePath
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    private function readExcelFile(string $filePath): array
    {
        //Read file
        $newSheetData = [];
        $inputFileName = $filePath;
        $inputFileType = IOFactory::identify($inputFileName);
        $reader = IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($inputFileName);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // Some cells contain null values. Take these out
        foreach($sheetData as $data){
            $dataKeys = array_keys($data);
            foreach($dataKeys as $key){
                if(is_null($data[$key])){
                    unset($data[$key]);
                }
            }
            $newSheetData[] = $data;
        }

        // Remove column titles
        array_shift($newSheetData);

        return $newSheetData;
    }

    /**
     * @param array $files
     * @return array|Response
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws Exception
     */
    private function getUserRolesFromExcelFile(array $files): Response|array
    {
        // Save user roles excel file
        $filename = $this->uploadFile($files);
        $filepath = Yii::getAlias('@userRolesUploadUrl') . $filename;

        // Read and group user roles together
        $rolesToAssign = $this->readExcelFile($filepath);
        $usernameCol = 'A';
        $roleCol = 'B';
        $users = [];
        $userRoles = [];
        if(empty($rolesToAssign)){
            $this->setFlash('danger', 'User roles', 'Roles not found in file or file failed to read.');
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        }else{
            // Get unique username
            foreach ($rolesToAssign as $roleToAssign){
                $users[] = $roleToAssign[$usernameCol];
            }
            $users = array_unique($users);

            // For each username, get their roles
            foreach ($users as $user){
                $roles = [];
                foreach ($rolesToAssign as $key => $roleToAssign){
                    if($roleToAssign[$usernameCol] === $user){
                        $roles[] = $roleToAssign[$roleCol];
                        // This role is already associated to a user, now remove it from future checks
                        unset($rolesToAssign[$key]);
                    }
                }
                $userRoles[$user] = $roles;
            }
        }
        return $userRoles;
    }
}