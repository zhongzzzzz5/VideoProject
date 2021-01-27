<?php
namespace Admin\Controller;
use Admin\Model\AppmanagementModel;
use Admin\Model\AppModel;
use Think\Controller;
use Think\Upload;

/**
 * Class AppManagementController
 * @package Admin\Controller
 * @describe 软件管理
 */
class AppManagementController extends Controller{
    /**
     *  展示所有软件信息
     */
    public function showAllApp(){
       if(IS_GET){
           $get =  I("get.");
           $page = $get["page"];//页码

           $offset = $get["limit"];//偏移量
           $start = ($page-1)*$offset;//起始位置

           $word = $get["key"];//模糊查询关键字
           if($word){
               $model = M("App");
               //SELECT * FROM vd_app WHERE app_name LIKE '%乐%' ORDER BY add_time DESC LIMIT 0,1
               $datas = $model->where("app_name LIKE '%$word%'")->order("add_time DESC")->limit("$start,$offset")->select();
               $count = $model->where("app_name LIKE '%$word%'")->order("add_time DESC")->count();
           }else{
                //SELECT * FROM vd_app ORDER BY add_time DESC LIMIT 0,1
               $model = M("App");
               $datas = $model->order("add_time DESC")->limit("$start,$offset")->select();
               $count = $model->count();
           }
           if($datas){
               for($i=0; $i<count($datas); $i++){
                   $datas[$i]["server_app_logo_path"] = $datas[$i]["app_logo_path"]?SERVER_URL.__ROOT__.$datas[$i]["app_logo_path"]:"";
                   $datas[$i]["server_app_file_path"] = $datas[$i]["app_file_path"]?SERVER_URL.__ROOT__.$datas[$i]["app_file_path"]:"";
                   $datas[$i]["app_describe"] = html2text($datas[$i]["app_describe"]);
                   $datas[$i]["app_carousel_arr_name"] = $datas[$i]["app_carousel_arr_name"]?explode(",",$datas[$i]["app_carousel_arr_name"]):$datas[$i]["app_carousel_arr_name"];
                   $datas[$i]["app_carousel_arr_path"] = $datas[$i]["app_carousel_arr_path"]?explode(",",$datas[$i]["app_carousel_arr_path"]):$datas[$i]["app_carousel_arr_path"];
                   if($datas[$i]["app_carousel_arr_path"]){
                       $datas[$i]["server_app_carousel_arr_path"] = [];
                       for($j=0 ;$j<count($datas[$i]["app_carousel_arr_path"]); $j++){
                           array_push($datas[$i]["server_app_carousel_arr_path"],SERVER_URL.__ROOT__.$datas[$i]["app_carousel_arr_path"][$j]);
                       }
                   }else{
                       $datas[$i]["server_app_carousel_arr_path"] = "";
                   }

                   $datas[$i]["add_time"] = date("Y-m-d H:i:s",$datas[$i]["add_time"]);
               }
           }
           $json = [
               "code" => 0,
               "msg" => "ok",
               "count" => $count,
               "data" => $datas
           ];
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
     *  添加软件信息
     */
    public function addApp(){
        if(IS_POST){
            $post = I("post.");
            if(!empty($post)){
                $app = new AppModel();
                $result = $app->addData($post);
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
                    "msg"=>"client error(no data)"
                ];
            }
        }else{
            $json = [
                "code"=>400,
                "msg"=>"client error(no post)"
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }


    /**
     *  异步上传logo,并添加到数据库
     */
    public function uploadLogo(){
        $file = $_FILES["file"];
        $post= I("post.");
        if($post["id"]){
            if($file["error"] == 0){
                $cfg = [
                    'maxSize'=> 1048576, //上传的文件大小限制，单位byte (0-不做限制) 此次为最大值 1M
                    "exts"=> array('jpg', 'gif', 'png', 'jpeg'), //允许上传的文件后缀
                    'replace'=>true, //存在同名是否覆盖
                    'rootPath'=> WORKING_PATH.UPLOAD_ROOT_PATH."app/logo/", //保存根路径
                ];
                $upload = new Upload($cfg);
                $info = $upload->uploadOne($file);
                if($info){
                    $arr = [
                        "id"=>$post["id"],
                        "app_logo_name"=>$info['name'],
                        "app_logo_path"=> UPLOAD_ROOT_PATH."app/logo/".$info['savepath'].$info['savename']
                    ];
                    $app = new AppModel();
                    $data = $app->find($post["id"]);
                    //若存在旧文件
                    if($data["app_logo_path"]){
                        $old_app_logo_path = $data["app_logo_path"];
                    }
                    $result = $app->saveData($arr);
                    if($result){
                        if(!empty($old_app_logo_path)){ //添加新数据完成，执行删除旧文件
                            delDirAndFile(WORKING_PATH.$old_app_logo_path,false);
                        }
                        $json = [
                            "code"=>200,
                            "msg"=>"ok",
                            "data"=>[
                                "id"=>$post["id"],
                                "app_logo_name"=>$arr["app_logo_name"],
                                "app_logo_path"=>$arr["app_logo_path"],
                            ],
                            "others"=>[
                                "app_logo_path" => SERVER_URL.__ROOT__.$arr["app_logo_path"],
                            ]
                        ];
                    }else{
                        delDirAndFile(WORKING_PATH.$arr["app_logo_path"],false);//数据添加到数据库失败，执行删除上传的文件
                        $json = [
                            "code"=>500,
                            "msg"=>"server error(add data)"
                        ];
                    }
                }else{
                    $json = [
                        "code"=>500,
                        "msg"=>"server upload error"
                    ];
                }
            }else{
                $json = [
                    "code"=>400,
                    "msg"=>"client upload error"
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
     *  异步上传文件,并添加到数据
     */
    public function uploadFile(){
        $file = $_FILES["file"];
        $post = I("post.");
        if($post["id"]){
            if($file["error"] == 0){
                $cfg = [
                    "exts"=> array('apk', 'zip', 'rar', '7z'), //允许上传的文件后缀
                    'replace'=>true, //存在同名是否覆盖
                    'rootPath'=> WORKING_PATH.UPLOAD_ROOT_PATH."app/download/", //保存根路径
                ];
                $upload = new Upload($cfg);
                $info = $upload->uploadOne($file);
                if($info){
                    $arr = [
                        "id" => $post["id"],
                        "app_file_name" => $info['name'],
                        "app_file_path" => UPLOAD_ROOT_PATH."app/download/".$info['savepath'].$info['savename']
                    ];
                    $app = new AppModel();
                    //若存在旧文件
                    $data = $app->find($post["id"]);
                    if($data["app_file_path"]){
                        $old_app_file_path = $data["app_file_path"];
                    }
                    //存新数据
                    $result = $app->saveData($arr);
                    if($result){
                        if(!empty($old_app_file_path)){ //新数据添加成功，执行删除旧文件
                            delDirAndFile(WORKING_PATH.$old_app_file_path,false);
                        }
                        $json = [
                            "code"=>200,
                            "msg"=>"ok",
                            "data"=>[
                                "id"=>$post["id"],
                                "app_file_name" => $arr["app_file_name"],
                                "app_file_path" => $arr["app_file_path"],
                            ],
                            "others"=>[
                                "server_file_path" => SERVER_URL.__ROOT__.$arr["app_file_path"],
                            ]
                        ];
                    }else{
                        delDirAndFile(WORKING_PATH.$arr["app_file_path"],false);//数据添加到数据库失败，执行删除上传的文件
                        $json = [
                            "code"=>500,
                            "msg"=>"server error(add data)",
                        ];
                    }
                }else{
                    $json = [
                        "code"=>500,
                        "msg"=>"server upload error",
                    ];
                }
            }else{
                $json = [
                    "code"=>400,
                    "msg"=>"client upload error"
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
     *  编辑信息
     */
    public function editApp(){
        if(IS_POST){
            $post = I("post.");
            if($post["id"]){
                $app = new AppModel();
                $result = $app->saveData($post);
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
     *  删除改软件信息
     */
    public function deleteApp(){
        $get = I("get.");
        if($get["id"]){
            $app = new AppModel();
            $result = $app->deleteData($get["id"]);
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

    /**
     *  批量删除软件
     */
    public function deleteAppArr(){
        if(IS_POST){
            $post = I("post.");
            if($post["id_arr"]){
                $app = new AppModel();
                $id_arr_str = implode(",",$post["id_arr"]);//重点：将数组转为字符串，且用,隔开
                $result = $app->deleteDataArr($id_arr_str);
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
                    "msg"=>"client error(not id_arr)"
                ];
            }
        }else{
            $json = [
                "code"=>400,
                "msg"=>"client error(no post)"
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }

    /**
     *  异步上传轮播图
     */
    public function downloadCarousel(){
        $file = $_FILES["file"];
        if($file["error"] == 0){
            $cfg = [
                'maxSize'=> 1048576, //上传的文件大小限制，单位byte (0-不做限制) 此次为最大值 1M
                "exts"=> array('jpg', 'gif', 'png', 'jpeg'), //允许上传的文件后缀
                'replace'=>true, //存在同名是否覆盖
                'rootPath'=> WORKING_PATH.UPLOAD_ROOT_PATH."app/carousel/", //保存根路径
            ];
            $upload = new Upload($cfg);
            $info = $upload->uploadOne($file);
            if($info){
                $json = [
                    "code"=>200,
                    "msg"=>"ok",
                    "data"=>[
                        "carousel_name" => $info['name'],
                        "carousel_path" => UPLOAD_ROOT_PATH."app/carousel/".$info['savepath'].$info['savename']
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
                "msg"=>"client error(upload error)"
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }

    /**
     *  编辑（封面）
     */
    public function coverAppEdit(){
        if(IS_POST){
            $post = I("post.");
            if(!empty($post["id"])){
                $app = new AppModel();
                $data = $app->find($post["id"]);//
                //如果提交过了文件
                if($post["app_carousel_arr_name"] && $post["app_carousel_arr_path"]){
                    $post["app_carousel_arr_name"] = implode(",",$post["app_carousel_arr_name"]);
                     $post["app_carousel_arr_path"] = implode(",",$post["app_carousel_arr_path"]);
                }
                $result = $app->saveData($post);
                if($result){
                    if($data["app_carousel_arr_path"]){
                        $arr = explode(",",$data["app_carousel_arr_path"]);
                        for($i=0; $i<count($arr); $i++){
                            delDirAndFile(WORKING_PATH.$arr[$i],false);
                        }
                    }
                    $json = [
                        "code"=>200,
                        "msg"=>"ok",
                    ];
                }
            }else{
                $json = [
                    "code"=>400,
                    "msg"=>"client error(not id)"
                ];
            }
        }else{
            $json = [
                "code"=>400,
                "msg"=>"client error(no post)"
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }
}