<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 * @date: 4/26/2023
 */
namespace app\models;

use Exception;
use JetBrains\PhpStorm\ArrayShape;
use Yii;
use yii\base\InvalidArgumentException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "smis.users".
 *
 * @property int $id
 * @property string $uuid String to show on page urls
 * @property string $username Can be any attribute to identify a user eg payroll number, service number etc. It's a FK to a table with full user data eg employee table
 * @property string $password
 * @property string|null $password_change_code unique string to verify user account during password change requests
 * @property string|null $last_login_at
 * @property string|null $password_changed_at
 * @property string $created_at
 * @property string $updated_at
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return DB_SCHEMA . '.users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['username', 'created_at', 'updated_at'], 'required'],
            [['last_login_at', 'password_changed_at', 'created_at', 'updated_at'], 'safe'],
            [['username'], 'string', 'max' => 20],
            [['password', 'password_change_code'], 'string', 'max' => 255],
            [['username'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['username' => 'payroll_number']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'password_change_code' => 'Password Change Code',
            'last_login_at' => 'Last Login Time',
            'password_changed_at' => 'Password Changed At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id): User|IdentityInterface|null
    {
        return static::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null){}

    /**
     * {@inheritdoc}
     */
    public function getId(): int|string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey(){}

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey){}

    /**
     * @param string $username
     * @return array|ActiveRecord|null
     */
    public static function findByUsername(string $username): array|ActiveRecord|null
    {
        return self::find()->where(['username' => $username])->one();
    }

    /**
     * @param string $password
     * @return bool
     * @throws Exception
     */
    public function validatePassword(string $password): bool
    {
        try{
            if(Yii::$app->getSecurity()->validatePassword($password, $this->password)){
                return true;
            }else{
                return false;
            }
        }catch (InvalidArgumentException $ex){
            return false;
        }
    }

    /**
     * @throws Exception
     */
    #[ArrayShape(['plain' => "string", 'hash' => "string"])]
    public function generatePassword(): array
    {
        $passwordMaker = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz-*/_$#()&!+';
        $plainPassword = substr(str_shuffle($passwordMaker), 0, 8);

        try{
            $hashPassword = Yii::$app->getSecurity()->generatePasswordHash($plainPassword);
        }catch(Exception $ex){
            $message = 'Failed to generate password hash.';
            if(YII_ENV_DEV){
                $message .= ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine();
            }
            throw new Exception($message);
        }

        return [
            'plain' => $plainPassword,
            'hash' => $hashPassword
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getEmployee(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['payroll_number' => 'username']);
    }
}
