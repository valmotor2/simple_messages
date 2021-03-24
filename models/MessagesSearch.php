<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Messages;

/**
 * MessagesSearch represents the model behind the search form of `app\models\Messages`.
 */
class MessagesSearch extends Messages
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['destination', 'message', 'name', 'when_send', 'status_desc', 'creat'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Messages::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder'=> ['id' => 'desc']]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'when_send' => $this->when_send,
            'creat' => $this->creat,
        ]);

        $query->andFilterWhere(['like', 'destination', $this->destination])
            ->andFilterWhere(['like', 'message', $this->message])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'status_desc', $this->status_desc]);

        return $dataProvider;
    }
}
