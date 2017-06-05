<?php
/**
 * Created by PhpStorm.
 * User: Chan
 * Date: 16/12/12
 * Time: 下午3:48
 */

namespace Home\Controller;

use Think\Controller;
use Component\CommonController;

class DataController extends CommonController
{

    //海洋生物
    public function animal()
    {
        $title = array('title' => '海洋生物', 'subtitle' => '管理海洋生物,并统计海洋生物拥有情况', 'type' => 'animal');

        $num = 25;
        $con = [];
        $_GET['p'] = empty($_GET['p']) ? 1 : $_GET['p'];
        $list = M('ocean_animals')->join('system_variables on system_variables.id = ocean_animals.type')->order('requiredLevel')->field('ocean_animals.id,ocean_animals.name,requiredLevel,requiredScore,system_variables.name as type')->limit($num)->page($_GET['p'], $num)->select();
        for ($i = 0; $i < count($list); $i++) {
            $list[$i]['p'] = (intval($_GET['p']) - 1) * $num;
        }
        $count = M('ocean_animals')->where($con)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, $num);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);// 赋值分页输出
        $this->assign("title", $title);
        $this->assign("list", $list);
        $this->display('Public/header');
        $this->display('data/base');
        $this->display('Public/footer');
    }

    //微课
    public function video()
    {
        $title = array('title' => '微课资源', 'subtitle' => '管理微课资源', 'type' => 'video');

        $num = 25;
        $con = [];
        $_GET['p'] = empty($_GET['p']) ? 1 : $_GET['p'];
        if(!empty($_GET['name'])){
           $con['tinyread.name'] = array('like', '%' . $_GET['name'] . '%');  
        }
        $list = M('tinyread')->where($con)->join('system_variables on system_variables.id = tinyread.type')->order('suit')->field('tinyread.id,tinyread.name,suit,system_variables.name as type')->limit($num)->page($_GET['p'], $num)->select();
        for ($i = 0; $i < count($list); $i++) {
            $list[$i]['p'] = (intval($_GET['p']) - 1) * $num;
        }
        $count = M('tinyread')->where($con)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, $num);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);// 赋值分页输出
        $this->assign("title", $title);
        $this->assign("list", $list);
        $this->display('Public/header');
        $this->display('data/base');
        $this->display('Public/footer');
    }

    //积分
    public function score()
    {
        $title = array('title' => '积分等级', 'subtitle' => '管理积分等级', 'type' => 'grand');

        $num = 10;
        $con = [];
        $_GET['p'] = empty($_GET['p']) ? 1 : $_GET['p'];
        $list = M('user_rank_sys')->field('rank,label,required')->limit($num)->page($_GET['p'], $num)->select();


        $data = M('user')->join('user_integral on user_integral.userId = user.userId')->join('school on school.schoolId = user.schoolId')->field('name,schoolName,totalIntegral')->order("totalIntegral desc")->limit($num)->page($_GET['p'], $num)->select();
        for ($i = 0; $i < count($list); $i++) {
            $list[$i]['count'] = M('user_rank')->where('rank=' . $list[$i]['rank'])->count();
        }
        $count = M('user_rank_sys')->where($con)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, $num);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);// 赋值分页输出
        $this->assign("title", $title);
        $this->assign("list", $list);
        $this->assign("data", $data);
        $this->display('Public/header');
        $this->display('data/base');
        $this->display('Public/footer');
    }

    //分类设置
    public function type()
    {
        alertToBack("努力开发中,敬请期待!");
//        $title = array('title' => '分类设置', 'subtitle' => '管理分类', 'type' => 'type');
//        $this->assign("title", $title);
//        $this->display('Public/header');
//        $this->display('data/base');
//        $this->display('Public/footer');
    }

    public function addanimal()
    {
         if(!empty($_POST)) 
        {
            $result=D('Ocean_animals')->addanimals($_POST,$_FILES['timg'],$_FILES['dimg']);
            if($result)
            {
                alertToUrl(U('Data/addanimal'),'上传成功');die;
            }else{
                alertToUrl(U('Data/addanimal'),'未知错误');die;
            }
        }
        $type=M('system_variables')->where(array('type'=>'ocean_animal_type'))->field('id,name')->select();
        $model=M('system_variables')->where(array('type'=>'model_type'))->field('id,name')->select();
        $this->assign('animal',$type);
        $this->assign('model',$model);
        $this->display('Public/header');
        $this->display();
        $this->display('Public/footer');
    }

    public function addvideo()
    {

        $this->display('Public/header');
        $this->display();
        $this->display('Public/footer');
    }

    public function editvideo()
    {
        $con['id'] = $_GET['id'];
        $info = M('tinyread')->where($con)->find();
        if (empty($_GET) || empty($info)) {
            alertToBack('查无此信息');
        }
        $this->assign('info', $info);
        $this->display('Public/header');
        $this->display('Data/addvideo');
        $this->display('Public/footer');
    }

    public function editscoregrade()
    {
        $res = array('tag' => '0', 'msg' => '网络错误');
        if (IS_POST) {
            $con['label'] = $_POST['label'];
            $con['required'] = $_POST['score'];
            $condition['rank'] = $_POST['rank'];
            if (M('user_rank_sys')->where($condition)->save($con)) {
                $res = array('tag' => '1', 'msg' => '修改成功');
            } else {
                $res = array('tag' => '2', 'msg' => '修改失败');
            }
        }
        $this->ajaxReturn($res);
    }
}