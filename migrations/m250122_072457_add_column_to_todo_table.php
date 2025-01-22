<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%todo}}`.
 */
class m250122_072457_add_column_to_todo_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('todo', 'delete', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('todo', 'delete');
    }
}
