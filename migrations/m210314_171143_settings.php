<?php

use yii\db\Migration;

/**
 * Class m210314_171143_settings
 */
class m210314_171143_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('settings', [
            'id' => $this->primaryKey(),
            'desc' => $this->string()->notNull()->unique(),
            'options' => $this->json(),
            'creat' => $this->dateTime() . ' DEFAULT NOW()',
        ]);

        $this->createIndex(
            'idx-settings_desc',
            'settings',
            'desc'
        );


        $this->insert('settings', [
            'desc' => 'messages',
            'options' => json_encode([
                'send_messages_received_to_mail' => [],
                'api' => [
                    'tokens' => [],
                    'forward_messages_received_to_url' => [],
                ]
            ])
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210314_171143_settings cannot be reverted.\n";
        $this->dropTable('settings');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210314_171143_settings cannot be reverted.\n";

        return false;
    }
    */
}
