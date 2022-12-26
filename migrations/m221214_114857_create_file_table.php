<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%file}}`.
 */
class m221214_114857_create_file_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%file}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255),
            'path' => $this->string(255),
            'public_link' => $this->integer()->defaultValue(0),
            'size' => $this->integer(),
            'created' => $this->date(),
            'parent_id' => $this->integer(),
            'thumb' => $this->string(255),
            'user_id' => $this->integer(),
        ], 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB');

        // creates index for column `user_id`
        $this->createIndex(
            'idx-file-user_id',
            '{{%file}}',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-file-user_id',
            '{{%file}}',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        // creates index for column `parent_id`
        $this->createIndex(
            'idx-file-parent_id',
            '{{%file}}',
            'parent_id'
        );

        // add foreign key for table `folder`
        $this->addForeignKey(
            'fk-file-parent_id',
            '{{%file}}',
            'parent_id',
            'folder',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%file}}');
    }
}
