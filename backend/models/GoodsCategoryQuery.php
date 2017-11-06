<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/5
 * Time: 17:03
 */

namespace backend\models;

use yii\db\ActiveQuery;
use creocoder\nestedsets\NestedSetsQueryBehavior;
class GoodsCategoryQuery extends ActiveQuery
{
    public function behaviors() {
        return [
            NestedSetsQueryBehavior::className(),
        ];
    }
}