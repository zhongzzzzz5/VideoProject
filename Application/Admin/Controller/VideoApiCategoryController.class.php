<?php
namespace Admin\Controller;
use Admin\Model\Api_categoryModel;
use Admin\Model\ApiModel;
use Think\Controller;

/**
 * Class VideoApiCategoryController
 * @package Admin\Controller
 * @describe 接口分类管理类
 */
class VideoApiCategoryController extends Controller{
    /**
     *  展示所有分类
     */
    public function apiCategory(){
        $get = I("get.");
        $page = $get["page"];//页码

        $offset = $get["limit"];//偏移量
        $start = ($page-1)*$offset;//起始位置

        $api_category = new Api_categoryModel();
        $datas = $api_category->limit("$start,$offset")->order("category_id")->select();
        $count = $api_category->count();
        if($datas){
            for ($i=0; $i<count($datas); $i++){
                $datas[$i]["category_describe"] = html2text($datas[$i]["category_describe"]);
            }
            $json = [
                "code"=>0,
                "msg"=>"ok",
                "count"=>$count,
                "data"=>$datas
            ];
        }else{
            $json = [
                "code"=>500,
                "msg"=>"server error"
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }

    /**
     *  添加分类
     */
    public function addApiCategory(){
        $post = I("post.");
        if($post){
            $api_category = new Api_categoryModel();
            $result = $api_category->addData($post);
            if($result){
                $json = ["code"=>200,"msg"=>"ok"];
            }else{
                $json = ["code"=>500,"msg"=>"server error"];
            }
        }else{
            $json = ["code"=>400,"msg"=>"client error(no data)"];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }

    /**
     *  删除该分类
     */
    public function deleteApiCategory(){
        $get = I("get.");
        if($get["id"]){
            $api_category = new Api_categoryModel();
            $result = $api_category->deleteData($get["id"]);
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

    /**
     *  编辑该类别
     */
    public function editApiCategory(){
        $post = I("post.");
        if($post["category_id"]){
            $api_category = new Api_categoryModel();
            $result = $api_category->saveData($post);
            if($result){
                $json = ["code"=>200,"ok"];
            }else{
                $json = ["code"=>500,"msg"=>"server error"];
            }
        }else{
            $json = ["code"=>400,"msg"=>"client error(no category_id)"];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }

    /**
     *  浏览
     */
    public function browse(){
        $get = I("get.");
        $page = $get["page"];//页码

        $offset = $get["limit"];//偏移量
        $start = ($page-1)*$offset;//起始位置

        if($get["category_id"]){
            $api = new ApiModel();
            $category_id = $get["category_id"];
            //SELECT t1.*,t2.* FROM vd_api as t1, vd_api_category as t2 WHERE t1.category_id = t2.category_id  AND t1.category_id = 1 ORDER BY id
            $datas = $api->field("t1.*,t2.*")->table("vd_api as t1, vd_api_category as t2")->where("t1.category_id = t2.category_id  AND t1.category_id = '$category_id'")->order("id")->limit("$start,$offset")->select();
            $count = $api->field("t1.*,t2.*")->table("vd_api as t1, vd_api_category as t2")->where("t1.category_id = t2.category_id  AND t1.category_id = '$category_id'")->count();
            if($datas){
                //组装前端需要的格式
                for($i=0; $i<count($datas); $i++){
                    $datas[$i]["api_describe"] = html2text($datas[$i]["api_describe"]);
                    $datas[$i]["add_time"] = date("Y-m-d H:i:s",$datas[$i]["add_time"]);
                }
                $json = ["code"=>0,"msg"=>"ok","count"=>$count,"data"=>$datas];
            }else{
                $json = ["code"=>500,"msg"=>"server(no data)"];
            }
        }else{
            $json = ["code"=>400,"msg"=>"client error(no category_id)"];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }
}