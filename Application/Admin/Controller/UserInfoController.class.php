<?php
namespace Admin\Controller;
use Admin\Model\Login_infoModel;
use Admin\Model\RoleModel;
use Admin\Model\UserModel;
use Think\Controller;
use Think\Page;
use Think\Upload;

/**
 * Class UserInfoController
 * @package Admin\Controller
 * @describe 用户信息管理
 */
class UserInfoController extends CommonController{
    /**
     *  【所有用户】
     *  展示所有用户
     */
    public function userList(){
        if(IS_GET){
            $get =  I("get.");
            $page = $get["page"];//页码

            $offset = $get["limit"];//偏移量
            $start = ($page-1)*$offset;//起始位置

            $word = $get["key"];//模糊查询关键字
            if($word){/*模糊查询*/
                $model = M("user");
                //SELECT t1.*,t2.role FROM vd_user as t1, vd_role as t2 WHERE t1.role_id = t2.role_id  AND t1.role_id<>-1 AND username LIKE '%z%' ORDER BY t1.add_time DESC LIMIT 0,10
                $datas = $model->field("t1.*,t2.role")->table("vd_user as t1, vd_role as t2")->where("t1.role_id = t2.role_id AND t1.role_id<>-1 AND username LIKE '%$word%'")->order("t1.add_time DESC")->limit("$start,$offset")->select();
                $count = $model->field("t1.*,t2.role")->table("vd_user as t1, vd_role as t2")->where("t1.role_id = t2.role_id AND t1.role_id<>-1 AND username LIKE '%$word%'")->order("t1.add_time DESC")->count();
            }else{/*普通查询*/
                $model = M("User");
                //SELECT t1.*,t2.role FROM vd_user as t1, vd_role as t2 WHERE t1.role_id = t2.role_id  AND t1.role_id<>-1 ORDER BY t1.add_time DESC LIMIT 0,10
                $datas = $model->field("t1.*,t2.role")->table("vd_user as t1, vd_role as t2")->where("t1.role_id = t2.role_id AND t1.role_id<>-1")->order("t1.add_time DESC")->limit("$start,$offset")->select();
                $count = $model->field("t1.*,t2.role")->table("vd_user as t1, vd_role as t2")->where("t1.role_id = t2.role_id AND t1.role_id<>-1")->count();
            }
            if($datas) {
                $newdatas = [[]];//前端需要的字段格式
                for ($i = 0; $i < count($datas); $i++) {
                    $newdatas[$i]["usersId"] = $datas[$i]["id"];
                    $newdatas[$i]["userName"] = $datas[$i]["username"];
                    $newdatas[$i]["userEmail"] = $datas[$i]["email"];
                    $newdatas[$i]["userSex"] = $datas[$i]["sex"];
                    $newdatas[$i]["userStatus"] = $datas[$i]["status_bool"];
                    $newdatas[$i]["userGrade"] = $datas[$i]["role_id"];
                    $newdatas[$i]["userEndTime"] = date("Y-m-d H:i:s", $datas[$i]["add_time"]);
                    $newdatas[$i]["userDesc"] = $datas[$i]["user_profiles"];
                }
                $json = [
                    "code" => 0,
                    "msg" => "ok",
                    "count" => $count,
                    "data" => $newdatas
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
     *  【所有用户】
     * 异步验证用户名是否已被使用
     */
    public function checkUserName(){
        if(IS_POST){
            $post = I("post.");
            if(!empty($post)){
                $username = $post["username"];
                $model = M("User");
                //SELECT * FROM vd_user WHERE BINARY username = "WuPeizhong"
                $result = $model->where("BINARY username = '$username'")->find();
                if($result){
                    $json = [
                        "code"=>200,
                        "used" => 1,
                        "msg"=>"username is used"
                    ];
                }else{
                    $json = [
                        "code"=>200,
                        "used"=>0,
                        "msg"=>"username is not used "
                    ];
                }
            }else{
                $json = [
                    "code"=>400,
                    "msg"=>"client error(not data)"
                ];
            }
        }else{
            $json = [
                "code"=>400,
                "msg"=>"client error(not post)"
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }

    /**
     *  【所有用户】
     *  添加用户
     */
    public function addUser(){
        if(IS_POST){
            $post = I("post.");
            if(!empty($post)){
                $user = new UserModel();
                $result = $user->addData($post);
                if($result){
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
                    "msg"=>"client error"
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
     *  【所有用户】
     *  编辑用户
     */
    public function editUser(){
        if(IS_POST){
            $post = I("post.");
            if($post["id"]){
                $user = new UserModel();
                $result = $user->saveData($post);
                if($result){
                    $json = [
                        "code"=>200,
                        "msg"=>"ok"
                    ];
                }else{
                    $json = [
                        "code"=>500,
                        "msg"=>"server error(saveData)"
                    ];
                }
            }else{
                $json = [
                    "code"=>400,
                    "msg"=>"client error(no id)"
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
     *  【所有用户】
     *  模糊查询
     */
    public function selectUserLike(){
        if(IS_GET){
            $get = I("get.");
            if($get["key"]){
                $word = $get["key"];
                $model = M("user");
                //SELECT t1.*,t2.role FROM vd_user as t1, vd_role as t2 WHERE t1.role_id = t2.role_id AND t1.role_id <>-1 AND username LIKE '%z%'
                $datas = $model->field("t1.*,t2.role")->table("vd_user as t1, vd_role as t2")->where("t1.role_id = t2.role_id AND t1.role_id <>-1 AND username LIKE '%$word%'")->limit("")->select();
                if($datas){
                    $newdatas = [[]];//前端需要的字段格式
                    for($i=0; $i<count($datas); $i++){
                        $newdatas[$i]["usersId"] = $datas[$i]["id"];
                        $newdatas[$i]["userName"] = $datas[$i]["username"];
                        $newdatas[$i]["userEmail"] = $datas[$i]["email"];
                        $newdatas[$i]["userSex"] =  $datas[$i]["sex"];
                        $newdatas[$i]["userStatus"] = $datas[$i]["status_bool"];
                        $newdatas[$i]["userGrade"] = $datas[$i]["role_id"];
                        $newdatas[$i]["userEndTime"] = date("Y-m-d H:i:s",$datas[$i]["add_time"]);
                        $newdatas[$i]["userDesc"] = $datas[$i]["user_profiles"];
                    }
                    $count = count($newdatas);//条数
                    $json = [
                        "code"=>0,
                        "msg"=>"ok",
                        "count"=>$count,
                        "data"=>$newdatas
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
                    "msg"=>"client error(no word)"
                ];
            }
        }else{
            $json = [
                "code"=>400,
                "msg"=>"client error(no get)"
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }


    /**
     *  展示在线所有用户
     */
    public function userOnline(){
        $get =  I("get.");
        $page = $get["page"];//页码

        $offset = $get["limit"];//偏移量
        $start = ($page-1)*$offset;//起始位置

        $login = new Login_infoModel();
        $datas = $login->order("login_time DESC")->limit("$start,$offset")->select();
        $count = $login->count();
        if($datas){
            for($i=0; $i<count($datas); $i++){
                $datas[$i]["No"] = $i+1;
                $datas[$i]["login_time"] = date("Y-m-d H:i:s",$datas[$i]["login_time"]);
            }
            $json = [
                "code"=>0,
                "msg"=>"ok",
                "count"=>$count,
                "data"=>$datas
            ];
        }else{
            $json = [
                "code"=>200,
                "msg"=>"server not data"
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }

    /**
     *  展示等级(角色) layui的下拉select
     *  不包含超级管理员
     */
    public function showRole(){
        $role = new RoleModel();
        $datas = $role->where("role_id <> -1")->select();
        if($datas){
            $json = [
                "code"=>200,
                "msg"=>"ok",
                "data"=>$datas
            ];
        }else{
            $json = [
                "code"=>500,
                "msg"=>"server error(not data)"
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }

    /***********************/
    /**
     *  【当前用户】
     *  展示当前用户信息
     */
    public function showUser(){
        if(IS_GET){
            $get = I("get.");
            $model = M("User");
            //SELECT t1.*,t2.role FROM vd_user as t1, vd_role as t2 WHERE t1.id = 16 AND t1.role_id = t2.role_id
            $data = $model->field("t1.*,t2.role")->table("vd_user as t1, vd_role as t2")->where("t1.role_id = t2.role_id")->find($get["id"]);
            if($data){
                $data["add_time"] = date("Y-m-d H:i:s",$data["add_time"]);
                unset($data["password"]);//删除变量$data["password"],不返回密码到前端

                //组装前端需要的特殊格式
                $server_head_url = $data["head_img_path"]?SERVER_URL.__ROOT__.$data["head_img_path"]:$data["head_img_path"];
                $skill_arr = explode(",",$data["skill"]);
                $home_arr= explode(",",$data["home_address"]);

                $json = [
                    "code"=>200,
                    "msg"=>"ok",
                    "data"=>$data,
                    "others"=>[
                        "server_head_url" => $server_head_url,
                        "skill_arr"=> $skill_arr,
                        "home_arr"=>$home_arr
                    ]
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
                "msg"=>"client error"
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }

    /**
     *  【当前用户】
     *  编辑当前用户信息
     */
    public function editMy(){
        if(IS_POST){
            $post = I("post.");
            if(!empty($post["id"])){
                $user = new UserModel();
                $result = $user->upData($post);
                if($result){
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
                    "msg"=>"client error"
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
     *  上传头像并同步到数据库
     */
    public function uploadHeadImg(){
        $file = $_FILES["file"];
        $get = I("get.");
        if($file["error"] == 0){
            $cfg = [
                'maxSize'=> 1048576, //上传的文件大小限制，单位byte (0-不做限制) 此次为最大值 1M
                "exts"=> array('jpg', 'gif', 'png', 'jpeg'), //允许上传的文件后缀
                'replace'=>true, //存在同名是否覆盖
                'rootPath'=> WORKING_PATH.UPLOAD_ROOT_PATH."user_head_img/", //保存根路径
            ];
            $upload = new Upload($cfg);
            $info = $upload->uploadOne($file);
            if($info){
                $arr["id"] = $get["id"];
                $arr["head_img"] = $info['name'];
                $arr["head_img_path"] = UPLOAD_ROOT_PATH."user_head_img/".$info['savepath'].$info['savename'];
                $model = M("User");
                $data = $model->find($get["id"]);
                if($data["head_img_path"]){
                    delDirAndFile(WORKING_PATH.$data["head_img_path"],false);
                }
                $result = $model->save($arr);
                if($result){
                    $json = [
                        "code"=> 200
                        ,"msg"=> "ok"
                        ,"data"=> [
                            'server_img_url'=> SERVER_URL.__ROOT__.UPLOAD_ROOT_PATH."user_head_img/".$info['savepath'].$info['savename'],
                            'file_url'=>UPLOAD_ROOT_PATH."user_head_img/".$info['savepath'].$info['savename'],
                            'file_name'=>$info['name'],
                        ]
                    ];
                }else{
                    //添加文件信息到数据库失败，执行删除刚上传的文件
                    delDirAndFile( WORKING_PATH.UPLOAD_ROOT_PATH."user_head_img/".$info['savepath'].$info['savename'],false);
                    $json = [
                        "code"=>500,
                        "msg"=>"server error(saveData)"
                    ];
                }
            }else{
                $json = [
                    "code"=>500,
                    "msg"=>"server error(upload)"
                ];
            }
        }else{
            $json = [
                "code"=>400,
                "msg"=>"client error(upload)"
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }

    /**
     *  【当前用户】
     *  重置当前用户密码
     */
    public function changePwd(){
        if(IS_POST){
            $post = I("post.");
            if(!empty($post["id"])){
                $old_password = $post["old_password"];
                $new_post = ["id"=>$post["id"],"password"=>$post["new_password"]];
                $user = new UserModel();
                $result = $user->upDataPwd($old_password,$new_post);
                if($result){
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
                    "msg"=>"client error"
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
     *  【当前用户】
     *  删除当前用户
     */
    public function deleteUser(){
        if(IS_GET){
            $get = I("get.");
            if($get["id"]){
                $user = new UserModel();
                $result = $user->deleteData($get["id"]);
                if($result){
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
                    "msg"=>"client error(no id)"
                ];
            }
            /** @var TYPE_NAME $json */
            $this->ajaxReturn($json);
        }
    }

    /**
     *  【删除多个用户】
     */
    public function deleteUserArr(){
        if(IS_POST){
            $post = I("post.");
            if($post["idArr"]){
                $user = new UserModel();
                $id_arr_str = implode(",",$post["idArr"]);//重点：将数组转为字符串，且用,隔开
                $result = $user->deleteDataArr($id_arr_str);
                if($result){
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
                    "msg"=>"client error(no id arr)"
                ];
            }
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }
}