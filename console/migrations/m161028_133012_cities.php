<?php

use yii\db\Migration;

class m161028_133012_cities extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('cities', [
            'id' => $this->primaryKey()->unsigned(),
            'regions_id' => $this->integer()->unsigned()->notNull(),
            'domain' => $this->string()->notNull(),
            'active' => $this->boolean()->defaultValue(1),
            'meta_google' => $this->string(),
            'meta_yandex' => $this->string(),
            'longitude' => $this->string(100),
            'latitude' => $this->string(100)
        ], $tableOptions);

//        $this->createIndex('regions_id', 'cities', 'regions_id');
        $this->addForeignKey('fk_cities_region', 'cities', 'regions_id', 'regions', 'id', 'CASCADE', 'CASCADE');

        $this->addcommentOnColumn('cities','longitude','Долгота');

        $this->addcommentOnColumn('cities','latitude','Широта');
    }

    public function down()
    {
        $this->dropTable('cities');
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
