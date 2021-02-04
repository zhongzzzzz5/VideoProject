<?php
namespace Libs\Action;

/**
 * Class FileProcessor
 * @package Libs\Action
 * @describe 个人文件处理类
 */
class FileProcessor{
    /**
     * 下载文件
     * @param $file  ***文件的绝对路径
     */
    public static function downloadFile($file){
        //下面的写法是固定的
        header("Content-type: application/octet-stream");
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header("Content-Length: ". filesize($file));
        readfile($file);
    }
}