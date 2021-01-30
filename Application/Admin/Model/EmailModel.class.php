<?php
namespace Admin\Model;
use Think\Model;

class EmailModel extends Model{
    /**
     *  添加附件数据
     * @param $arr
     * @return bool|int|mixed|string
     *  成功则返回新建的主键
     */
    public function addFileData($arr){
        if($arr["from_id"] && $arr["file_name"] && $arr["file_path"]){
            $result_id = $this->add($arr);
            return $result_id?$result_id:false;
        }else{
            return false;
        }
    }

    /**
     * @param $arr
     * @return bool|int|string
     * 若上次已上传过，则执行修改，返回受影响的行数
     */
    public function saveFileData($arr){
        if($arr["id"] && $arr["from_id"] && $arr["file_name"] && $arr["file_path"]){
            $arr["add_time"] = time();
            $result = $this->save($arr);
            return $result?$result:false;
        }else{
            return false;
        }
    }

    /**
     *  添加数据
     * @param $post
     * @return bool|int|mixed|string
     */
    public function addData($post){
        if($post["from_id"] && $post["to_id"]){
            $to_id_arr = explode(",",$post["to_id"]);
            if(count($to_id_arr) > 1){
                $result_id = [];
                for($i=0; $i<count($to_id_arr); $i++){
                    $post["to_id"] = $to_id_arr[$i];
                    $post["add_time"] = time();
                    $result_id[$i] = $this->add($post);
                }
                return $result_id?$result_id:false;
            }else{
                $post["add_time"] = time();
                $result_id = $this->add($post);
                return $result_id?$result_id:false;
            }
        }else{
            return false;
        }
    }

    /**
     *  编辑数据
     * @param $post
     * @return bool|int|string|array
     */
    public function saveData($post){
        if($post["id"] && $post["from_id"] && $post["to_id"]){
            $to_id_arr = explode(",",$post["to_id"]);
            if(count($to_id_arr) > 1){
                $result_id = [];//存放操作成功后返回的所有主键
                $data = []; //存放查询到的数据
                for($i=0; $i<count($to_id_arr); $i++){
                    if($i==0){
                        $post["add_time"] = time();
                        $post["to_id"] = $to_id_arr[$i];
                        $result = $this->save($post);
                        $result_id[$i] = $post["id"];
                        $data = $this->find($post["id"]);
                        unset($post["id"]);//移除键
                    }else{
                        $post["add_time"] = time();
                        $post["to_id"] = $to_id_arr[$i];
                        $post["file_name"] = $data["file_name"];
                        $post["file_path"] = $data["file_path"];
                        $result_id[$i] = $this->add($post);
                    }
                }
                return $result_id?$result_id:false;
            }else{
                $post["add_time"] = time();
                $result = $this->save($post);
                return $result?$result:false;
            }
        }else{
            return false;
        }
    }

    /**
     *  删除数据
     * @param $ids_str
     * @return bool|int|mixed|string
     */
    public function deleteData($ids_str){
        if($ids_str){
            $datas = $this->select($ids_str);
            $result = $this->delete($ids_str);
            if($result){
                for($i=0; $i<count($datas); $i++){
                    if($datas[$i]["file_path"]){
                        delDirAndFile(WORKING_PATH.$datas[$i]["file_path"],false);
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