<?php
namespace Admin\Model;
use Think\Model;

class NoticeModel extends Model{
    /**
     *  添加数据
     * @param $post
     * @return bool|int|mixed|string
     */
    public function addData($post){
        if($post["title"] && $post["content"]){
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
     * @param $post
     * @return bool|int|string
     */
    public function upData($post){
        if($post["id"] && $post["title"] && $post["content"]){
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