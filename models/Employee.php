<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 * @date: 4/26/2023
 */
namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "smis.employees".
 *
 * @property int $id
 * @property string $payroll_number Can also be service number
 * @property string $surname
 * @property string $other_names
 * @property string $title
 * @property string $email
 * @property string $phone_number
 * @property string $dept_code
 * @property string $faculty_code
 * @property string $job_cadre
 */
class Employee extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return DB_SCHEMA . '.um_employees';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['payroll_number', 'surname', 'other_names', 'title', 'email', 'phone_number', 'dept_code', 'faculty_code', 'job_cadre'], 'required'],
            [['payroll_number', 'phone_number'], 'string', 'max' => 20],
            [['surname', 'other_names'], 'string', 'max' => 255],
            [['title', 'email'], 'string', 'max' => 50],
            [['dept_code', 'faculty_code'], 'string', 'max' => 10],
            [['job_cadre'], 'string', 'max' => 15],
            [['payroll_number'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'payroll_number' => 'Payroll Number',
            'surname' => 'Surname',
            'other_names' => 'Other Names',
            'title' => 'Title',
            'email' => 'Email',
            'phone_number' => 'Phone Number',
            'dept_code' => 'Dept Code',
            'faculty_code' => 'Faculty Code',
            'job_cadre' => 'Job Cadre',
        ];
    }
}
