<?php
namespace Api\Controller;
use Think\Controller;

class ApiController extends Controller{
    /**
     *  验证接口与前端是否连通
     */
    public function api(){
        $app_new_version = false;
        if($app_new_version){
            $json = [
                "code"=> 200,
                "app_new_version"=>1,
                "msg"=>"all data ok",
                "data"=>[
                    "title"=>"版本更新",
                    "content"=>"需要更新，请前去更新",
                    "app_new_version_download"=>"www.baidu.com"
                ]
            ];
        }else{
            $model = M("default_api");
            $data = $model->find();
            $json = [
                "code"=>200,
                "app_new_version"=>0,
                "msg"=>"all data ok",
                "data"=>[
                    "title"=>"版本提示",
                    "content"=>"这是一段文字",
                    "interface_switch"=>$data["switch"],//接口是否开关
                    "interface_url"=>$data["default_api_url"],  //影视解析接口
                ]
            ];
        }
        $this->ajaxReturn($json);
    }
}