<?php

use yii\db\Migration;

/**
 * Handles the creation of table `menu`.
 */
class m171110_033459_create_menu_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('menu', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(50)->notNull()->comment('菜单名称'),
            'parent'=>$this->integer()->notNull()->comment('上级菜单id'),
            'url'=>$this->string(50)->notNull()->comment('路由'),
            'depth'=>$this->integer(5)->notNull()->comment('深度'),
            'sort'=>$this->integer(20)->notNull()->comment('排序'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('menu');
    }
}
