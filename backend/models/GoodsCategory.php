<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/5
 * Time: 16:59
 */

namespace backend\models;

use creocoder\nestedsets\NestedSetsBehavior;
use yii\db\ActiveRecord;

class GoodsCategory extends ActiveRecord
{
    public function getCategory(){
        return $this->hasOne(GoodsCategory::class,['id'=>'parent_id']);
    }
    public static function tableName()
    {
        return 'goods_category';
    }
    public function rules(){
        return [
            [['name', 'parent_id'], 'required'],
            [['tree', 'lft', 'rgt', 'depth', 'parent_id'], 'integer'],
            [['intro'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels(){
        return [
            'id' => 'ID',
            'tree' => 'Tree',
            'lft' => 'Lft',
            'rgt' => 'Rgt',
            'depth' => 'Depth',
            'name' => '商品（类）名',
            'parent_id' => '商品类别',
            'intro' => '简介',
        ];
    }
    //nested-set  文档
    public function behaviors() {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                 'treeAttribute' => 'tree',
                // 'leftAttribute' => 'lft',
                // 'rightAttribute' => 'rgt',
                // 'depthAttribute' => 'depth',
            ],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        return new GoodsCategoryQuery(get_called_class());
    }
    //未ztree展示上级分类获取商品分类的父节点
    public static function getNodes(){
        return self::find()->select(['name','parent_id','id'])->asArray()->all();
    }
}