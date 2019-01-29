<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Product;

/**
 * ProductSearch represents the model behind the search form of `common\models\Product`.
 */
class ProductSearch extends Product
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'buybox', 'availability'], 'integer'],
            [['title', 'amazon_link', 'target_link', 'walmart_link', 'hayneedle_link', 'waifair_link', 'amazon_price', 'target_price', 'walmart_price', 'hayneedle_price', 'waifair_price', 'update_time', 'img', 'asin'], 'safe'],
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
        $query = Product::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'update_time' => $this->update_time,
            'buybox' => $this->buybox,
            'availability' => $this->availability,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'amazon_link', $this->amazon_link])
            ->andFilterWhere(['like', 'target_link', $this->target_link])
            ->andFilterWhere(['like', 'walmart_link', $this->walmart_link])
            ->andFilterWhere(['like', 'hayneedle_link', $this->hayneedle_link])
            ->andFilterWhere(['like', 'waifair_link', $this->waifair_link])
            ->andFilterWhere(['like', 'amazon_price', $this->amazon_price])
            ->andFilterWhere(['like', 'target_price', $this->target_price])
            ->andFilterWhere(['like', 'walmart_price', $this->walmart_price])
            ->andFilterWhere(['like', 'hayneedle_price', $this->hayneedle_price])
            ->andFilterWhere(['like', 'waifair_price', $this->waifair_price])
            ->andFilterWhere(['like', 'img', $this->img])
            ->andFilterWhere(['like', 'asin', $this->asin]);

        return $dataProvider;
    }
}
