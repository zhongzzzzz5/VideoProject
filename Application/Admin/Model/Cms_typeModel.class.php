<?php
namespace Admin\Model;
use Think\Model;

class Cms_typeModel extends Model{
    /**
     *  删除数据
     * @param $type_id
     * @return bool|int|mixed|string
     */
    public function deleteData($type_id){
        if($type_id){
            $result = $this->delete($type_id);
            return $result?$result:false;
        }else{
            return false;
        }
    }

    /**
     * 处理图标
     * @param $arr
     * @return bool|int|string
     */
    public function saveIconData($arr){
        if($arr["type_id"] && $arr["type_icon"]){
            $data = $this->find($arr["type_id"]);
            if($data["type_icon"]){
                delDirAndFile(WORKING_PATH.$data["type_icon"],false);
                $result = $this->save($arr);
                return $result?$result:false;
            }else{
                $result = $this->save($arr);
                return $result?$result:false;
            }
        }else{
            return false;
        }
    }

    /**
     *  编辑数据
     * @param $arr
     * @return bool|int|string
     */
    public function saveData($arr){
        if($arr["type_id"]){
            $result = $this->save($arr);
            return $result?$result:false;
        }else{
            return false;
        }
    }


    /**
     *  添加数据（上传）
     */
    public function uploadAddData($arr){
        if($arr){
            $data = $this->field("MAX(type_order) as max_type_order")->find();
            $arr["type_order"] = $data["max_type_order"] + 1;
            $result_type_id = $this->add($arr);
            return $result_type_id?$result_type_id:false;
        }else{
            return false;
        }
    }

    /**
     *  向前提等级
     */
    public function up($type_id){
        if($type_id){
            $now_data = $this->find($type_id);
            $now_order = $now_data["type_order"];
            $now_data_type_id = $type_id;

            $last_order = $now_data["type_order"] - 1;
            $last_data = $this->where("type_order = '$last_order'")->find();
            $last_data_type_id = $last_data["type_id"];

            $arr_now = ["type_id"=>$now_data_type_id,"type_order"=>$last_order];
            $result1 = $this->save($arr_now);

            if($result1){
                $arr_last = ["type_id"=>$last_data_type_id,"type_order"=>$now_order];
                $result = $this->save($arr_last);
                return $result?true:false;
            }else{
                return false;
            }

        }else{
            return false;
        }
    }

}
