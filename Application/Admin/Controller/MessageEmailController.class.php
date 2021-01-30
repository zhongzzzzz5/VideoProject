<?php
namespace Admin\Controller;
use Admin\Model\EmailModel;
use Admin\Model\UserModel;
use Think\Controller;
use Think\Upload;

class MessageEmailController extends Controller{
    /**
     *  展示通讯录
     */
    public function addressBook(){
        $get =  I("get.");
        $id = $get["id"];

        $page = $get["page"];//页码

        $offset = $get["limit"];//偏移量
        $start = ($page-1)*$offset;//起始位置

        $word = $get["key"];//模糊查询关键字
        $user = new UserModel();
        if($word){
            $datas = $user->field("t1.id,t1.username,t1.real_name,t1.email")->table("vd_user as t1, vd_role as t2")->where("t1.role_id = t2.role_id AND username LIKE '%$word%' AND t1.id<>'$id'")->order("t1.id")->limit("$start,$offset")->select();
            $count = $user->field("t1.id,t1.username,t1.real_name,t1.email")->table("vd_user as t1, vd_role as t2")->where("t1.role_id = t2.role_id AND username LIKE '%$word%' AND t1.id<>'$id'")->count();
        }else{
            $datas = $user->field("t1.id,t1.username,t1.real_name,t1.email")->table("vd_user as t1, vd_role as t2")->where("t1.role_id = t2.role_id AND t1.id<>'$id'")->order("t1.id")->limit("$start,$offset")->select();
            $count = $user->field("t1.id,t1.username,t1.real_name,t1.email")->table("vd_user as t1, vd_role as t2")->where("t1.role_id = t2.role_id AND t1.id<>'$id'")->count();
        }
        if($datas){
            $json = ["code"=>0,"msg"=>"ok","count"=>$count,"data"=>$datas];
        }else{
            $json = ['code'=>500,"msg"=>"no data"];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }

    /**
     *  写邮件
     */
    public function writeEmail(){
        if(IS_POST){
            $post = I("post.");
            if($post["from_id"] && $post["to_id"]){
                if($post["id"]){
                    $email = new EmailModel();
                    $result = $email->saveData($post);
                    if($result){
                        $json = ["code"=>200,"msg"=>"save ok"];
                    }else{
                        $json = ["code"=>500,"msg"=>"server error(save data error)"];
                    }
                }else{
                    $email = new EmailModel();
                    $result_id = $email->addData($post);
                    if($result_id){
                        $json = ["code"=>200,"msg"=>"add ok"];
                    }else{
                        $json = ["code"=>500,"msg"=>"server error(add data error)"];
                    }
                }
            }else{
                $json = ["code"=>400,"msg"=>"client error(no id)"];
            }
        }else{
            $json = ["code"=>400,"msg"=>"client error(no post)"];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }

    /**
     *  异步文件上传
     */
    public function uploadFile(){
        $file = $_FILES["file"];
        $get = I("get.");
        if($file && $get["from_id"]){
            $cfg = [
                'replace'=>true, //存在同名是否覆盖
                'rootPath'=> WORKING_PATH.UPLOAD_ROOT_PATH."email_file/", //保存根路径
            ];
            $upload = new Upload($cfg);
            $info = $upload->uploadOne($file);
            if($info){
                $arr["from_id"] = $get["from_id"];
                $arr["file_name"] = $info['name'];
                $arr["file_path"] = UPLOAD_ROOT_PATH."email_file/".$info['savepath'].$info['savename'];
                $email = new EmailModel();
                $from_id = $get["from_id"];
                $data = $email->where("from_id='$from_id'")->find();
                if(!($data["to_id"]) && $data["file_path"] && !($data["add_time"])){
                    delDirAndFile(WORKING_PATH.$data["file_path"],false);
                    $arr["id"] = $data["id"];
                    $result = $email->saveFileData($arr);
                    if($result){
                        $json = ["code"=>200,"msg"=>"ok","data"=>["id"=>$data["id"]]];
                    }else{
                        $json = ["code"=>500,"msg"=>"server error(save data error)"];
                    }
                }else{
                    $result_id = $email->addFileData($arr);
                    if($result_id){
                        $json = ["code"=>200,"msg"=>"ok","data"=>["id"=>$result_id]];
                    }else{
                        $json = ["code"=>500,"msg"=>"server error(add data error)"];
                    }
                }
            }else{
                $json = ["code"=>500,"msg"=>"server error(upload error)"];
            }
        }else{
            $json = ["code"=>400,"msg"=>"client error(no file or no from_id)"];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }


    /**
     *  收件箱
     */
    //展示收件箱内容
    public function showInbox(){
        $get = I("get.");
        $id = $get["id"];
        $page = $get["page"];//页码

        $offset = $get["limit"];//偏移量
        $start = ($page-1)*$offset;//起始位置

        $email = new EmailModel();
        //SELECT t1.*,t2.id as from_user_id,t2.username as from_username,t2.real_name as from_real_name FROM vd_email as t1, vd_user as t2 WHERE t1.from_id = t2.id AND t1.to_id = 38
        $datas = $email->field("t1.*,t2.id as from_user_id,t2.username as from_username,t2.real_name as from_real_name")->table("vd_email as t1, vd_user as t2")->where("t1.from_id = t2.id AND t1.to_id='$id'")->order("t1.add_time DESC")->limit("$start,$offset")->select();
        $count = $email->field("t1.*,t2.id as from_user_id,t2.username as from_username,t2.real_name as from_real_name")->table("vd_email as t1, vd_user as t2")->where("t1.from_id = t2.id AND t1.to_id='$id'")->count();
        if($datas){
            for($i=0; $i<count($datas); $i++){
                $datas[$i]["add_time"] = date("Y-m-d H:i:s",$datas[$i]["add_time"]);
                $datas[$i]["content"] = html2text($datas[$i]["content"]);
            }
            $json = ["code"=>0,"msg"=>"ok","count"=>$count,"data"=>$datas];
        }else{
            $json = ["code"=>500,"msg"=>"您没有任何邮件哟"];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }

    //删除邮件
    public function deleteEmail(){
        $post = I("post.");
        if($post["ids"]){
            $email = new EmailModel();
            $ids_str = implode(",",$post["ids"]);
            $result = $email->deleteData($ids_str);
            if($result){
                $json = ["code"=>200,"msg"=>"ok"];
            }else{
                $json = ["code"=>500,"msg"=>"server error"];
            }
        }else{
            $json = ["code"=>400,"msg"=>"client error(no ids)"];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }
}