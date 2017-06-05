<?php
return array(
    //'配置项'=>'配置值'
    "ERROR_PAGE" => HOST_URL . 'index.php/index/404.html',
    //让页面显示追踪日志信息
    'SHOW_PAGE_TRACE' => false,
    //'配置项'=>'配置值'
//    'APP_GROUP_LIST' => 'Home/School', //项目分组设定
    'DEFAULT_GROUP' => 'Home', //默认分组

    //扩展相关配置文件
    'LOAD_EXT_CONFIG' => 'dbconfig,UploadConfig,logconfig,gotoconfig,UserRightConfig',

    //日志记录组件开关
    'OPERATION_ON' => true,

//    'URL_ROUTER_ON'   => true,
    //URL模式，去掉index.php
    'URL_MODEL' => 2,
    //隐藏模块名
    'DEFAULT_MODULE' => 'Home',
    'MULTI_MODULE' => false,//将拓展的多模块隐藏
    
    'DEFAULT_AJAX_RETURN' => 'json',//默认ajax的返回类型

//    'DB_PATH_NAME' => 'db',        //备份目录名称,主要是为了创建备份目录；
//    'DB_PATH' => './db/',     //数据库备份路径必须以 / 结尾；
//    'DB_PART' => '20971520',  //该值用于限制压缩后的分卷最大长度。单位：B；建议设置20M
//    'DB_COMPRESS' => '1',         //压缩备份文件需要PHP环境支持gzopen,gzwrite函数        0:不压缩 1:启用压缩
//    'DB_LEVEL' => '9',         //压缩级别   1:普通   4:一般   9:最高
);