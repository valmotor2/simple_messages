<?php

use yii\db\Migration;

/**
 * Class m210315_115106_service_sms_send
 */
class m210315_115106_service_sms_send extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('sms_send', [
            'id' => $this->primaryKey(),
            'sql_id' => $this->bigInteger(),
            'momt' => "ENUM('MO', 'MT')",
            'sender' => $this->string(20),
            'receiver' => $this->string(20),
            'udhdata' => 'BLOB',
            'msgdata' => $this->text(),
            'time' => $this->bigInteger(),
            'smsc_id' => $this->string(60),
            'service' => $this->string(),
            'account' => $this->string(),
            'sms_type' => $this->bigInteger(),
            'mclass' => $this->bigInteger(),
            'mwi' => $this->bigInteger(),
            'coding' => $this->bigInteger(),
            'compress' => $this->bigInteger(),
            'validity' => $this->bigInteger(),
            'deferred' => $this->bigInteger(),
            'dlr_mask' => $this->bigInteger(),
            'dlr_url' => $this->string(),
            'pid' => $this->bigInteger(),
            'alt_dcs' => $this->bigInteger(),
            'rpi' => $this->bigInteger(),
            'charset' => $this->string(20),
            'boxc_id' => $this->string(20),
            'binfo' => $this->string(),
            'meta_data' => $this->text(),
            'foreign_id' => $this->string(),
            'priority' => $this->string(),
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210315_115106_service_sms_send cannot be reverted.\n";
        $this->dropTable('sms_send');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210315_115106_service_sms_send cannot be reverted.\n";

        return false;
    }
    */
}
