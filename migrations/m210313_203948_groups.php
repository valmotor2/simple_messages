<?php

use yii\db\Migration;

/**
 * Class m210313_203948_groups
 */
class m210313_203948_groups extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('groups', [
            'id' => $this->primaryKey(),
            'group_name' => $this->string()->notNull(),
            'full_name' => $this->string(),
            'destination' => $this->string()->notNull(),
            'creat' => $this->dateTime() . ' DEFAULT NOW()',
        ]);

        $this->createIndex(
            'idx-group_name_desc',
            'groups',
            'group_name'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210313_203948_groups cannot be reverted.\n";

        $this->dropTable('groups');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210313_203948_groups cannot be reverted.\n";

        return false;
    }
    */
}
