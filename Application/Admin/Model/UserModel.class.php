<?php
namespace Admin\Model;
use Think\Model;

class UserModel extends Model{
    /**
     *  验证登录
     * @param $username
     * @param $password
     * @return array|bool|mixed|string
     */
    public function verifyLogin($username,$password){
        $password = md5($password);
        //sql中 BINARY 区分大小写
        $data = $this->where("BINARY username='$username' AND password='$password' ")->find();
        if(!empty($data)){
            return $data;
        }else{
            return false;
        }
    }

    /**********用户列表**************/
    /**
     *  添加用户
     * @param $post
     * @return bool|int|mixed|string
     */
    public function addData($post){
        if($post){
            $post["add_time"] = time();
            $post["password"] = md5($post["password"]);
            $result = $this->add($post);
            if($result){
                return $result;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /** 【所有用户】
     *  编辑用户
     * @param $post
     * @return bool|int|string
     */
    public function saveData($post){
        if(!empty($post["id"])){
            $post["add_time"] = time();
            $result = $this->save($post);
            if($result){
                return $result;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }


    /********登录者的个人信息*********/
    /**
     *  更改该用户的密码
     * @param $old_password
     * @param $new_post
     * @return bool
     */
    public function upDataPwd($old_password,$new_post){
        if($old_password && $new_post){
            $id = $new_post["id"];
            $old_password = md5($old_password);
            $data = $this->where("id='$id' AND password='$old_password'")->find();
            if(!empty($data)){
                $new_post["password"] = md5($new_post["password"]);
                $new_post["add_time"] = time();
                $result = $this->save($new_post);
                if(!empty($result)){
                    return $result;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     *  编辑该用户数据
     * @param $post
     * @return bool|int|string
     */
    public function upData($post){
        if(!empty($post["id"])){
            $post["add_time"] = time();
            $result = $this->save($post);
            if($result){
                return $result;
            }else{
                return false;
            }
        }else{
            return false;
        }

    }

    /**
     *  删除该用户
     * @param $id
     * @return bool|int|mixed|string
     */
    public function deleteData($id){
        if(!empty($id)){
            $data = $this->find($id);
            if($data["head_img_path"]){
                delDirAndFile(WORKING_PATH.$data["head_img_path"],false);
            }
            $result = $this->delete($id);
            if($result){
                return $result;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     *  删除多个用户
     * @param $idArr
     * @return bool|int|mixed|string
     */
    public function deleteDataArr($idArr){
        if($idArr){
            $datas = $this->select($idArr);
            if($datas){
                for($i=0; $i<count($datas); $i++){
                    if($datas[$i]["head_img_path"]){
                        delDirAndFile(WORKING_PATH.$datas[$i]["head_img_path"]);
                    }
                }
            }
            $result = $this->delete($idArr);
            if($result){
                return $result;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}