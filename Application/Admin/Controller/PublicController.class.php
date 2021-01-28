<?php
namespace Admin\Controller;
use Admin\Model\Login_infoModel;
use Admin\Model\UserModel;
use Exception;
use Org\Net\JWT;
use Org\PHPMailer\PHPMailer;
use Think\Controller;
use Think\Verify;

/**
 * Class PublicController
 * @package Admin\Controller
 * @describe 登录，注销，忘记密码等功能
 */
class PublicController extends Controller{
    /**
     *  显示验证码
     */
    public function captcha(){
        ob_end_clean();//加此函数，输出的验证码不乱码
        $cfg = array(
            'fontSize'  =>  14,              // 验证码字体大小(px)
            'useCurve'  =>  false,            // 是否画混淆曲线
            'useNoise'  =>  true,            // 是否添加杂点
            'imageH'    =>  35,               // 验证码图片高度
            'imageW'    =>  100,               // 验证码图片宽度
            'length'    =>  4,               // 验证码位数
        );
        $verify = new Verify($cfg);
        $verify->entry();
    }
    /**
     *  异步验证验证码
     */
    public function checkCode(){
        if(IS_POST){
            $post = I("post.");
            $code = $post["code"];
            $verify = new Verify();
            $checkResult = $verify->check($code);
            if($checkResult){
                session("sessionResult",md5(time())) ;//凭证
                $json = [
                    "code"=>200,
                    "msg"=>"ok",
                    "data"=>[
                        "checkResult"=> 1, //校验结果
                        "sessionResult"=>session("sessionResult") //凭证
                    ]
                ];
            }else{
                $json = [
                    "code"=> -1,
                    "msg"=>"checking..."
                ];
            }
            /** @var TYPE_NAME $json */
            $this->ajaxReturn($json);
        }
    }

