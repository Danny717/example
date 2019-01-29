<?php

use yii\db\Migration;

/**
 * Handles the creation of table `player`.
 */
class m190122_175724_create_player_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('player', [
            'id' => $this->primaryKey(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('player');
    }
}
