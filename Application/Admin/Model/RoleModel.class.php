<?php
namespace Admin\Model;
use Think\Model;

class RoleModel extends Model{
    /**
     *  添加等级(角色)
     * @param $role
     * @return bool|int|mixed|string
     */
    public function addData($role){
        if($role){
            //SELECT MAX(role_id) as max_role_id FROM vd_role;
            $data = $this->field("MAX(role_id) as max_role_id")->find();
            if($data["max_role_id"] == 5){
               return false;
            }else{
                $arr = [
                    "role_id"=>$data["max_role_id"]+1,
                    "role"=>$role
                ];
                $result = $this->add($arr);
                return $result?$result:false;
            }
        }else{
            return false;
        }
    }

    /**
     *  删除等级（角色）
     * @param $role_id
     * @return bool|int|mixed|string
     */
    public function deleteData($role_id){
        if($role_id){
            if($role_id <= 1){//不允许删除小于等于2的角色等级
                return false;
            }else{
                $user = new UserModel();
                //SELECT t1.*,t2.role FROM vd_user as t1, vd_role as t2 WHERE t1.role_id = t2.role_id AND t1.role_id = 1
                $data = $user->field("t1.*,t2.role")->table("vd_user as t1, vd_role as t2")->where("t1.role_id = t2.role_id AND t1.role_id = '$role_id'")->find();
                if($data){
                    return false;
                }else{
                    $result = $this->delete($role_id);
                    return $result?$result:false;
                }
            }
        }else{
            return false;
        }
    }
}