<?php

use yii\db\Migration;

/**
 * Class m210324_125252_dlr
 */
class m210324_125252_dlr extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('dlr', [
            'smsc' => $this->string(40),
            'ts' => $this->string(40),
            'source' => $this->string(40),
            'destination' => $this->string(40),
            'service' => $this->string(40),
            'url' => $this->string(),
            'mask' => $this->integer(),
            'boxc' => $this->string(40),
            'status' => $this->integer()
        ]);

        $this->createIndex(
            'idx-dlr-smsc',
            'dlr',
            'smsc'
        );
        
        $this->createIndex(
            'idx-dlr-ts',
            'dlr',
            'ts'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210324_125252_dlr cannot be reverted.\n";
        $this->dropTable('dlr');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210324_125252_dlr cannot be reverted.\n";

        return false;
    }
    */
}
