<?php

use yii\db\Migration;

/**
 * Class m210313_203940_messages
 */
class m210313_203940_messages extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('messages', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'destination' => $this->string()->notNull(),
            'message' => $this->text()->notNull(),
            'status_desc' => $this->string()->notNull(),
            'when_send' => $this->dateTime() . ' DEFAULT NOW()',
            'creat' => $this->dateTime() . ' DEFAULT NOW()',
        ]);

        $this->createIndex(
            'idx-messages-status_desc',
            'messages',
            'status_desc'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210313_203940_messages cannot be reverted.\n";

        $this->dropTable('messages');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210313_203940_messages cannot be reverted.\n";

        return false;
    }
    */
}
