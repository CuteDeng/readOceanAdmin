<?php
/**
 * Created by PhpStorm.
 * User: Chan
 * Date: 17/3/12
 * Time: 下午3:48
 * Note:用户权限,配合role,privilege表,新增功能是,需要在privilege表按照原来的命名,新增记录。
 */

namespace Home\Controller;

use Component\CommonController;
use Home\Model\RoleModel;
use Think\Controller;
use Component\Classify;//无极分类

class RoleController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
        if (in_array($_SESSION['edminInfo']['role'],C('SudoStr'))) {
            alertToBack('抱歉您无权进入');
        }
    }

    //添加用户角色
    public function roleAdd()
    {
        if (!empty($_POST)) {
            $con['privilege'] = implode($_POST['pri'], ',');
            $con['rolename'] = $_POST['roleName'];
            $con['updatetime'] = time();
            if (M('role')->add($con) || M('role')->add($con) === 0) {
                alertToUrl(__CONTROLLER__ / role, '角色修改成功,将会在下次进入系统时生效');
            } else {
                alertToBack('角色修改失败');
            }
        } else {
            $priList = D('privilege')->select();
            $classify = new Classify();
            $Data = $classify->tree($priList);
            $this->assign('priList', $Data);
            $this->display('Public/header');
            $this->display();
            $this->display('Public/footer');
        }
    }

    //编辑角色权限
    public function roleEdit()
    {
        if (!empty($_POST)) {
            $con['privilege'] = implode($_POST['pri'], ',');
            $con['roleId'] = $_GET['id'];
            $con['rolename'] = $_POST['roleName'];
            $con['updatetime'] = time();
            if (M('role')->save($con) || M('role')->save($con) === 0) {
                alertToUrl(__ACTION__ . '/id/' . $_GET['id'], '角色修改成功,将会在下次进入系统时生效');
            } else {
                alertToBack('角色修改失败');
            }
        } else {
            $priList = D('privilege')->select();
            $info = D('role')->find($_GET['id']);
            $classify = new Classify();
            $Data = $classify->tree($priList);
            $this->assign('priList', $Data);
            $this->assign('info', $info);
            $this->display('Public/header');
            $this->display();
            $this->display('Public/footer');
        }
    }

    public function role()
    {
        $role = M('role')->select();
        for ($i = 0; $i < count($role); $i++) {
            $role[$i]['updatetime'] = date('Y-m-d H:i:s', $role[$i]['updatetime']);
            $role[$i]['count'] = M('edmin')->where('role=' . $role[$i]['roleid'])->count();
        }

        $this->assign('info', $role);
        $this->display('Public/header');
        $this->display();
        $this->display('Public/footer');

    }

}