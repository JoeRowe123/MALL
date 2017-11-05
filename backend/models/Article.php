<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/3
 * Time: 17:42
 */

namespace backend\models;


use yii\db\ActiveRecord;

class Article extends ActiveRecord
{
        public $content;
        //获取分类id对应的分类名称
        public function getCategory(){
            return $this->hasOne(ArticleCategory::className(),['id'=>'article_category_id']);
        }
        public function rules(){
            return [
                [['name','intro','sort','status','article_category_id','content'],'required'],
            ];
        }
        public function attributeLabels(){
            return [
                'content'=>'内容',
                'name'=>'标题',
                'intro'=>'简介',
                'sort'=>'排序',
                'status'=>'状态',
                'article_category_id'=>'分类'
            ];
        }
}