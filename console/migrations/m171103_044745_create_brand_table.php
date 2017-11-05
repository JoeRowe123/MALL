<?php

use yii\db\Migration;

/**
 * Handles the creation of table `brand`.
 */
class m171103_044745_create_brand_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('brand', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(50)->notNull()->comment('名称'),
            'intro'=>$this->text()->notNull()->comment('简介'),
            'logo'=>$this->string(100)->comment('商标'),
            'sort'=>$this->integer(11)->comment('排序'),
            'status'=>$this->integer(3)->notNull()->comment('状态')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('brand');
    }
}
