<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 * @date: 4/26/2023
 * @time: 6:29 PM
 */

namespace app\models\search;

use app\models\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class UsersSearch extends User
{
    /**
     * @return array
     */
    public function attributes(): array
    {
        return [];
    }

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios(): array
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = User::find()->alias('u')
            ->select([
                'u.id',
                'u.username',
                'u.created_at',
                'u.last_login_at'
            ])
            ->joinWith(['employee e' => function(ActiveQuery $q){
                $q->select([
                    'e.id',
                    'e.payroll_number',
                    'e.surname',
                    'e.other_names',
                    'e.title',
                    'e.email',
                    'e.phone_number',
                    'e.dept_code',
                    'e.faculty_code'
                ]);
            }], true, 'INNER JOIN')
            ->asArray();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pagesize' => 50,
            ],
        ]);

        $this->load($params);

        if(!$this->validate()) {
            return $dataProvider;
        }

        /**
         * Add filters
         */

        return $dataProvider;
    }
}