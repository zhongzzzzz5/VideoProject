<?php
namespace Admin\Model;
use Think\Model;

class Login_infoModel extends Model{
    /**
     *  增加用户登录数据
     * @param $user_info
     * @return bool|int|mixed|string
     */
    public function addData($user_info){
        if($user_info){
            $info =[
                "uid"=>$user_info["id"],
                "uname"=>$user_info["username"],
                "uface"=>$user_info["head_img_path"]?SERVER_URL.__ROOT__.$user_info["head_img_path"]:"",
                "login_status" => 1,
                "login_time"=>time(),
            ];
            $result = $this->add($info);
            if($result){
                $model = M("Login_info_log");
                $arr = [
                    "uid"=>$user_info["id"],
                    "uname"=>$user_info["username"],
                    "uface"=>$user_info["head_img_path"]?SERVER_URL.__ROOT__.$user_info["head_img_path"]:"",
                    "login_time"=>time(),
                    "ip"=>get_client_ip()
                ];
                $log_id = $model->add($arr);
                $count = $model->count();//若数据库的量过多，执行删除前排的数据
                if($count > 100){
                    $num = $log_id-50;
                    $model->where("id<'$num'")->delete();
                }
                //
                return $result;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     *  删除已登录信息
     * @param $uid
     * @return bool|int|string
     */
    public function deleteData($uid){
        if($uid){
            $login_time = $this->find($uid)["login_time"];//获得登录时间
            $result = $this->delete($uid);
            if($result){
                $model = M("Login_info_log");
                $data = $model->where("uid='$uid' AND login_time='$login_time'")->find();//uid加登录时间，找到用户主键信息
                $arr = [
                    "id"=>$data["id"],
                    "logout_time"=>time()
                ];
                $res = $model->save($arr);
                return $result&&$res?$result:false;
            }
        }else{
            return false;
        }
    }
}