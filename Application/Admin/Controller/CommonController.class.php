<?php
namespace Admin\Controller;
use Think\Controller;

/**
 * Class CommonController
 * @package Admin\Controller
 * @describe 公共类(公共业务处理)
 */
class CommonController extends Controller{
    public function __construct()
    {
        parent::__construct();//构造父类
        /*公共业务处理*/
        $Request_header_PHPSESSID = $_SERVER["HTTP_COOKIE"];//请求头携带的phpsessid
        $client_PHPSESSID = substr($Request_header_PHPSESSID,10,strlen($Request_header_PHPSESSID)-1);//截取开头的PHPSESSID=，剩下需要的phpsessid字符串
        $server_PHPSESSID = session_id();//服务器的phpsessid

        $max_num = M("operation_log")->field("MAX(serial_num) AS max_num")->find()["max_num"];
        $arr = [
            "serial_num" => $max_num?$max_num=$max_num+1:$max_num=1,
            "request_url"=>$_SERVER["REQUEST_URI"],
            "request_method"=>$_SERVER["REQUEST_METHOD"],
            "request_ip"=>$_SERVER["SERVER_ADDR"],
            "times"=>time() - find_num($_SERVER["REQUEST_TIME"]),
            "add_time"=>time()
        ];
        if($client_PHPSESSID != $server_PHPSESSID){
            $arr["is_exception"] = 1;
            M("operation_log")->add($arr);
            $this->ajaxReturn($json = ["code"=>500,"msg"=>"expire"]);exit;
        }else{
            $arr["is_exception"] = 0;
            M("operation_log")->add($arr);
        }
    }

    public function show(){
        dump($_SERVER);
        dump(session_id());
    }

}