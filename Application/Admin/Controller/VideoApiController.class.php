<?php
namespace Admin\Controller;
use Admin\Model\ApiModel;
use Admin\Model\Default_apiModel;
use Think\Controller;

/**
 * Class VideoApiController
 * @package Admin\Controller
 * @describe 影视接口管理
 */
class VideoApiController extends CommonController{
    /**
     *  接口总开关
     */
    public function apiSwitch(){
        $model = M("default_api");
        $data = $model->find();
        if($data){
            $json = [
                "code"=>200,
                "msg"=>"ok",
                "data"=>$data
            ];
        }else{
            $json = [
                "code"=>200,
                "msg"=>"no data"
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }

    /**
     *  更改接口总开关状态
     */
    public function switchChange(){
        $post = I("post.");
        if($post["id"]){
            $default_api = new Default_apiModel();
            $result = $default_api->changeSwitch($post);
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
     *  展示所有接口
     */
    public function showApi(){
        $get =  I("get.");
        $page = $get["page"];//页码

        $offset = $get["limit"];//偏移量
        $start = ($page-1)*$offset;//起始位置

        $word = $get["key"];//模糊查询关键字
        if($word){ /*模糊查询*/
            $model = M("Api");
            //SELECT t1.*,t2.* FROM vd_api as t1, vd_api_category as t2 WHERE t1.category_id = t2.category_id AND api_name LIKE '%蒋%' ORDER BY id LIMIT 0,10
            $datas = $model->field("t1.*,t2.*")->table("vd_api as t1, vd_api_category as t2")->where("t1.category_id = t2.category_id AND api_name LIKE '%$word%'")->order("id")->limit("$start,$offset")->select();
            $count = $model->where("api_name LIKE '%$word%'")->count();
        }else{/*普通查询*/
            $model = M("Api");
            //SELECT t1.*,t2.* FROM vd_api as t1, vd_api_category as t2 WHERE t1.category_id = t2.category_id ORDER BY id LIMIT 0,10
            $datas = $model->field("t1.*,t2.*")->table("vd_api as t1, vd_api_category as t2")->where("t1.category_id = t2.category_id")->order("id")->limit("$start,$offset")->select();
            $count = $model->count();
        }
        if($datas){
            //组装前端需要的格式
            for($i=0; $i<count($datas); $i++){
                $datas[$i]["api_describe"] = html2text($datas[$i]["api_describe"]);
                $datas[$i]["add_time"] = date("Y-m-d H:i:s",$datas[$i]["add_time"]);
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
                "msg"=>"server no data"
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }

    /**
     *  展示接口分类
     */
    public function api_category(){
        $model = M("api_category");
        $data = $model->select();
        if($data){
            $json = [
                "code"=>200,
                "msg"=>"ok",
                "data"=>$data
            ];
        }else{
            $json = [
                "code"=>"500",
                "msg"=>"server error"
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }
    /**
     *  添加接口
     */
    public function addApi(){
        if(IS_POST){
            $post = I("post.");
            if($post["api_name"] && $post["api_url"]){
                $api = new ApiModel();
                $result = $api->addData($post);
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
                "msg"=>"client error(not post)"
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }


    /**
     *  删除单个接口
     */
    public function deleteApi(){
        $get = I("get.");
        if($get["id"]){
            $api = new ApiModel();
            $result = $api->deleteData($get['id']);
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
     *  删除多个接口
     */
    public function deleteApiArr(){
        if(IS_POST){
            $post = I("post.");
            if($post["id_arr"]){
                $id_arr_str = implode(",",$post["id_arr"]);
                $api = new ApiModel();
                $result = $api->deleteDataArr($id_arr_str);
                if($result){
                    $json = [
                        "code"=>"200",
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
                "msg"=>"client error(no post)"
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }

    /**
     *  编辑接口
     */
    public function editApi(){
        if(IS_POST){
            $post = I("post.");
            if($post["id"]){
                $api = new ApiModel();
                $result = $api->saveData($post);
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
                "code"=>"400",
                "msg"=>"client error(no post)"
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }

    /**
     *  设置默认接口信息
     */
    public function defaultApi(){
        if(IS_POST){
            $post = I("post.");
            if($post["id"]){
                $default_api = new Default_apiModel();
                $result = $default_api->saveData($post);
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
                "msg"=>"client error(no post)"
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }
}