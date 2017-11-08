<?php

use yii\db\Migration;

/**
 * Handles the creation of table `goods`.
 */
class m171106_034557_create_goods_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('goods', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(20)->notNull()->comment('商品名称'),
            'sn'=>$this->string(20)->notNull()->comment('货号'),
            'logo'=>$this->string(255)->notNull()->comment('logo'),
            'goods_category_id'=>$this->integer()->notNull()->comment('商品分类id'),
            'brand_id'=>$this->integer()->notNull()->comment('品牌分类id'),
            'market_price'=>$this->decimal(10,2)->notNull()->comment('市场价格'),
            'shop_price'=>$this->decimal(10,2)->notNull()->comment('商品价格'),
            'stock'=>$this->integer(10)->notNull()->comment('库存'),
            'is_on_sale'=>$this->integer(1)->notNull()->comment('是否在售(1在售 0下架)'),
            'status'=>$this->integer(1)->notNull()->comment('状态(1正常 0回收站)'),
            'sort'=>$this->integer()->notNull()->comment('排序'),
            'create_time'=>$this->integer()->notNull()->comment('添加时间'),
            'view_times'=>$this->integer()->notNull()->comment('浏览次数'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('goods');
    }
}
