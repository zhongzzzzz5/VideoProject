<?php
namespace Admin\Model;
use Think\Model;

class AuthorModel extends Model{
    /**
     *  增加作者数据
     * @param $post
     * @return bool|int|mixed|string
     */
    public function addData($post){
        if($post){
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
     *  编辑作者数据
     * @param $post
     * @return bool|int|string
     */
    public function saveData($post){
       if($post){
           $data = $this->find($post['id']);
           if($data == $post){
               return true;
           }else{
               if($data["wx_img_path"]){
                   delDirAndFile(WORKING_PATH.$data["wx_img_path"],false);
               }
               if($data["qq_img_path"]){
                   delDirAndFile(WORKING_PATH.$data["qq_img_path"],false);
               }
               $result = $this->save($post);
               if($result){
                   return $result;
               }else{
                   return false;
               }
           }
       }else{
           return false;
       }
    }
}