<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/14
 * Time: 15:58
 */

namespace frontend\models;


use yii\db\ActiveRecord;

class Article extends ActiveRecord
{
    public static function getArticle($article_id){
        return self::find()->where(['article_category_id'=>$article_id])->asArray()->all();
    }
}