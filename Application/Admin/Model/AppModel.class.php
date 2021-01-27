<?php
namespace Admin\Model;
use Think\Model;

class AppModel extends Model{
    /**
     *  添加数据
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
     *  编辑数据
     * @param $kv
     * @return bool|int|string
     */
    public function saveData($kv){
        if(!empty($kv)){
            $kv["add_time"] = time();
            $result = $this->save($kv);
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
     *  删除该数据
     * @param $id
     * @return bool|int|mixed|string
     */
    public function deleteData($id){
        if(!empty($id)){
            $data = $this->find($id);
            $result = $this->delete($id);
            if($result){
                if($data["app_logo_path"]){
                    delDirAndFile(WORKING_PATH.$data["app_logo_path"]);
                }
                if($data["app_file_path"]){
                    delDirAndFile(WORKING_PATH.$data["app_file_path"]);
                }
                if($data["app_carousel_arr_path"]){
                    $arr = explode(",",$data["app_carousel_arr_path"]);
                    for($i=0; $i<count($arr); $i++){
                        delDirAndFile(WORKING_PATH.$arr[$i]);
                    }
                }
                return $result;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     *  批量删除
     * @param $idArr
     * @return bool|int|mixed|string
     */
    public function deleteDataArr($idArr){
        if($idArr){
            $datas = $this->select($idArr);
            $result = $this->delete($idArr);
            if($result){
                if($datas){
                    for($i=0; $i<count($datas); $i++){
                        if($datas[$i]["app_logo_path"]){
                            delDirAndFile(WORKING_PATH.$datas[$i]["app_logo_path"],false);
                        }
                        if($datas[$i]["app_file_path"]){
                            delDirAndFile(WORKING_PATH.$datas[$i]["app_file_path"],false);
                        }
                        if($datas[$i]["app_carousel_arr_path"]){
                            $arr = explode(",",$datas[$i]["app_carousel_arr_path"]);
                            for($i=0; $i<count($arr); $i++){
                                delDirAndFile(WORKING_PATH.$arr[$i],false);
                            }
                        }
                    }
                }
                return $result;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}
