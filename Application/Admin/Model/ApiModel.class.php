<?php
namespace Admin\Model;
use Think\Model;

class ApiModel extends Model{
    /**
     *  添加接口数据
     * @param $post
     * @return bool|int|mixed|string
     */
    public function addData($post){
        if(!empty($post)){
            $post["add_time"] = time();
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

    /**
     *  删除单个接口数据
     * @param $id
     * @return bool|int|mixed|string
     */
    public function deleteData($id){
        if($id){
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
     *  删除多个接口数据
     * @param $id_arr_str
     * @return bool|int|mixed|string
     */
    public function deleteDataArr($id_arr_str){
        if($id_arr_str){
            $result = $this->delete($id_arr_str);
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
     *  编辑接口数据
     * @param $post
     * @return bool|int|string
     */
    public function saveData($post){
        if($post["id"]){
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
}