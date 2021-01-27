<?php
namespace Admin\Controller;
use Admin\Model\AuthorModel;
use Admin\Model\SystemModel;
use Org\Net\IpLocation;
use Think\Controller;
use Think\Upload;

/**
 * Class SystemController
 * @package Admin\Controller
 * @describe 管理系统配置信息
 */
class SystemController extends Controller{
    /**
     *  系统基本参数
     *  渲染cms系统相关信息
     */
    public function systemInfo(){
        //渲染
        if(IS_GET){
            $model = M("System");
            $data = $model->find();
            if(!empty($data)){
                $json = $data;
                $this->ajaxReturn($json);
            }else{
                $json = [
                    "code"=>500,
                    "msg"=>"server error"
                ];
                $this->ajaxReturn($json);
            }
        }else{
            $json = [
                "code"=>400,
                "msg"=>"client error"
            ];
            $this->ajaxReturn($json);
        }

    }

    /**
     *  系统基本参数
     *  提交cms系统相关信息
     */
    public function submitSystemInfo(){
        //提交
        if(IS_POST){
            $post = I("post.");
            if(!empty($post)){
                $system = new SystemModel();
                $result = $system->upData($post);
                if($result){
                    $json = [
                        "code"=>200,
                        "msg"=>"ok"
                    ];
                    $this->ajaxReturn($json);
                }else{
                    $json = [
                        "code"=>500,
                        "msg"=>"server error"
                    ];
                    $this->ajaxReturn($json);
                }
            }
        }else{
            $json = [
                "code"=>400,
                "msg"=>"client error"
            ];
            $this->ajaxReturn($json);
        }
    }

    /**
     *  联系作者
     *  数据显示
     */
    public function showAuthor(){
        $model = M("Author");
        $data = $model->select();
        if($data){
            $server_url = "http://".$_SERVER['HTTP_HOST'];
            $data[0]["wx_img_path_online"] = $server_url.__ROOT__.$data[0]["wx_img_path"];
            $data[0]["qq_img_path_online"] = $server_url.__ROOT__.$data[0]["qq_img_path"];
            $json = [
                "code"=>200,
                "msg"=>"ok",
                "data"=>$data
            ];
            $this->ajaxReturn($json);
        }else{
            $json = [
                "code"=>500,
                "msg"=>"server error"
            ];
            $this->ajaxReturn($json);
        }
    }

    /**
     *  联系作者
     *  上传表单
     */
    public function contactAuthor(){
        if(IS_POST){
            $post = I("post.");
            $author = new AuthorModel();
            $result = $author->addData($post);
            if($result){
                $json= [
                    "code"=>200,
                    "msg"=>"ok",
                ];
                $this->ajaxReturn($json);
            }else{
                $json = [
                    "code"=>500,
                    "msg"=>"server error"
                ];
                $this->ajaxReturn($json);
            }
        }else{
            $json = [
                "code"=>400,
                "msg"=>"client error"
            ];
            $this->ajaxReturn($json);
        }
    }

    /**
     *  联系作者
     *  编辑
     */
    public function editAuthor(){
        if(IS_POST){
            $post = I("post.");
            $author = new AuthorModel();
            $result = $author->saveData($post);
            if($result){
                $json = [
                    "code"=>200,
                    "msg"=>"ok"
                ];
                $this->ajaxReturn($json);
            }else{
                $json = [
                    "code"=>500,
                    "msg"=>"server error"
                ];
                $this->ajaxReturn($json);
            }
        }else{
            $json = [
                "code"=>400,
                "msg"=>"client error"
            ];
            $this->ajaxReturn($json);
        }
    }

    /**
     *  联系作者
     *  异步上传二维码图
     */
    public function uploadQR(){
        $file = $_FILES["file"];
        if($file['error'] == '0'){
            $cfg = [
                'maxSize'=> 1048576, //上传的文件大小限制，单位byte (0-不做限制) 此次为最大值 1M
                "exts"=> array('jpg', 'gif', 'png', 'jpeg'), //允许上传的文件后缀
                'replace'=>true, //存在同名是否覆盖
                'rootPath'=> WORKING_PATH.UPLOAD_ROOT_PATH."author/" //保存根路径
            ];
            $upload = new Upload($cfg);
            $info = $upload->uploadOne($file);
            if($info){
                $json = [
                    "code"=> 200
                    ,"msg"=> "ok"
                    ,"data"=> [
                        'file_url'=>UPLOAD_ROOT_PATH."author/".$info['savepath'].$info['savename'],
                        'file_name'=>$info['name'],
                    ]
                ];
                $this->ajaxReturn($json);
            }
        }else{
            $json = [
                "code"=> 500
                ,"msg"=> "server error"
                ,"data"=> [
                    "src"=>"000"
                ]
            ];
            $this->ajaxReturn($json);
        }
    }

    /**
     *  登录日志
     */
    public function loginLog(){
        $get =  I("get.");
        $page = $get["page"];//页码

        $offset = $get["limit"];//偏移量
        $start = ($page-1)*$offset;//起始位置
        $model = M("Login_info_log");
        $datas = $model->order("id DESC")->limit("$start,$offset")->select();
        $count = $model->count();
        if($datas){
            $iplocation = new IpLocation("qqwry.dat");
            for($i=0; $i<count($datas); $i++){
                $datas[$i]["No"] = $i+1;
                $datas[$i]["login_time"] = date("Y-m-d H:i:s",$datas[$i]["login_time"]);
                $datas[$i]["logout_time"] = $datas[$i]["logout_time"]? date("Y-m-d H:i:s",$datas[$i]["logout_time"]):"";
                    //因为纯真数据库的gbk的，需转为utf8
                    $country = array_iconv("gbk","utf-8",$iplocation->getlocation($datas[$i]["ip"])["country"]);
                    $area = array_iconv("gbk","utf-8",$iplocation->getlocation($datas[$i]["ip"])["area"]);
                $datas[$i]["ip_location"] = substr($country,1,strlen($country)-2) .",".substr($area,1,strlen($area)-2);
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
                "msg"=>"server not data"
            ];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }
}
