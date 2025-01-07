<?php
use yii\helpers\Html;

$this->title = 'Đăng ký thành công';
?>
<div class="success">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>Chúc mừng bạn đã đăng ký thành công!</p>
    <p><?= Html::a('Quay lại trang chủ', ['/site/index'], ['class' => 'btn btn-primary']) ?></p>
</div>
