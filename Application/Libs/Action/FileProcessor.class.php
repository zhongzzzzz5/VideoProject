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
    public static function downloadFile($file):void{
        //下面的写法是固定的
        header("Content-type: application/octet-stream");
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header("Content-Length: ". filesize($file));
        readfile($file);
    }

    /**
     * 删除文件
     * @param $path  ***待删除目录路径
     * @param bool $delDir  ***是否删除目录，1或true删除目录，0或false则只删除文件保留目录（包含子目录）
     * @return bool  ***返回删除状态
     */
    public static function deleteDirAndFile($path, $delDir = FALSE){
        if (is_array($path)) {
            foreach ($path as $subPath)
                FileProcessor::deleteDirAndFile($subPath, $delDir);
        }
        /** @var TYPE_NAME $path */
        if (is_dir($path)) {
            $handle = opendir($path);
            if ($handle) {
                while (false !== ( $item = readdir($handle) )) {
                    if ($item != "." && $item != "..")
                        is_dir("$path/$item") ? FileProcessor::deleteDirAndFile("$path/$item", $delDir) : unlink("$path/$item");
                }
                closedir($handle);
                if ($delDir)
                    /** @var TYPE_NAME $path */
                    return rmdir($path);
            }
        } else {
            /** @var TYPE_NAME $path */
            if (file_exists($path)) {
                return unlink($path);
            } else {
                return FALSE;
            }
        }
        clearstatcache();
    }

}