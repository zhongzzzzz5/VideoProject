<?php
namespace Admin\Model;
use Think\Model;

class Default_apiModel extends Model{
    /**
     *  更新默认接口
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

    /**
     *  更改接口总开关状态
     */
    public function changeSwitch($post){
        if(!empty($post["id"])){
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