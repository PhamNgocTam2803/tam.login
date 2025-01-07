<?php
use yii\helpers\Html;
$this->title = 'User Profile'; 
?>

<div class="user-profile">
    <h1><?= Html::encode($this->title) ?></h1>
    <p><strong>Full Name:</strong> <?= Html::encode($user->username) ?></p>
    <p><strong>Email:</strong> <?= Html::encode($user->email) ?></p>
</div>