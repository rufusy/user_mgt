<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 * @date: 5/8/2023
 * @time: 8:21 PM
 */

namespace app\models\search;

use app\models\AuthItem;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class RolesSearch extends AuthItem
{
    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['name'], 'safe']
        ];
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
        $query = AuthItem::find()
            ->select(['name', 'description'])
            ->where(['type' => 1])
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

        $query->filterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}