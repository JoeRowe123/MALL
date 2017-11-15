<?php

use yii\db\Migration;

/**
 * Handles the creation of table `address`.
 */
class m171113_083529_create_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('address', [
            'id' => $this->primaryKey(),
            'member_id'=>$this->integer()->notNull()->comment('客户id'),
            'status'=>$this->integer(1)->notNull()->comment('是否默认地址'),
            'consignee'=>$this->string(50)->notNull()->comment('收货人'),
            'province'=>$this->string(30)->notNull()->comment('省'),
            'city'=>$this->string(20)->notNull()->comment('市/辖区'),
            'area'=>$this->string(20)->notNull()->comment('区/县'),
            'address'=>$this->string(255)->notNull()->comment('详细地址'),
            'tel'=>$this->char(11)->notNull()->comment('手机号码'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('address');
    }
}
