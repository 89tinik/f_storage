<?php

use yii\db\Migration;

/**
 * Class m221219_184506_create_foreign_key
 */
class m221219_184506_create_foreign_key extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    { // creates index for column `user_id`
        $this->createIndex(
            'idx-file-parent_id',
            '{{%file}}',
            'parent_id'
        );

        // add foreign key for table `user`
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
        echo "m221219_184506_create_foreign_key cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221219_184506_create_foreign_key cannot be reverted.\n";

        return false;
    }
    */
}
