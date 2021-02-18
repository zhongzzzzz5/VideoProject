<?php
namespace Admin\Controller;
use Think\Controller;

/**
 * Class MainController
 * @package Admin\Controller
 * @describe 首页/主页
 */
class MainController extends CommonController{
    /**
     *  用户总数
     */
    public function usersNumbers(){
        if(IS_GET){
            $model = M("User");
            //除了超级管理员之外的所有用户数量
            $count = $model->field("t1.*,t2.role")->table("vd_user as t1,vd_role as t2")->where("t1.role_id = t2.role_id AND t1.role_id<>-1")->count();
            if($count){
                $json = [
                    "code"=>200,
                    "msg"=>"ok",
                    "data"=>[
                        "count"=>$count
                    ]
                ];
            }else{
                $json = [
                    "code"=>500,
                    "msg"=>"server error(no users data)"
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
     *  echarts饼图数据，(所有用户)
     */
    public function echartsData(){
        if(IS_GET){
            $model = M("User");
            //数据
            //SELECT t1.role_id,t2.role,COUNT(t1.role_id) as COUNT FROM vd_user as t1,vd_role as t2 WHERE t1.role_id = t2.role_id GROUP BY role_id;
            $datas = $model->field("t1.role_id,t2.role as name,COUNT(t1.role_id) as value")->table("vd_user as t1,vd_role as t2")->where("t1.role_id = t2.role_id")->group("role_id")->select();
            if($datas){
                //图例
                $legend = [];
                for($i=0; $i<count($datas); $i++){
                    array_push($legend,$datas[$i]["name"]);
                }
                //返回数据到前端
                $json = [
                    "code"=>200,
                    "msg"=>"ok",
                    "data"=>[
                        "legend"=>$legend,     //图例
                        "data"=>$datas         //数据
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
     *  App开发
     */
    public function app(){
        $model = M("App");
        $datas = $model->order("add_time DESC")->select();
        $count = $model->count();
        if($datas){
            for($i=0; $i<count($datas); $i++){
                $datas[$i]["server_app_logo_path"] = $datas[$i]["app_logo_path"]?SERVER_URL.__ROOT__.$datas[$i]["app_logo_path"]:"";
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

                $json = [
                    "code" => 200,
                    "msg" => "ok",
                    "count" => $count,
                    "data" => $datas
                ];
            }
        }else{
            $json = [
                "code"=>500,
                "msg"=>"no data"
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }

    /**
     *  在线人数
     */
    public function userOnline(){
        $model = M("Login_info");
        $count = $model->count();
        $json = [
            "code"=>200,
            "msg"=>"ok",
            "count"=>$count
        ];
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }
}