<?php
namespace Admin\Controller;
use Think\Controller;

/**
 * Class ApiController
 * @package Admin\Controller
 * @describe 测试系统与后端的连通
 */
class ApiController extends CommonController {
    /**
     *  管理系统与后端是否连通
     */
    public function api(){
        $json = [
            "code"=>200,
            "msg"=>"connect ok"
        ];
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }
}