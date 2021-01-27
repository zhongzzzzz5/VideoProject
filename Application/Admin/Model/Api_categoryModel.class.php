<?php
namespace Admin\Model;
use Think\Model;

class Api_categoryModel extends Model{
    /**
     * @param $post
     * @return bool
     */
    public function addData($post){
        if($post){
            //SELECT MAX(category_id) as category_id FROM vd_api_category;
            $data = $this->field("MAX(category_id) as category_id")->find();
            if($data){
                $post["category_id"] = $data["category_id"] + 1;
            }else{
                $post["category_id"] = 0;
            }
            $result = $this->add($post);
            return $result?$result:false;
        }else{
            return false;
        }
    }

    /**
     *  删除数据
     * @param $id
     * @return bool|int|mixed|string
     */
    public function deleteData($id){
        if(!empty($id)){
            $data = M("api")->where("category_id = '$id'")->find();//若该分类没有被使用，则可以删除
            if(!$data){
                $result = $this->delete($id);
                return $result?$result:false;
            }else {
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
    public function saveData($post){
        if($post["category_id"]){
            $result = $this->save($post);
            return $result?$result:false;
        }else{
            return false;
        }
    }
}