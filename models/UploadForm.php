<?php
namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use WebPConvert\WebPConvert;
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
        $folder_name = Yii::$app->user->identity->uuid;
        //$pathWeb = Yii::$app->basePath . '/web';
        // dd($pathWeb);
        $pathWeb = Yii::getAlias('@webroot');
        
        if (!is_dir($pathWeb.'/'.$folder_name.'/uploads/webp')) {
            mkdir($pathWeb.'/'.$folder_name.'/uploads/webp', 0777, true); 
        }
        
        $path = $folder_name . '/uploads/';
        if ($this->validate()) { 
            foreach ($this->imageFiles as $file) {
                $randomString = Yii::$app->security->generateRandomString(16);
                $sourcePath = $path . $randomString . '.' . $file->extension; 
                $file->saveAs($sourcePath);
                //convert to webP
                try {
                    //code...
                    $source = Yii::$app->basePath . '/web/' . $sourcePath;
                    $destination = $pathWeb . '/' . $folder_name . '/uploads/webp/' . $randomString . '.webp';
                    $options = [];
                    WebPConvert::convert($source, $destination, $options);
                } catch (\Throwable $th) {
                    throw $th;
                }

                $images_model = new Images();
                $images_model->path = $path . $randomString . '.' . $file->extension;
                $images_model->user_id = Yii::$app->user->identity->ID;
                $images_model->created_date = date('Y/m/d H:i:s', time());
                $images_model->save();
            }
            return true;
        } else {
            return false;
        }
        
    }
}