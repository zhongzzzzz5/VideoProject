<?php
namespace Admin\Controller;
use Admin\Model\Cms_typeModel;
use Think\Controller;
use Think\Upload;

class VideoTypeController extends CommonController {
    /**
     *  展示所有影视分类
     */
    public function showVideoType(){
        $get = I("get.");
        $page = $get["page"];//页码

        $offset = $get["limit"];//偏移量
        $start = ($page-1)*$offset;//起始位置

        $cms_type = new Cms_typeModel();
        $datas = $cms_type->limit("$start,$offset")->select();
        $count = $cms_type->count();
        if($datas){
            for($i=0; $i<count($datas); $i++){
                $datas[$i]["type_icon"]?$datas[$i]["type_icon"] = SERVER_URL.__ROOT__.$datas[$i]["type_icon"]:$datas[$i]["type_icon"] = "";
            }
            $json = ["code"=>0,"msg"=>"ok","count"=>$count,"data"=>$datas];
        }else{
            $json = ["code"=>200,"msg"=>"没有数据"];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }

    /**
     *  删除分类
     */
    public function deleteVideoType(){
        $get = I("get.");
        if($get["type_id"]){
            $cms_type = new Cms_typeModel();
            $result = $cms_type->deleteData($get["type_id"]);
            if ($result){
                $json = ['code'=>200,"msg"=>"ok"];
            }else{
                $json = ["code"=>500,"msg"=>"server error"];
            }
        }else{
            $json = ["code"=>400,"msg"=>"client error(no id)"];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }


    /**
     *  编辑分类
     */
    //表格时间操作
    public function tableEventEdit(){
        $post = I("post.");
        if($post["type_id"]){
            $cms_type = new Cms_typeModel();
            if (isset($post["type_status"])){
                $result = $cms_type->save($post);
                if ($result){
                    $json = ["code"=>200,"msg"=>"ok"];
                }else{
                    $json = ["code"=>500,"msg"=>"server error"];
                }
            }

        }else{
            $json = ["code"=>400,"msg"=>"no data"];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }
    //编辑
    public function editVideoType(){
        $post = I("post.");
        if($post["type_id"]){
            $cms_type = new Cms_typeModel();
            $result = $cms_type->saveData($post);
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
    //异步上传图标(仅图片格式)
    public function uploadIcon(){
        $get = I("get.");
        $file = $_FILES["file"];
        if($get["type_id"]){
            if($file["error"] == 0){
                $cfg = [
                    'maxSize'=> 1048576, //上传的文件大小限制，单位byte (0-不做限制) 此次为最大值 1M
                    "exts"=> array('jpg','png', 'jpeg'), //允许上传的文件后缀
                    'replace'=>true, //存在同名是否覆盖
                    'rootPath'=> WORKING_PATH.UPLOAD_ROOT_PATH."video_type_icon/", //保存根路径
                ];
                $upload = new Upload($cfg);
                $info = $upload->uploadOne($file);
                if($info){
                    $arr["type_id"] = $get["type_id"];
                    $arr["type_icon"] = UPLOAD_ROOT_PATH."video_type_icon/".$info['savepath'].$info['savename'];
                    $cms_type = new Cms_typeModel();
                    $result = $cms_type->saveIconData($arr);
                    if($result){
                        $json = ["code"=>200,"msg"=>"ok"];
                    }else{
                        delDirAndFile(WORKING_PATH.$arr["type_icon"],false);
                        $json = ["'code"=>500,"msg"=>"server error(save error)"];
                    }
                }else{
                    $json = ["code"=>500,"msg"=>"server upload error"];
                }
            }else{
                $json = ["code"=>400,"msg"=>"client upload error"];
            }
        }else{
            $json = ["code"=>400,"msg"=>"client error(no id)"];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }


    /**
     *  添加分类
     */
    public function addVideoType(){
        $post = I("post.");
        $file = $_FILES["file"];

        if($file){
           if($file["error"] == 0){
               $cfg = [
                   'maxSize'=> 1048576, //上传的文件大小限制，单位byte (0-不做限制) 此次为最大值 1M
                   "exts"=> array('jpg','png', 'jpeg'), //允许上传的文件后缀
                   'replace'=>true, //存在同名是否覆盖
                   'rootPath'=> WORKING_PATH.UPLOAD_ROOT_PATH."video_type_icon/", //保存根路径
               ];
               $upload = new Upload($cfg);
               $info = $upload->uploadOne($file);
               if($info){
                   $arr["type_icon"] = UPLOAD_ROOT_PATH."video_type_icon/".$info['savepath'].$info['savename'];
                   $cms_type = new Cms_typeModel();
                   $result_type_id = $cms_type->uploadAddData($arr);
                   if($result_type_id){
                       $json = ["code"=>200,"msg"=>"ok","data"=>["type_id"=>$result_type_id]];
                   }else{
                       delDirAndFile(WORKING_PATH.$arr["type_icon"],false);
                       $json = ["code"=>500,"msg"=>"server error(add data)"];
                   }
               }else{
                   $json = ["code"=>500,"msg"=>"server (upload)"];
               }
           }else{
               $json = ["code"=>400,"msg"=>"client error(upload)"];
           }
        }


        if($post){
            if($post["type_id"]){
                $cms_type = new Cms_typeModel();
                $result = $cms_type->save($post);
                if($result){
                    $json = ["code"=>200,"msg"=>"ok"];
                }else{
                    $data = $cms_type->find($post["type_id"]);
                    if($data["type_icon"]){
                        delDirAndFile(WORKING_PATH.$data["type_icon"],false);
                    }
                    $json = ["code"=>500,"msg"=>"server error(add data)"];
                }
            }else{
                $json = ["code"=>400,"msg"=>"client error(no id)"];
            }
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);

    }


    /**
     *  向前提等级
     */
    public function up(){
        $get = I("get.");
        if($get["type_id"]){
            $cms_type = new Cms_typeModel();
            $result = $cms_type->up($get["type_id"]);
            if($result) {
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