    /**
     *  校验验证码和管理员登录
     */
    public function login(){
        if(IS_POST){
            $post = I("post.");
            //校验验证码
            $checkResult = $post["checkResult"];
            $sessionResult = $post["sessionResult"];
            if($checkResult == "1" && session("sessionResult") == $sessionResult){ //验证凭证
                //登录
                $username = $post['username'];
                $password = $post['password'];
                $user = new UserModel();
                $result = $user->verifyLogin($username,$password);
                if(!empty($result)){
                    session("sessionResult",null);//清除已验证的凭证
                    $checkLogin = new Login_infoModel();
                    $uid = $result["id"];
                    $dt = $checkLogin->where("uid = '$uid'")->find();
                    if($dt["login_status"] == 1){//登录状态
                        $json = [
                            "code"=>200,
                            "msg"=>"User logged in",
                            "login_status"=>1,
                            "data"=>[
                                "id"=>$result["id"]
                            ]
                        ];
                    }else{
                        if($result["status_bool"] == 0 ){ //用户状态--正常
                            $res = $checkLogin->addData($result);
                            $login_res = $checkLogin->find($result["id"]);//登录时间
                            $login_time = $login_res["login_time"];
                            if($res){



//                                $jwt = new Jwt();
//                                $jwt->setKey('123123123');//key
//                                $jwt->setIss('Auth0');//JWT的签发者
//                                $jwt->setIat(time());//签发时间
//                                $jwt->setExp(time() + 60 * 60 * 24);//过期时间为1天
//                                $jwt->setClaim(['id' => 100, 'nickname' => '志在卓越']);//存储数据
//                                //生成token并获取
//                                $token = $jwt->getToken();
//                                var_dump($token);
//
//                                //token验证
//                                $verifyResult = $jwt->verifyToken($token);
//                                if (!$verifyResult) {
//                                    var_dump("token 无效");
//                                } else {
//                                    var_dump($jwt->getClaim());//获取存储数据
//                                    var_dump($jwt->getExp());//获取token过期时间
//                                }







                                //持久存入用户信息到session
                                session("id",$result["id"]);
                                session("username",$result["username"]);
                                if($result["head_img_path"]){
                                    session("head_img_path",SERVER_URL.__ROOT__.$result["head_img_path"]);
                                }else{
                                    session("head_img_path",0);
                                }
                                session("role_id",$result["role_id"]);
                                $userInfo = [
                                    "UID"=>session("id"),
                                    "UNAME"=>session("username"),
                                    "UFACE"=>session("head_img_path"),
                                    "ROLE"=>session("role_id"),
                                ];
                                $json = [
                                    "code"=>200,
                                    "msg"=>"ok",
                                    "status_bool"=>0,
                                    "login_time"=>$login_time,
                                    "data"=>$userInfo
                                ];
                            }else{
                                $json = [
                                    "code"=>500,
                                    "msg"=>"server error"
                                ];
                            }

                        }
                        if($result["status_bool"] == 1 ){  //用户状态--被禁用
                            $json  = [
                                "code"=>200,
                                "msg"=>"ok",
                                "status_bool"=>1
                            ];
                        }
                    }


                }else{
                    $json = [
                        "code"=>500,
                        "msg"=>"server error"
                    ];
                }
            }else{
                $json = [
                    "code"=>-1,
                    "msg"=>"code error",
                    "data"=>[
                        $sessionResult,
                        session("sessionResult")
                    ]
                ];
            }
        }else{
            $json = [
                "code"=>400,
                "msg"=>"client error"
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }

    /**
     *  强制登录
     */
    public function forcedLogin(){
        $get = I("get.");
        if($get["id"]){
            $user = new UserModel();
            $data = $user->find($get["id"]);
            $login = new Login_infoModel();
            $result = $login->deleteData($data["id"]);//强制注销先前用户
            if($result){
                $new_res = $login->addData($data);//添加用户
                $login_res = $login->find($data["id"]);//登录时间
                $login_time = $login_res["login_time"];
                if($new_res){
                    //持久存入用户信息到session
                    session("id",$data["id"]);
                    session("username",$data["username"]);
                    if($data["head_img_path"]){
                        session("head_img_path",SERVER_URL.__ROOT__.$data["head_img_path"]);
                    }else{
                        session("head_img_path",0);
                    }
                    session("role_id",$data["role_id"]);
                    $userInfo = [
                        "UID"=>session("id"),
                        "UNAME"=>session("username"),
                        "UFACE"=>session("head_img_path"),
                        "ROLE"=>session("role_id"),
                    ];
                    $json = [
                        "code"=>200,
                        "msg"=>"ok",
                        "status_bool"=>$data["status_bool"],
                        "login_time"=>$login_time,
                        "data"=>$userInfo
                    ];
                }else{
                    $json = [
                        "code"=>500,
                        "msg"=>"server error"
                    ];
                }
            }else{
                $json = [
                    "code"=>500,
                    "msg"=>"server error"
                ];
            }
        }else{
            $json = [
                "code"=>400,
                "msg"=>"client error(not id)"
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }

    /**
     *  用户实时状态（是否被禁用）
     */
    public function userTatus(){
        /*
         *  0:  用户状态正常
         *  1:  用户被禁用
         *  -1: 用户被强迫下线
         */
        $get = I("get.");
        $model = M("user");
        $text = 0;//初始化
        //用户是否可用状态
        $data = $model->find($get["UID"]);
        if($data["status_bool"] == 1){
            $text = 1;
        }
        //用户是否被强登
        $mod = M("Login_info");
        $uid = $get["UID"];
        $login_time = $get["time"];
        $dt = $mod->where("uid='$uid' AND login_time='$login_time'")->find();
        if(!$dt){
            $text = -1;
        }

        /** @var TYPE_NAME $text */
        echo $text;
    }

    /**----------
     *  忘记密码
     */
    public function forgetPassword(){
        /*
         *  post需提交的数据:  1.用户名  2.邮箱
         *
         *  json结果：result  1:成功  0:用户没有设置过邮箱  -1:用户提交的邮箱与数据库邮箱不一致  -2:邮箱发送失败
         */
        $post = I("post.");
        if($post["username"]&&$post["email"]){
            $user = new UserModel();
            $username = $post["username"];
            $data = $user->where("username='$username'")->find();
            if($data){
                if($data["email"]){
                    if($post["email"] == $data["email"]){//提交的邮箱于数据库邮箱相同

                        $addressee_email = $data["email"];
                        $addressee_name = $data["username"];
                        $request_token = md5($data["id"].$data["username"].$data["password"]);

                        $id = $data["id"];
                        $model = M("temp_verify");//存发送信息到临时表
                        $data = $model->find($data["id"]);
                        if($data){
                            $model->delete($data["id"]); //如果存在旧数据，执行删除旧数据，再执行添加新的
                        }
                        $arr = [
                            "uid" => $id,
                            "request_token"=>$request_token,
                            "send_time"=>time()
                        ];
                        $res = $model->add($arr);
                        if(!$res){ //添加临时数据失败，终止程序
                            exit();
                        }
                        //发送邮箱  //'</h1><p>请点击链接以验证操作:'.SERVER_URL.__ROOT__.'/index.php/Admin/Public/emailVerify?request_token='.$request_token.'</p>'
                        //'www.5wpz.top【重置密码】'
                        $sendRes= sendEmail($addressee_email,$addressee_name,'www.5wpz.top【重置密码】','</h1><p>请点击链接以验证操作:'.SERVER_URL.__ROOT__.'/index.php/Admin/Public/emailVerify?request_token='.$request_token.'</p>');
                        if($sendRes){
                            //echo '邮件发送成功';
                            $json = [
                                "code"=>200,
                                "msg"=>"ok",
                                "result"=>1,
                                "data"=>[
                                    "id"=>$id
                                ]
                            ];
                        }else{
                            //echo '邮件发送失败: ', $mail->ErrorInfo;

                            $model->delete($id);//发送失败则删除临时表信息

                            $json = [
                                "code"=>200,
                                "msg"=>"send email error",
                                "result"=>-2,
                            ];
                        }
                    }else{
                        //返回：用户提交的邮箱与数据库邮箱不一致
                        $json = [
                            "code"=>200,
                            "msg"=>"email disparity",
                            "result"=>-1
                        ];
                    }
                }else{
                    //返回：用户没有设置过邮箱
                    $json = [
                        "code"=>200,
                        "msg"=>"no email",
                        "result"=>0,
                    ];
                }
            }else{
                //返回：用户名不对
                $json = [
                    "code"=>500,
                    "msg"=>"server error(not data)"
                ];
            }
        }else{
            //返回：客户端提交问题
            $json = [
                "code"=>400,
                "msg"=>"client error(not data)"
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);

    }

    /**
     *  异步验证邮箱
     */
    public function emailVerify(){
        $get = I("get.");
        if($get){
            $request_token = $get["request_token"];//token令牌

            $model = M("temp_verify");
            $data = $model->where("request_token='$request_token'")->find();//比对token
            if($data){
                $send_time = $data["send_time"];//发送时的时间
                if(time() - $send_time < 24 * 60 * 60) { //是否是小于24小时，否则过期
                    $arr = ["uid"=>$data["uid"],"status"=>1];//修改验证状态（成功）
                    $result = $model->save($arr);
                    if($result){
                        //echo "<h1 style='color: #5FB878; text-align: center'>验证成功<h1>";
                        $text_data= ["text"=>"验证成功","bgc"=>"#009688"];
                        $this->assign("text_data", $text_data );
                    }else{
                        $model->delete($data["uid"]);
                        //echo "<h1 style='color: #FF5722; text-align: center'>验证失败,请去登录页重试<h1>";
                        $text_data = ["text"=>"验证失败,请去登录页重试","bgc"=>"#FF5722"];
                        $this->assign("text_data",$text_data);
                    }
                }else{
                    $model->delete($data["uid"]);
                    //echo "<h1 style='color: #FF5722; text-align: center'>验证过期，请重试<h1>";
                    $text_data = ["text"=>"验证过期，请重试","bgc"=>"#FF5722"];
                    $this->assign("text_data",$text_data);
                }
            }else{
                //echo "<h1 style='color: #FF5722; text-align: center'>令牌错误或令牌已过期<h1>";
                $text_data = ["text"=>"令牌错误或令牌已过期","bgc"=>"#FF5722"];
                $this->assign("text_data", $text_data);
            }
            $this->display();
        }else{
            return;
        }
    }

    /**
     *  实时监测
     */
    public function monitor(){
        $post = I('post.');
        $model = M("temp_verify");
        $data = $model->find($post["id"]);
        if($data["status"] == 1){
            $json = [
                "code"=>200,
                "msg"=>"ok",
                "status"=>1
            ];
        }else{
            $json = [
                "code"=>200,
                "msg"=>"status is 0",
                "status"=>0
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }

    /**
     *  执行修改密码
     */
    public function resetPassword(){
        $post = I("post.");
        if($post["id"]){
            $id = $post["id"];
            $password = $post["password"];
            $user = new UserModel();
            $arr = [
                "id"=>$id,
                "password"=> md5($password)
            ];
            $result = $user->saveData($arr);
            if($result){
                $model = M("temp_verify");//重置完成,删除临时表
                $model->delete($id);
                $json = [
                    "code"=>200,
                    "msg"=>"ok"
                ];
            }else{
                $json = [
                    "code"=>500,
                    "msg"=>"server error"
                ];
            }
        }else{
            $json = [
                "code"=>400,
                "msg"=>"client error(not id)"
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }
    /**------------------------
    */


    /**
     *  注销登录
     */
    public function logout(){
        $get = I("get.");
        $login_info = new Login_infoModel();
        $result = $login_info->deleteData($get["id"]);
        if($result){
            session(null);//清除session信息
            $json = [
                "code"=>200,
                "msg"=>"ok"
            ];
        }else{
            $json = [
                "code"=>500,
                "msg"=>"logout error"
            ];
        }
        $this->ajaxReturn($json);
    }
}
