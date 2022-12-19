<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m221214_114824_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'email' => $this->string(255)->notNull(),
            'password' => $this->string(255)->notNull(),
            'auth_key' => $this->string(255),
            'storage_size' => $this->integer()->defaultValue(0),
            'max_storage_size' => $this->integer()->defaultValue(100),
            'max_file_size' => $this->integer()->defaultValue(10),
            'blocked' => $this->integer()->defaultValue(1),
            'admin' => $this->integer()->defaultValue(0),
        ], 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
