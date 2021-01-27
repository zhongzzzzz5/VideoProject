<?php
namespace Api\Controller;
use Think\Controller;

class UserController extends Controller{
    public function allInterface(){
        $model = M("Api");
        //SELECT api_name as title,api_url as url,api_describe as api_describe FROM vd_api WHERE api_status = 0 ORDER BY id;
        $datas = $model->field("api_name as title,api_url as url,api_describe as api_describe")->where("api_status = 0")->order("id")->select();
        if($datas){
            for ($i=0; $i<count($datas); $i++){
                $datas[$i]["No"] = "接口".($i+1);
            }
            $json = [
                "code"=>200,
                "msg"=>"ok",
                "data"=>$datas
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }
}