<?php

use yii\db\Migration;

/**
 * Handles the creation of table `product`.
 */
class m190125_094051_create_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('product', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'amazon_link' => $this->string(),
            'target_link' => $this->string(),
            'walmart_link' => $this->string(),
            'hayneedle_link' => $this->string(),
            'waifair_link' => $this->string(),
            'amazon_price' => $this->string(),
            'target_price' => $this->string(),
            'walmart_price' => $this->string(),
            'hayneedle_price' => $this->string(),
            'waifair_price' => $this->string(),
            'update_time' => $this->dateTime()->notNull(),
            'img' =>$this->string(),
            'asin' =>$this->string(),
            'buybox' =>$this->smallInteger(1)->unsigned(),
            'availability' =>$this->smallInteger(1)->unsigned()
        ],'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('product');
    }
}
