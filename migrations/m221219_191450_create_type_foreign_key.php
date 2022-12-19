<?php

use yii\db\Migration;

/**
 * Class m221219_191450_create_type_foreign_key
 */
class m221219_191450_create_type_foreign_key extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex(
            'idx-file-type',
            '{{%file}}',
            'type'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-file-type',
            '{{%file}}',
            'type',
            'type',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221219_191450_create_type_foreign_key cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221219_191450_create_type_foreign_key cannot be reverted.\n";

        return false;
    }
    */
}
