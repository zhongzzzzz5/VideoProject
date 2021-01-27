<?php
namespace Admin\Model;
use Think\Model;

class SystemModel extends Model{
    /**
     *  存入cms系统相关信息
     * @param $post
     * @return bool|int|string
     */
    public function upData($post){
        if(!empty($post)){
            $data = $this->find($post['id']);
            if($data == $post){
                return true;
            }
            $result = $this->save($post);
            if($result){
                return $result;
            }else{
                return  false;
            }
        }
    }
}