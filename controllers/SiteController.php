<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Images;
use app\models\SignUpForm;
use app\models\Todo;
use app\models\UploadForm;
use app\models\User;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout','upload','todo'],
                'rules' => [
                    [
                        'actions' => ['logout','upload','todo'],
                        'allow' => true,
                        'roles' => ['@'], //for authenticated users
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['site/profile']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['site/profile']);
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }


    public function actionSignup()
    {
        $model = new SignUpForm();
        // dd(Yii::$app->request->post());
        if ($model->load(Yii::$app->request->post()))
        {
            // dd(Yii::$app->request->post());
            if ($model->saveSignUpForm()){

                return $this-> render('success');
                
            }else{
                Yii::$app->session->setFlash('error','Đã xảy ra lỗi, bạn hãy thử lại');
            }
            
        }
        return $this->render('signup',[
            'model' => $model,
        ]);
        
    }

    public function actionProfile()
    {
    // Kiểm tra nếu người dùng chưa đăng nhập, chuyển hướng về trang login
    if (Yii::$app->user->isGuest) {
        return $this->redirect(['site/login']);
    }

    // Lấy thông tin người dùng hiện tại
    $user = Yii::$app->user->identity;

    // Render view profile và truyền thông tin user
    return $this->render('profile', [
        'user' => $user,
    ]);
    }
    //Upload files
    public function actionUpload()
    {
        $headers = Yii::$app->request->headers;
        // dd($headers);
        $model = new UploadForm();
        if (Yii::$app->request->isPost) {
            if ($headers->get('origin')!==$_ENV['DOMAIN_SITE']){
                throw new BadRequestHttpException();
            }
            $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');
            if ($model->upload()) {
                Yii::$app->session->setFlash('success', 'Đã upload thành công!');
                return $this->redirect(['site/upload']);;
            }
        }
        return $this->render('upload', ['model' => $model]);
    }

    public function actionDeleteImage(){
        if (Yii::$app->request->isPost) {
            // Lấy dữ liệu JSON chuyển đổi thành PHP
            $data = json_decode(Yii::$app->request->getRawBody(), true);
            
            // Kiểm tra có id không
            if (isset($data['id'])) {
                $imageId = $data['id']; 
    
                // Tìm ảnh chứa id được gửi từ client trong bảng Images
                $image = Images::findOne($imageId);
    
                if ($image !== null) {
                    $filePath = Yii::getAlias('@webroot') . '/' . $image->path; // Đường dẫn đầy đủ đến ảnh
                    if (file_exists($filePath)) {
                        unlink($filePath); // Xóa file
                    }
    
                    // Xóa ảnh trong db
                    $image->delete();
    
                    // phản hồi thành công
                    return $this->asJson(['success' => true]);
                } else {
                    // trả ra lỗi nếu thất bại
                    return $this->asJson(['success' => false, 'error' => 'Image not found.']);
                }
            }
        }
        // nếu request ko hợp lệ thì trả ra lỗi
        return $this->asJson(['success' => false, 'error' => 'Invalid request.']);
    }

    //Todo-List
    public function actionTodo()
    {
        return $this->render('todo');
    }
    //Add task todo
    public function actionAddTask()
    {
        if (Yii::$app->request->isPost){
            $data = json_decode(Yii::$app->request->getRawBody(), true);
            
            //Kiểm tra có task không
            if(isset($data['text']) && isset($data['done']) && isset($data['delete'])){
                $taskData = $data['text'];
                $taskStatus = $data['done'];
                $taskDelete = $data['delete'];
                //Lưu vào trong todo table database
                $todoModel = new Todo();
                $todoModel->task_name = $taskData;
                $todoModel->status = $taskStatus;
                $todoModel->user_id = Yii::$app->user->identity->id;
                $todoModel->delete = $taskDelete;
                if ($todoModel->save()){
                    return $this->asJson(['success' => true,'id' => $todoModel->id]);
                }else{
                    return $this->asJson(['success' => false, 'error' => 'Lưu task và database thất bại!']);
                }
            }else{
                return $this->asJson(['success' => false, 'error' => 'Không nhận được dữ liệu']);
            }
        }else{
            return $this->asJson(['success' => false, 'error' => 'Request không hợp lệ']);
        }
    }
    //Get task from database
    public function actionGetDataTask()
    {
        $clientId = Yii::$app->user->identity->id;
        
        $task = (Todo::findAll(['user_id' => $clientId, 'delete' => 0]));
        
        $taskAsJson = [];

        if($task != null){
            foreach($task as $dataTask)
            {
            $taskAsJson[] = [
                'text' => $dataTask->task_name,
                'done' => $dataTask->status==='complete',
                'id' => $dataTask->id,
                'delete' => $dataTask->delete,
            ];
            }
            return json_encode($taskAsJson);
        }
        return json_encode($taskAsJson);
    }
    //Update task's status
    public function actionUpdateTaskStatus()
    {
        if(Yii::$app->request->isPatch){
            $data = json_decode(Yii::$app->request->getRawBody(),true);
            if(isset($data['task_id']) && isset($data['done'])){
                $task = Todo::find()->where(['id' => $data['task_id']])->one();
                if($task){
                    $task->status = $data['done'] ? 'not complete' : 'complete';
                    if($task->save()){
                        return $this->asJson(['success' => true]);
                    }else{
                        return $this->asJson(['success' => false, 'error' => 'Cập nhật trạng thái task thất bại!']);
                    }
                }else{
                    return $this->asJson(['success'=>false,'error'=>'Không tìm thất task phù hợp với id yêu cầu!']);
                }
            }else{
                return $this->asJson(['success'=>false, 'error'=>'Không có dữ liệu được gửi lên']);
            }
        }else{
            return $this->asJson(['success'=>false,'error'=>'Request không phù hợp']);
        }
    }
    //Soft delete tasks from todo list
    public function actionSoftDeleteTask(){
        if(Yii::$app->request->isPatch){
            $data = json_decode(Yii::$app->request->getRawBody(),true);
            if(isset($data['task_id']) && isset($data['delete'])){
                $task = Todo::findOne(['id' => $data['task_id']]);
                if($task){
                    $task->delete = 1;
                    if($task->save()){
                        return $this->asJson(['success' => true]);
                    }else{
                        return $this->asJson(['success' => false, 'error' => 'Lưu thất bại']);
                    }
                }else{
                    return $this->asJson(['success'=>false,'error'=>'Không có task phù hợp với id']);
                }
            }else{
                return $this->asJson(['success'=>false,'error'=>'Không có dữ liệu được gửi lên']);
            }
        }else{
            return $this->asJson(['success'=>false,'error'=>'request không đúng']);
        }
    } 
}
