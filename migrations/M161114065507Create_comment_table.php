<?php

namespace yuncms\comment\migrations;

use yii\db\Migration;

class M161114065507Create_comment_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%comment}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'to_user_id' => $this->integer()->unsigned()->comment('at某人的时候本字段不为空'),
            'source_id' => $this->integer()->notNull(),
            'source_type' => $this->string(100)->notNull(),
            'parent' => $this->integer(),
            'content' => $this->text()->notNull(),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull()->unsigned(),
        ], $tableOptions);
        $this->addForeignKey('comment_ibfk_1', '{{%comment}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');

        $this->addForeignKey('comment_ibfk_2', '{{%comment}}', 'to_user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');

        $this->addForeignKey('comment_ibfk_3', '{{%comment}}', 'parent', '{{%comment}}', 'id', 'CASCADE', 'CASCADE');
        $this->createIndex('comment_status', '{{%comment}}', ['status']);
        $this->createIndex('comment_sid_smodel', '{{%comment}}', ['source_id', 'source_type']);

    }

    public function down()
    {
        $this->dropTable('{{%comment}}');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
