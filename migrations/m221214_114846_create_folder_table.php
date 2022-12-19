<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%filder}}`.
 */
class m221214_114846_create_folder_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%folder}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255),
            'parent_id' => $this->integer(),
            'user_id' => $this->integer(),
        ], 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB');

        // creates index for column `user_id`
        $this->createIndex(
            'idx-folder-user_id',
            '{{%folder}}',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-folder-user_id',
            '{{%folder}}',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%folder}}');
    }
}
