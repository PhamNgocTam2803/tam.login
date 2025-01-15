<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%library}}`.
 */
class m250115_072132_create_images_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%images}}', [
            'id' => $this->primaryKey(),
            'path' => $this->text(),
            'created_date' => $this->timestamp(),
            'deleted_date' => $this->timestamp(),
            'user_id' => $this->integer(),
            
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%images}}');
    }
}
