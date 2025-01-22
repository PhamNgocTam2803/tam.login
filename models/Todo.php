<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "todo".
 *
 * @property int $id
 * @property string|null $task_name
 * @property string|null $status
 * @property int|null $user_id
 */
class Todo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'todo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_name'], 'string'],
            [['user_id'], 'integer'],
            [['status'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_name' => 'Task Name',
            'status' => 'Status',
            'user_id' => 'User ID',
        ];
    }

    //One user has many todo tasks
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
