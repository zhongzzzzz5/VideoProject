<?php
return array(
	//'配置项'=>'配置值'

    /* 数据库设置 */
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  '119.29.15.197', // 服务器地址
    'DB_NAME'               =>  'db_video',          // 数据库名
    'DB_USER'               =>  'db_video',      // 用户名
    'DB_PWD'                =>  '1998zhong',          // 密码
    'DB_PORT'               =>  '3306',        // 端口
    'DB_PREFIX'             =>  'vd_',    // 数据库表前缀

    //显示跟踪信息
    'SHOW_PAGE_TRACE'       =>  false ,    // 默认false,开启则改写成true (开启，就是查询/展示系统的执行相关状况)

    'DEFAULT_MODULE'        =>  'Admin',  // 默认模块
    'DEFAULT_CONTROLLER'    =>  'Index', // 默认控制器名称
    'DEFAULT_ACTION'        =>  'index', // 默认操作名称

);