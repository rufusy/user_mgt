<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%testusers}}`.
 */
class m230425_170750_create_testusers_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%testusers}}', [
            'id' => $this->primaryKey(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%testusers}}');
    }
}
