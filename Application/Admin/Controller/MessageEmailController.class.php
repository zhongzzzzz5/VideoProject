<?php
namespace Admin\Controller;
use Admin\Model\UserModel;
use Think\Controller;

class MessageEmailController extends Controller{

    public function writeEmail(){
        $get =  I("get.");
        $page = $get["page"];//页码

        $offset = $get["limit"];//偏移量
        $start = ($page-1)*$offset;//起始位置

        $word = $get["key"];//模糊查询关键字
        $user = new UserModel();
        if($word){
            $datas = $user->field("t1.id,t1.username,t1.real_name,t1.email")->table("vd_user as t1, vd_role as t2")->where("t1.role_id = t2.role_id AND username LIKE '%$word%'")->order("t1.id")->limit("$start,$offset")->select();
            $count = $user->field("t1.id,t1.username,t1.real_name,t1.email")->table("vd_user as t1, vd_role as t2")->where("t1.role_id = t2.role_id AND username LIKE '%$word%'")->count();
        }else{
            $datas = $user->field("t1.id,t1.username,t1.real_name,t1.email")->table("vd_user as t1, vd_role as t2")->where("t1.role_id = t2.role_id")->order("t1.id")->limit("$start,$offset")->select();
            $count = $user->field("t1.id,t1.username,t1.real_name,t1.email")->table("vd_user as t1, vd_role as t2")->where("t1.role_id = t2.role_id")->count();
        }
        if($datas){
            $json = ["code"=>0,"msg"=>"ok","count"=>$count,"data"=>$datas];
        }else{
            $json = ['code'=>500,"msg"=>"no data"];
        }
        /** @var TYPE_NAME $json */
        $this->ajaxReturn($json);
    }
}