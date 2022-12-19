<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%type}}`.
 */
class m221214_122619_create_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%type}}', [
            'id' => $this->primaryKey(),
            'icon' => $this->string(255),
        ], 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB');

        $this->batchInsert('{{%type}}', ['icon'], [['images/icon/default.png'],['images/icon/word.png'],[NULL]]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%type}}');
    }
}
