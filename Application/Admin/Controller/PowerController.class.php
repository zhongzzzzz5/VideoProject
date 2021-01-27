<?php
namespace Admin\Controller;
use Admin\Model\RoleModel;
use Admin\Model\UserModel;
use Think\Controller;

/**
 * Class PowerController
 * @package Admin\Controller
 * @describe 等级、权限类
 */
class PowerController extends Controller{
    /**
     *  用户等级(角色)
     */
    public function userRole(){
        $role = new RoleModel();
        $datas = $role->select();
        if($datas){
            for($i=0; $i<count($datas); $i++){
                $datas[$i]["No"] = $i+1;
            }
            $json = [
                "code"=>0,
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

    /**
     *  添加等级(角色)
     */
    public function addRole(){
        $post = I("post.");
        if($post){
            $role = new RoleModel();
            $result = $role->addData($post["role"]);
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
                "msg"=>"client error(not data)"
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }

    /**
     *  验证
     */
    public function isHasPower(){
        $post = I("post.");
        if($post){
            $username = $post["username"];
            $password = md5($post["password"]);
            $user = new UserModel();
            $data = $user->where("username='$username' AND password='$password'")->find();
            if($data){
                if($data["role_id"] == -1){
                    $json = [
                        "code"=>200,
                        "msg"=>"ok",
                        "result"=>1//验证结果 （1：成功  0：失败）
                    ];
                }else{
                    $json = [
                        "code"=>200,
                        "msg"=>"you not power",
                        "result"=>0
                    ];
                }
            }else{
                $json = [
                    "code"=>500,
                    "msg"=>"server error(password wrong)"
                ];
            }
        }else{
            $json = [
                "code"=>400,
                "msg"=>"client error(not data)"
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }


    /**
     *  删除等级(角色)
     */
    public function deleteRole(){
        $get = I("get.");
        if($get["role_id"]){
            $role = new RoleModel();
            $result = $role->deleteData($get["role_id"]);
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
                "msg"=>"client error(not id)"
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }
}