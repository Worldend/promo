<?php

use yii\db\Migration;

class m161126_233728_categories_text_table_create extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {

            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('categories_text', [
            'id' => $this->primaryKey()->unsigned(),
            'categories_id' => $this->integer()->unsigned()->notNull(),
            'languages_id' => $this->integer()->unsigned()->notNull(),
            'name' => $this->string()->notNull(),
            'url' => $this->string()->notNull(),
            'seo_title' => $this->string(),
            'seo_desc' => $this->string(),
            'seo_keywords' => $this->string(),
        ], $tableOptions);

        $this->addForeignKey('fk_categories_text_categories', 'categories_text', 'categories_id', 'categories', 'id', 'CASCADE', 'CASCADE');

        $this->addForeignKey('fk_languages_for_language', 'categories_text', 'languages_id', 'languages', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('fk_categories_text_categories', 'categories_text');
        $this->dropForeignKey('fk_languages_for_language', 'categories_text');
        $this->dropTable('regions_text');
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
