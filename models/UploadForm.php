<?php
namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile[]
     */
    public $imageFiles;

    public function rules()
    {
        return [
            [['imageFiles'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, webp', 'maxFiles' => 2, 'maxSize' => 2000000, 'tooBig' => 'File không được lớn hơn 2MB.'],
        ];
    }
    
    public function upload()
    {
        $folder_name = Yii::$app->user->identity->username;

        //dd($folder_name);
        if (!is_dir($folder_name)) {
            mkdir($folder_name . '/' . 'uploads', 0777, true); 
        }
        if ($this->validate()) { 
            foreach ($this->imageFiles as $file) {
                $file->saveAs($folder_name . '/' . 'uploads' . '/' . $file->baseName . '.' . $file->extension);
            }
            return true;
        } else {
            return false;
        }
    }
}