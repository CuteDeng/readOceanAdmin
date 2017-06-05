<?php
namespace Component;

use Think\Controller;
use Model\AdminModel;

//继承此类可以判断是否登录，只有登陆用户才能访问该方法
class PowerController extends Controller
{
    /**
     * 验证登录
     *
     * @param unknown_type $priv ： 要验证的权限的名字
     */
    public function __construct()
    {
        parent::__construct();
        if (empty(session('edminInfo'))) {
            alertToUrl(__MODULE__ . '/User/login', '请先登录');
        }
    }

    //上传成功删除相应的文件
    public function deleteFile($url)
    {
        $url_new = '.' . substr($url, 8, strlen($url));
        if (file_exists($url_new)) {
            unlink($url_new);
        }
    }

    public function _empty()
    {
        echo '找不到该方法';
    }
}

?>