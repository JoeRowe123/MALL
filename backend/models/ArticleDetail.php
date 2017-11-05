<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/3
 * Time: 17:53
 */

namespace backend\models;


use yii\db\ActiveRecord;

class ArticleDetail extends ActiveRecord
{
    //根据文章id获取标题
    public function getArticle(){
        return $this->hasOne(Article::className(),['id'=>'article_id']);
    }
    public function rules(){
        return [
            [['article_id','content'],'required'],
        ];
    }
    public function attributeLabels(){
        return [
            'article_id'=>'文章id',
            'content'=>'内容',
        ];
    }
}