<?php
namespace Admin\Controller;
use Admin\Model\EmailModel;
use Admin\Model\UserModel;
use Libs\Action\FileProcessor;
use Think\Controller;
use Think\Upload;

class MessageEmailController extends CommonController{
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
                    $arr["id"] = $data["id"];
                    $result = $email->saveFileData($arr);
                    if($result){
                        delDirAndFile(WORKING_PATH.$data["file_path"],false);//编辑成功则删除旧文件
                        $json = ["code"=>200,"msg"=>"ok","data"=>["id"=>$data["id"]]];
                    }else{
                        delDirAndFile(WORKING_PATH.$arr["file_path"],false);//失败则删除上传的文件
                        $json = ["code"=>500,"msg"=>"server error(save data error)"];
                    }
                }else{
                    $result_id = $email->addFileData($arr);
                    if($result_id){
                        $json = ["code"=>200,"msg"=>"ok","data"=>["id"=>$result_id]];
                    }else{
                        delDirAndFile(WORKING_PATH.$arr["file_path"],false);//失败则删除上传的文件
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
        //SELECT t1.*,t2.id as from_user_id,t2.username as from_username,t2.real_name as from_real_name FROM vd_email as t1, vd_user as t2 WHERE t1.from_id = t2.id AND t1.is_hide=0 AND t1.to_id = 38
        $datas = $email->field("t1.*,t2.id as from_user_id,t2.username as from_username,t2.real_name as from_real_name")->table("vd_email as t1, vd_user as t2")->where("t1.from_id = t2.id AND t1.is_hide=0 AND t1.to_id='$id'")->order("t1.add_time DESC")->limit("$start,$offset")->select();
        $count = $email->field("t1.*,t2.id as from_user_id,t2.username as from_username,t2.real_name as from_real_name")->table("vd_email as t1, vd_user as t2")->where("t1.from_id = t2.id AND t1.is_hide=0 AND t1.to_id='$id'")->count();
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

    //隐藏邮件（伪删除）
    public function hideEmail(){
        $post = I("post.");
        if($post["ids"]){
            $email = new EmailModel();
            $result = $email->hideData($post["ids"]);
            if($result){
                $json = ["code"=>200,"msg"=>"ok"];
            }else{
                $json = ["code"=>500,"msg"=>"server error"];
            }
        }else{
            $json = ["code"=>400,"msg"=>"client error(no id)"];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }

    //查看邮件
    public function readEmail(){
        $get = I("get.");
        if($get["id"]){
            $email = new EmailModel();
            $get["is_read"] = 1;
            $result = $email->save($get);
            if($result) {
                $json = ["code" => 200, "msg" => "ok"];
            }else{
                $json = ["code"=>500,"msg"=>"server error"];
            }
        }else{
            $json = ["code"=>400,"msg"=>"client error(no id)"];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }
    //下载附件
    public function downloadFile(){
        $get = I("get.");
        if($get["id"]){
            $email = new EmailModel();
            $data = $email->find($get["id"]);
            if($data){
                //文件下载
                $file = WORKING_PATH.$data['file_path']; //需要下载的文件路径
                FileProcessor::downloadFile($file);//执行文件下载渲染
            }else{
                echo "出错了";
            }
        }else{
            echo "出错了";
        }
    }


    /**
     *  发件箱
     */
    //展示发的邮件
    public function showOutbox(){
        $get = I("get.");
        $id = $get["id"];
        $page = $get["page"];//页码

        $offset = $get["limit"];//偏移量
        $start = ($page-1)*$offset;//起始位置

        $word = $get["key"];//模糊查询关键字
        $email = new EmailModel();
        if($word){
            //
            $datas = $email->field("t1.*,t2.id as to_user_id,t2.username as to_username")->table("vd_email as t1, vd_user as t2")->where("t1.to_id = t2.id AND t1.from_id ='$id' AND username LIKE '%$word%'")->order("t1.add_time DESC")->limit("$start,$offset")->select();
            $count = $email->field("t1.*,t2.id as to_user_id,t2.username as to_username")->table("vd_email as t1, vd_user as t2")->where("t1.to_id = t2.id AND t1.from_id ='$id' AND username LIKE '%$word%'")->count();
        }else{
            //SELECT t1.*,t2.id as to_user_id,t2.username as to_username FROM vd_email as t1, vd_user as t2 WHERE t1.to_id = t2.id AND t1.from_id = 16
            $datas = $email->field("t1.*,t2.id as to_user_id,t2.username as to_username")->table("vd_email as t1, vd_user as t2")->where("t1.to_id = t2.id AND t1.from_id ='$id'")->order("t1.add_time DESC")->limit("$start,$offset")->select();
            $count = $email->field("t1.*,t2.id as to_user_id,t2.username as to_username")->table("vd_email as t1, vd_user as t2")->where("t1.to_id = t2.id AND t1.from_id ='$id'")->count();
        }
        if($datas){
            for($i=0; $i<count($datas); $i++){
                $datas[$i]["add_time"] = date("Y-m-d H:i:s",$datas[$i]["add_time"]);
                $datas[$i]["content"] = html2text($datas[$i]["content"]);
            }
            $json = ["code"=>0,"mgs"=>"ok","count"=>$count,"data"=>$datas];
        }else{
            $json = ["code"=>500,"msg"=>"没有任何数据哟"];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }

    //删除邮件
    public function deleteEmail(){
        $post = I("post.");
        if($post["ids"]){ //删除多个
            $email = new EmailModel();
            $ids_str = implode(",",$post["ids"]);
            $result = $email->deleteData($ids_str);
            if($result){
                $json = ["code"=>200,"msg"=>"ok"];
            }else{
                $json = ["code"=>500,"msg"=>"server error"];
            }
        }else if ($post["id"]){ //删除一个
            $email = new EmailModel();
            $result = $email->deleteData($post["id"]);
            if($result){
                $json = ["code"=>200,"msg"=>"ok"];
            }else{
                $json = ["code"=>500,"msg"=>"server error"];
            }
        } else{
            $json = ["code"=>400,"msg"=>"client error(no ids)"];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }


    /**
     *  主页
     */
    public function emailNum(){
        $get = I("get.");
        if($get["id"]){
            $id = $get["id"];
            $email = new EmailModel();
            $num_arr = [];
            $unread_count = $email->where("to_id='$id' AND is_hide=0 AND is_read=0")->count();
            $send_count = $email->where("from_id='$id'")->count();
            $num_arr["unread_count"] = $unread_count?$unread_count:0;
            $num_arr["send_count"] = $send_count?$send_count:0;
            $json = ["code"=>200,"msg"=>"ok","data"=>$num_arr];
        }else{
            $json = ["code"=>400,"msg"=>"client error(no id)"];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }


    /**
     *  草稿箱
     */
    //展示草稿箱数据
    public function showDraftEmail(){
        $get = I("get.");
        if($get["from_id"]){
            $from_id = $get["from_id"];
            $email = new EmailModel();
            //SELECT * FROM vd_email WHERE to_id is NULL AND from_id = 16
            $datas = $email->where("to_id is NULL AND from_id='$from_id'")->select();
            if($datas){
                $json = ["code"=>200,"msg"=>"ok","data"=>$datas];
            }else{
                $json = ["code"=>200,"msg"=>"no data"];
            }
        }else{
            $json = ["code"=>400,"msg"=>"client error(no from_id)"];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }
    //编辑数据（草稿箱）
    public function editDraftEmail(){
        $post = I("post.");
        if($post["id"] && $post["from_id"] && $post["to_id"] && $post["subject"] && $post["content"]){
            $email = new EmailModel();
            $result = $email->saveData($post);
            if($result){
                $json = ["code"=>200,"msg"=>"ok"];
            }else{
                $json = ["code"=>500,"msg"=>"server error(save data error)"];
            }
        }else{
            $json = ["code"=>400,"msg"=>"client error(no data)"];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }
    //异步文件上传
    public function editUploadFile(){
        $get = I("get.");
        $file = $_FILES["file"];
        if($get["id"] && $file["error"]==0){
            $cfg = [
                'replace'=>true, //存在同名是否覆盖
                'rootPath'=> WORKING_PATH.UPLOAD_ROOT_PATH."email_file/", //保存根路径
            ];
            $upload = new Upload($cfg);
            $info = $upload->uploadOne($file);
            if($info) {
                $arr = [];//存数据
                $arr["id"] = $get["id"];
                $arr["file_name"] = $info['name'];
                $arr["file_path"] = UPLOAD_ROOT_PATH . "email_file/" . $info['savepath'] . $info['savename'];
                $email= new EmailModel();
                $data = $email->find($get["id"]);
                $arr["from_id"] = $data["from_id"];
                $result = $email->saveFileData($arr);
                if($result){
                    delDirAndFile(WORKING_PATH.$data["file_path"],false);//成功则删除旧文件
                    $json = ["code"=>200,"msg"=>"ok","data"=>["id"=>$data["id"]]];
                }else{
                    delDirAndFile(WORKING_PATH.$arr["file_path"]);//失败则删除上传的文件
                    $json = ["code"=>500,"msg"=>"server error(save data error)","error"=>$arr];
                }
            }else{
                $json = ["code"=>500,"msg"=>"upload error"];
            }
        }else{
            $json = ["code"=>400,"msg"=>"client error(no id)"];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }
    //删除草稿
    public function deleteDraftEmail(){
        $get = I("get.");
        if($get["id"]){
            $email = new EmailModel();
            $result = $email->deleteOneData($get["id"]);
            if($result){
                $json = ["code"=>200,"msg"=>"ok"];
            }else{
                $json = ["code"=>500,"msg"=>"server error"];
            }
        }else{
            $json = ["code"=>400,"msg"=>"client error(no id)"];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }
}