<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if (version_compare(PHP_VERSION, '5.3.0', '<')) die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
ob_start();
header("Content-Type:text/html;Charset=utf-8");
// 开启调试模式
define('APP_DEBUG', true);
define('HOST_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/');//服务器路径

define('UPLOAD_URL', 'http://172.21.44.144:8080/ReadingOcean/');//上传路径

define('CONTROLLER', dirname('http://' . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"]));
define('ADMIN_FONT_URL', HOST_URL . 'Public/Edmin/fonts/');
define('ADMIN_EDITOR_URL', HOST_URL . 'Public/Edmin/plugins/simditor/');//simditor编辑器
define('WANGEDITOR', HOST_URL . 'Public/Edmin/plugins/wangeditor/');//
define('ADMIN_CSS_URL', HOST_URL . 'Public/Edmin/css/');
define('ADMIN_JS_URL', HOST_URL . 'Public/Edmin/js/');
define('ADMIN_IMG_URL', HOST_URL . 'Public/Edmin/img/');
define('VUE_URL', HOST_URL . 'Public/Edmin/vue/');

define('UPLOAD_BOOK_URL', UPLOAD_URL . 'book/');//书本的封面的url+分类+id
define('UPLOAD_SEA_GIF_URL', UPLOAD_URL . 'ocean/');//海洋生物的url+分类+id.gif 动画
define('UPLOAD_SEA_JPG_URL', UPLOAD_URL . 'tujian/');//海洋生物的url+分类+id.jpg 图鉴
define('UPLOAD_VIDEO_URL', UPLOAD_URL . 'tinyread/');//微课视频的url+分类+id

//测试用于海洋生物上传
//定义工作路径
define('WORKING_PATH',str_replace('\\','/',__DIR__));
//定义海洋生物图片上传根目录
define('UPLOAD_OCEANANIMALS_TIMG','/Public/Oceantimg/');
define('UPLOAD_OCEANANIMALS_DIMG','/Public/Oceandimg/');

require './Thinkphp/Thinkphp/ThinkPHP.php';
ob_end_flush();
trace();
// 亲^_^ 后面不需要任何代码了 就是如此简单