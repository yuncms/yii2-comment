<?php

namespace yuncms\comment\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yuncms\comment\models\Comment;

/**
 * CommentSearch represents the model behind the search form about `yuncms\comment\models\Comment`.
 */
class CommentSearch extends Comment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'source_id', 'parent', 'status', 'created_at'], 'integer'],
            [['source_type', 'content'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = Comment::find();

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
            'user_id' => $this->user_id,
            'source_id' => $this->source_id,
            'parent' => $this->parent,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'source_type', $this->source_type])
            ->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }
}
