<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 * @date: 5/8/2023
 * @time: 10:16 PM
 */

namespace app\models;

use JetBrains\PhpStorm\ArrayShape;
use yii\base\Model;

/**
 * ForgotPasswordForm is the model behind the forgot password form.
 */
class ForgotPasswordForm extends Model
{
    public $username;

    public $email;

    /**
     * @return array the validation rules.
     */
    public function rules(): array
    {
        return [
            [['username'], 'required'],
            [['email', 'username'], 'string'],
            [['email', 'username'], 'trim'],
            [['email', 'username'], 'default'],
            ['email', 'email'],
        ];
    }

    #[ArrayShape(['email' => "string", 'username' => "string"])]
    public function attributeLabels(): array
    {
        return [
            'email' => 'Email address',
            'username' => 'username'
        ];
    }
}