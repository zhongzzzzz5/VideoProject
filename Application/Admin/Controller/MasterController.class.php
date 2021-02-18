<?php
namespace Admin\Controller;
use Think\Controller;

/**
 * Class MasterController
 * @package Admin\Controller
 * @describe 母版
 */
class MasterController extends CommonController{
    /**
     *   母版：底部信息
     */
    public function foot(){
        $model = M("system");
        $datas = $model->select();
        if($datas){
            $json = [
                "code"=>200,
                "msg"=>"ok",
                "data"=>$datas[0]
            ];
        }else{
            $json = [
                "code"=>-1,
                "msg"=>"no data"
            ];
        }
        /** @var TYPE_NAME $json */
        $json = json_encode($json);
        echo $json;
    }
}