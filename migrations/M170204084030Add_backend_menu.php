<?php

namespace yuncms\comment\migrations;

use yii\db\Migration;

class M170204084030Add_backend_menu extends Migration
{
    public function up()
    {
        $this->insert('{{%admin_menu}}', [
            'name' => '评论管理',
            'parent' => 8,
            'route' => '/comment/comment/index',
            'icon' => 'fa-file-text-o',
            'sort' => NULL,
            'data' => NULL
        ]);

        $id = (new \yii\db\Query())->select(['id'])->from('{{%admin_menu}}')->where(['name' => '评论管理', 'parent' => 8,])->scalar($this->getDb());
        $this->batchInsert('{{%admin_menu}}', ['name', 'parent', 'route', 'visible', 'sort'], [
            ['评论审核', $id, '/comment/comment/audit', 0, NULL],
        ]);
    }

    public function down()
    {
        $id = (new \yii\db\Query())->select(['id'])->from('{{%admin_menu}}')->where(['name' => '评论管理', 'parent' => 8,])->scalar($this->getDb());
        $this->delete('{{%admin_menu}}', ['parent' => $id]);
        $this->delete('{{%admin_menu}}', ['id' => $id]);
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
