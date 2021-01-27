<?php
// +----------------------------------------------------------------------
// | ThinkPHP_3.2.3 [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
header('Content-Type:text/html; charset=utf-8');
header("Access-Control-Allow-Origin: *"); //解决跨域

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',True);

// 定义应用目录
define('APP_PATH','./Application/');


/**
 * 自定义宏开始
 */
//定义工作路径,即框架的路径 ../ThinkPHP_3.2.3
define('WORKING_PATH',str_replace('\\','/',__DIR__)); //str_replace()将 \ 转换成 /

//定义上传文件的根目录
define('UPLOAD_ROOT_PATH','/Public/Upload/');
define('MY_UPLOAD_PATH',WORKING_PATH.UPLOAD_ROOT_PATH);
//服务器地址
define("SERVER_URL","http://".$_SERVER['HTTP_HOST']);
/**
 *  自定义宏结束
 */

// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单