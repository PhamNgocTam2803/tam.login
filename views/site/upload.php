<?php
use yii\widgets\ActiveForm;
$array = Yii::$app->user->identity->images;
?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>


    <?= $form->field($model, 'imageFiles[]')->fileInput(['multiple' => true, 'accept' => 'image/*', 'id' => 'input_image']) ?>

    <button>Upload Image</button>

    <div id="preview_container" style="display:flex; gap: 10px;">


    </div>
    
<?php ActiveForm::end() ?>

<table>
    <?php foreach($array as $image){ ?>
        <div>
            <img src="/<?= $image->path?>">

            <div x-data="{
                removeImage: (id) => {
                    <?php $array ?>.splice(id, 1);
                }
            }" >
                <button type="button" @click="removeImage('<?= $image->id ?>')" >
                    Remove
                </button>
            </div>

        </div>
    <?php } ?>
</table>

<script>
    document.getElementById('input_image').addEventListener('change', function(event) {
        const files = event.target.files;
        const previewContainer = document.getElementById('preview_container');

        // Xóa các ảnh cũ khi chọn file mới
        previewContainer.innerHTML = '';

        Array.from(files).forEach(file => {
            if (!file.type.startsWith('image/')) return; // Bỏ qua file không phải ảnh

            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result; // Gán đường dẫn ảnh vào thẻ img
                img.style.width = '100px';
                img.style.height = '100px';
                img.style.objectFit = 'cover';
                img.style.border = '1px solid #ccc';
                img.style.borderRadius = '5px';
                previewContainer.appendChild(img);
            };
            reader.readAsDataURL(file); // Đọc file để tạo URL
        });
    });

</script>