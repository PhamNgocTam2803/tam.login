<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;

class SignUpForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $re_password;

    public function rules()
    {
        return [
            [['username','email','password','re_password'],'required'],
            ['password','string','min'=> 8, 'tooShort'=> 'Mật khẩu phải từ 8 kí tự trở lên'],
            ['password','match','pattern'=>'/^[a-zA-Z0-9@%&*]+$/','message'=>'Mật chỉ được chứa những kí tự đặc biệt @,%,&,*'],
            ['email','email'],
            ['email','unique','message'=>'Email này đã được sử dụng.'],
            ['re_password','compare','compareAttribute'=>'password','message'=>'Mật khẩu nhập lại không đúng'],
        ];
    }

    public function saveSignUpForm()
    {
        $user_account = new User();
        $user_account->username = $this->username;
        $user_account->email = $this->email;
        $user_account->password = Yii::$app->getSecurity()->generatePasswordHash($this->password);
        // dd($this->username,$this->email);
        return $user_account->save();
        
    }
}

