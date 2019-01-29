<?php

use yii\db\Migration;

/**
 * Handles the creation of table `team`.
 */
class m190122_103147_create_team_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('team', [
            'id' => $this->primaryKey(),
            'title' => $this->string(100)->notNull(),
            'type' => $this->smallInteger(2)->notNull()->unsigned(),
            'logo' => $this->string(100),
            'country' => $this->string(50)->notNull(),
            'city' => $this->string(50)->notNull()
        ],'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('team');
    }
}
