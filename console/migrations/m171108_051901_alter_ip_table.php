<?php

use yii\db\Migration;

class m171108_051901_alter_ip_table extends Migration
{
    public function up()
    {
        $this->alterColumn('user','last_login_ip',$this->bigInteger()->notNull()->comment('最后登录ip'));
        $this->alterColumn('user','last_login_time',$this->integer(255)->defaultValue(0)->notNull()->comment('最后登录时间'));
        $this->alterColumn('user','status',$this->smallInteger()->notNull()->defaultValue(1)->comment('状态'));

    }

    public function down()
    {
        echo "m171108_051901_alter_ip_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
