<?php

use yii\db\Migration;

class m171108_023113_add_user_table extends Migration
{
    public function up()
    {
        $this->addColumn('user','last_login_time',$this->integer()->notNull()->comment('最后登录时间'));
        $this->addColumn('user','last_login_ip',$this->string()->notNull()->comment('最后登录时间'));
    }

    public function down()
    {
        echo "m171108_023113_add_user_table cannot be reverted.\n";

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
