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
use yii\helpers\Url;

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
    //首页显示商品分类
    public static function getIndexGoodsCategory(){
        //使用redis进行性能优化(后台改变商品分类[添加修改删除],需要清除redis缓存)
        //缓存使用 先读缓存,有就直接用,没有就重写生成
        $redis = new \Redis();
        $redis->connect('127.0.0.1');
//        $redis->delete('goods-category');
        $html = $redis->get('goods-category');
        if($html == false){
            $html =  '<div class="cat_bd">';
            //遍历一级分类
            $categories = self::find()->where(['parent_id'=>0])->all();
            foreach ($categories as $k1=>$category){
                //第一个一级分类需要加class = item1
                $html .= '<div class="cat '.($k1==0?'item1':'').'">
                    <h3><a href='.Url::to(['goods/show','category_id'=>$category['id']]).'>'.$category->name.'</a><b></b></h3>
                    <div class="cat_detail">';
                //遍历该一级分类的二级分类
                $categories2 = $category->children(1)->all();
                foreach ($categories2 as $k2=>$category2){
                    $html .= '<dl '.($k2==0?'class="dl_1st"':'').'>
                            <dt><a href='.Url::to(['goods/show','category_id'=>$category2['id']]).'>'.$category2->name.'</a></dt>
                            <dd>';
                    //遍历该二级分类的三级分类
                    $categories3 = $category2->children(1)->all();
                    foreach ($categories3 as $category3){
                        $html .= '<a href='.Url::to(['goods/show','category_id'=>$category3['id']]).'>'.$category3->name.'</a>';
                    }
                    $html .= '</dd>
                        </dl>';
                }

                $html .= '</div>
                </div>';
            }
            $html .= '</div>';
            //保存到redis
            $redis->set('goods-category',$html,24*3600);
        }

        return $html;
    }
}