<?php
use yii\widgets\ActiveForm;
$array = Yii::$app->user->identity->images; 
?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>


    <?= $form->field($model, 'imageFiles[]')->fileInput(['multiple' => true, 'accept' => 'image/*', 'id' => 'input_image']) ?>
    <!-- Upload button -->
    <button style="background-color: cyan; width: 120px; height: 40px;border-radius: 10px; font-weight: 600 ">Upload Image</button>
    <!-- Preview container -->
    <div id="preview_container" style="display:flex; gap: 20px;flex-wrap: wrap; flex-direction: row;">
    </div>
    
<?php ActiveForm::end() ?>
<!-- Section include: Image and Remove button -->
<?php foreach($array as $image){ ?>
    <div class="img-block" style="width: 35vw;display:flex;align-items: center;margin:20px auto;border:1px solid;border-radius: 10px;justify-content: space-evenly">
        <img src="/<?= $image->path?>" width="400px" >
        <button style="background-color: pink; width: 120px;height:40px;border-radius: 10px; font-weight: 600;margin:0 20px" type="button" class="remove-button" data-id="<?= $image->id?>">
            Remove
        </button>
    </div>
<?php } ?>
<!-- Config Remove button -->
<script>
    const removeButton = document.querySelectorAll('.remove-button');
    removeButton.forEach(button => {
        button.addEventListener('click', event => {
            const imgId = event.target.dataset.id;
            fetch('/site/delete-image', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
                },
                body: JSON.stringify({id: imgId})
            })
            .then(response => response.json()) // data phản hồi của server được chuyển thành js
            .then(data => {
                if(data.success){
                    button.closest('.img-block').remove();//phản hồi trả về là true thì xoá ảnh 
                    alert('Đã xoá ảnh có ID: ' + imgId);
                }else{
                    alert('Bị lỗi:' + data.error); 
                }
            })
            .catch(error => {
                console.log(error);
            })
        })
    }) 
</script>
<!-- Preview images container before upload images  -->
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

