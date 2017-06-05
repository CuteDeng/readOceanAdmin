<?php
/**
 * Created by PhpStorm.
 * User: Chan
 * Date: 16/12/12
 * Time: 下午3:48
 */

namespace Home\Controller;

use Component\CommonController;
use Think\Controller;

class SystemController extends CommonController
{
    //后台用户界面
    public function users()
    {
        $num = 25;
        if (!(empty($_SESSION['edminInfo']['school']))) {
            $con['roleId'] = array('IN', '3,5');//学校管理员只能在以及管理员和二级管理员之间选择
            $con['schoolId'] = $_SESSION['edminInfo']['school'];
        } else {
            if ($_SESSION['edminInfo']['role'] != 1) {
                $con['roleId'] = array('NOT IN', '1');//学校管理员只能在以及管理员和二级管理员之间选择
            }
        }
        $tag = M('role')->where($con)->select();
        if (empty($_GET['p'])) {
            $_GET['p'] = 1;
        }
        if (IS_GET || !empty($_GET['type'])) {
            if ($_GET['type'] == 1) {
                $con['status'] = 0;
            }
            if ($_GET['type'] == 2) {
                $con['status'] = 1;
            }
            if (!empty($_GET['username'])) {
                $con['edmin.name'] = array('LIKE', "%" . $_GET['username'] . "%");
            }
        }
        if (!empty($_GET['role'])) {
            $con['role'] = $_GET['role'];
        }
        //分页
        $list = M('edmin')->join('role on role=roleId')->where($con)->order('eid desc')->limit($num)->page($_GET['p'], $num)->select();
        $count = M('edmin')->join('role on role=roleId')->where($con)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, $num);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('tag', $tag);// 赋值分页输出
        $this->assign('list', $list);
        $this->display('Public/header');
        $this->display();
        $this->display('Public/footer');
    }

    //添加用户界面,界面显示
    public function addusr()
    {
        if (!empty($_SESSION['edminInfo']['school'])) {
            $con['roleId'] = array('IN', '3,5');//学校管理员只能在以及管理员和二级管理员之间选择
            $tag = M('role')->field('roleid,rolename')->where($con)->order('roleid')->select();
        } else if ($_SESSION['edminInfo']['role'] != 1) {
            $con['roleId'] = array('NEQ', '1');//非超级管理员及其他管理员
            $tag = M('role')->field('roleid,rolename')->where($con)->order('roleid')->select();
        } else {//超级管理员
            $tag = M('role')->field('roleid,rolename')->order('roleid')->select();
        }
        $this->assign('role', $tag);
        $this->display('Public/header');
        $this->display('System/usrbase');
        $this->display('Public/footer');
    }

    //添加用户执行方法
    public function addusers()
    {
        $res = JsonParse('1', '用户添加失败。');
        if (IS_POST) {
            $con['usr'] = $_POST['usr'];
            $con['name'] = $_POST['username'];
            $con['pwd'] = sha1($_POST['pwd']);
            $con['role'] = $_POST['role'];
            if (empty($_SESSION['edminInfo']['school'])) {
                $con['schoolId'] = $_POST['school'];
                $con['gradeId'] = $_POST['grade'];
            } else {
                $con['schoolId'] = mb_substr($_POST['grade'], 0, 7);
                $con['gradeId'] = $_POST['grade'];
            }
            $con['enrolltime'] = time();
            if (!D('User')->checkusername($con['usr'])) {
                $res = JsonParse('2', '用户名已存在,请重新输入。');
            } else {
                if (M('edmin')->add($con)) {
                    $data['usr'] = $_POST['usr'];
                    $id = M('edmin')->where($data)->getField('eid');
                    WriteLog($id);
                    $res = JsonParse('0', '用户添加成功');
                }
            }
        }
        $this->ajaxReturn($res);
    }

    //查看用户缴费情况
    public function paycheck()
    {

        $num = 25;
        if (empty($_GET['p'])) {
            $_GET['p'] = 1;
        }
        if (!empty($_GET['username'])) {
            $con['user.name'] = array('LIKE', "%" . $_GET['username'] . "%");
        }
        $con['schoolId'] = '1000000';
        $data = M('user');
        if (IS_GET && !empty($_GET['type']) && $_GET['type'] != 2) {
            switch ($_GET['type']) {
                case 1:
                    $con['status'] = '1';
                    break;//激活
                case 3:
                    $con['status'] = '0';
                    break;//停用
            }
        }
        if (!IS_GET || $_GET['type'] != 2) {
            $count = $data->join('left join user_bill on user.userId = user_bill.userId')->where($con)->where('schoolId=1000000')->count();// 查询满足要求的总记录数
            $list = $data->join('left join user_bill on user.userId = user_bill.userId')->where($con)->where('schoolId=1000000')->order('date desc')->limit($num)->page($_GET['p'], $num)->field('user.userId,user.name,status,user.remarks,date,out_trade_no')->select();
        }
        //查看欠费用户,通过mysql 日期函数处理缴费时间
        if ($_GET['type'] == 2) {
            $count = $data->where($con)->join('left join user_bill on user.userId = user_bill.userId')->where($con)->where("TO_DAYS(NOW()) > TO_DAYS(DATE_ADD(date,interval 1 year))")->count();// 查询满足要求的总记录数
            $list = $data->join('left join user_bill on user.userId = user_bill.userId')->where($con)->where("TO_DAYS(NOW()) > TO_DAYS(DATE_ADD(date,interval 1 year))")->order('date')->limit($num)->page($_GET['p'], $num)->field('user.userId,user.name,status,user.remarks,date,out_trade_no')->select();
        }
        //处理显示欠费问题
        for ($i = 0; $i < count($list); $i++) {
            $list[$i]['p'] = $_GET['p'];
            if (!empty($list[$i]['date'])) {
                $date = date_create($list[$i]['date']);
                $dates = date_add($date, date_interval_create_from_date_string("1 year"));
                $dates = objectToArray($dates);
                if (time() > strtotime($dates['date'])) {
                    $list[$i]['outdate'] = 1;
                }
            }
        }
        $schoolst = M('school')->select();
        for ($i = 0; $i < count($schoolst); $i++) {
            if (!empty($schoolst[$i]['paytime'])) {
                $schoolst[$i]['paytime'] = date('Y-m-d', $schoolst[$i]['paytime']);
                $date = date_create($schoolst[$i]['paytime']);//创建一个时间对象
                $dates = date_add($date, date_interval_create_from_date_string("1 year"));
                $dates = objectToArray($dates);
                if (time() > strtotime($dates['date'])) {
                    $schoolst[$i]['outdate'] = 1;
                }
            }
        }
        //page
        $Page = new \Think\Page($count, $num);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('list', $list);
        $this->assign('schoolst', $schoolst);
        $this->assign('page', $show);// 赋值分页输出

        $this->display('Public/header');
        $this->display();
        $this->display('Public/footer');
    }

    //意见反馈
    public function opinion()
    {
        $num = 25;
        if (!empty($_GET['type']) && $_GET['type'] != 4 && $_GET['type'] != 3) {
            $con['tag'] = $_GET['type'];
        } else if ($_GET['type'] == '3') {
            $con['tag'] = '0';
        }
        if (!empty($_GET['name'])) {
            $con['name'] = array('like', '%' . $_GET['name'] . '%');
        }
        if (empty($_GET['p'])) {
            $_GET['p'] = 1;
        }

        $list = M('feedback')->join(' user on user.userId=feedback.userId')->where($con)->order('id desc')
            ->field('feedback.id,feedback.userId,name,content,feedback.time,tag')->limit($num)->page($_GET['p'], $num)->select();

        for ($i = 0; $i < count($list); $i++) {
            $list[$i]['p'] = (intval($_GET['p']) - 1) * $num;
        }
        $this->assign('list', $list);// 赋值数据集
        //分页
        $count = M('feedback')->join(' user on user.userId=feedback.userId')->where($con)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, $num);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);// 赋值分页输出
        $this->display('Public/header');
        $this->display();
        $this->display('Public/footer');
    }

    //意见反馈细节
    public function opiniondetail()
    {
        if (IS_GET && $_GET['action']) {
            if ($_GET['action'] == 1) {
                $con['tag'] = 1;
            } else if ($_GET['action'] == 2) {
                $con['tag'] = 2;
            }
            M('feedback')->where("feedback.id=" . $_GET['id'])->save($con);
        }
        $info = M('feedback')->join(' user on user.userId=feedback.userId')->where("feedback.id=" . $_GET['id'])->field('feedback.id,feedback.userId,name,contact,content,feedback.time,tag')->select();
        $user_comment = M('feedback_answer')->join(' user on user.userId=feedback_answer.userId')->where("feedbackId=" . I('get.id') . ' and feedback_answer.role = 0')->field('aid,name,content,feedback_answer.time,feedback_answer.role')->select();
        $edmin_comment = M('feedback_answer')->join(' edmin on eid=feedback_answer.userId')->where("feedbackId=" . I('get.id') . ' and feedback_answer.role = 1')->field('aid,name,content,feedback_answer.time,feedback_answer.role')->select();
        $this->assign('info', $info[0]);
        $this->assign('comment', array_merge($edmin_comment, $user_comment));//数组合并
        $this->display('Public/header');
        $this->display();
        $this->display('Public/footer');
    }

    //管理员回复意见反馈
    public function comment()
    {
        if (I('post.comment')) {
            $con['content'] = I('post.comment');
            $con['feedbackId'] = I('get.id');
            $con['userid'] = session('edminInfo.userId');
            $con['time'] = time();
            $con['role'] = 1;
            $tag = M('feedback_answer')->add($con);
            if ($tag) {
                alertToUrl(__CONTROLLER__ . '/opiniondetail/id/' . $con['feedbackId'], '回复成功');
            } else {
                alertToUrl(__CONTROLLER__ . '/opiniondetail/id/' . $con['feedbackId'], '回复失败,请稍后再试');
            }
        }
    }

    //重置密码
    public function reset()
    {
        $con['eid'] = $_GET['id'];
        $reset['pwd'] = sha1(md5('123123'));
        if (M('edmin')->where($con)->save($reset)) {
            $name = M('edmin')->where($con)->getField('name');
            alertToBack('用户-' . $name . '的密码已重置为默认密码。');
        } else {
            alertToBack('网络错误');
        }
    }

    //禁用后台管理员
    public function closeuser()
    {
        if (empty($_GET['p'])) {
            $_GET['p'] = 1;
        }
        if (empty($_GET['type'])) {
            $_GET['type'] = 0;
        }
        $con['eid'] = $_GET['id'];
        $reset['status'] = 1;
        if (M('edmin')->where($con)->save($reset)) {
            alertToUrl(__CONTROLLER__ . '/users' . getRightUrl(), '用户已禁用');
        } else {
            alertToBack('网络错误');
        }
    }

    //启用后台管理员
    public function openuser()
    {
        if (empty($_GET['p'])) {
            $_GET['p'] = 1;
        }
        if (empty($_GET['type'])) {
            $_GET['type'] = 0;
        }
        $con['eid'] = $_GET['id'];
        $reset['status'] = 0;
        if (M('edmin')->where($con)->save($reset)) {
            alertToUrl(__CONTROLLER__ . '/users' . getRightUrl(), '用户已启用');
        } else {
            alertToBack('网络错误');
        }
    }

    //散客用户的禁用和激活
    public function closestu()
    {
        if (empty($_GET['p'])) {
            $_GET['p'] = 1;
        }
        $con['userId'] = $_GET['id'];
        $reset['status'] = 0;
        if (M('user')->where($con)->save($reset)) {
            alertToUrl(__CONTROLLER__ . '/paycheck' . getRightUrl(), '用户已禁用');
        } else {
            alertToBack('网络错误');
        }
    }

    //散客用户的禁用和激活
    public function openstu()
    {
        if (empty($_GET['p'])) {
            $_GET['p'] = 1;
        }
        $con['userId'] = $_GET['id'];
        $reset['status'] = 1;
        if (M('user')->where($con)->save($reset)) {
            alertToUrl(__CONTROLLER__ . '/paycheck' . getRightUrl(), '用户已启用');
        } else {
            alertToBack('网络错误');
        }
    }

    //学校管理员
    public function findSchool()
    {
        if (!empty($_SESSION['edminInfo']['school'])) {
            $con['schoolId'] = $_SESSION['edminInfo']['school'];
        }
        $res = M('school')->where($con)->select();
        $str = "<select style='' class='form-control col-sm-5' id='school' name='school' required>";
        if (!empty($res)) {
            $str .= "<option value=''> 请选择</option>";
            foreach ($res as $v) {
                $str .= "<option value='" . $v['schoolid'] . "'> " . $v['schoolname'] . "</option>";
            }
            $resp = array('msg' => '1', 'str' => $str);
        }
        $str .= "</select>";
        if (empty($res)) {
            $str = '';
            $resp = array('msg' => '0', 'str' => $str);
        }

        $this->ajaxReturn($resp);
    }

    //二级学校管理员-年级管理员
    public function findGrade()
    {
        //如果是学校管理员
        if (!empty($_SESSION['edminInfo']['school'])) {
            $con['grade . schoolId'] = $_SESSION['edminInfo']['school'];
        }
        $res = M('grade')->join('school on school . schoolId = grade . schoolId')->where($con)->select();
        $str = "<select style='' class='form-control col-sm-5' id='grade' name='grade' required>";
        if (!empty($res)) {
            $str .= "<option value=''> 请选择</option>";
            foreach ($res as $v) {
                $str .= "<option value='" . $v['gradeid'] . "'> " . $v['schoolname'] . "-" . $v['gradename'] . "</option>";
            }
            $resp = array('msg' => '1', 'str' => $str);
        }
        $str .= "</select>";
        if (empty($res)) {
            $str = '';
            $resp = array('msg' => '0', 'str' => $str);
        }

        $this->ajaxReturn($resp);
    }

    //检查用户名是否重复
    public function checkName()
    {
        $name = $_GET['name'];
        if (D('User')->checkusername($name)) {
            $res['tag'] = 1;
        } else {
            $res['tag'] = 0;
        }
        $this->ajaxReturn($res);
    }

    //管理员或年级管理员修改用户权限
    public function userinfo()
    {
        $res['tag'] = 1;
        $con['eid'] = $_GET['id'];
        if (!empty($_SESSION['edminInfo']['school'])) {
            $con['roleId'] = array('IN', C('SchoolUser'));//学校管理员只能在以及管理员和二级管理员之间选择
            $tag = M('role')->field('roleid,rolename')->where($con)->order('roleid')->select();
        } else if ($_SESSION['edminInfo']['role'] != 1) {
            $con['roleId'] = array('NEQ', '1');//学校管理员只能在以及管理员和二级管理员之间选择
            $tag = M('role')->field('roleid,rolename')->where($con)->order('roleid')->select();
        } else {
            $tag = M('role')->field('roleid,rolename')->where($con)->order('roleid')->select();
        }
        $info = M('edmin')->where($con)->find();
        $this->assign('info', $info);
        $this->assign('role', $tag);//角色列表
        $this->display('Public/header');
        $this->display('System/usrinfo');
        $this->display('Public/footer');
    }

    //修改用户信息,修改后台用户信息,非本人操作
    public function editinfo()
    {
        $res = JsonParse('1', '网络错误,修改失败');
        $con['eid'] = $_POST['id'];
        if (!empty($_SESSION['edminInfo']['school']) || !empty($_POST['school'])) {
            $con['schoolId'] = !empty($_SESSION['edminInfo']['school']) ? $_SESSION['edminInfo']['school'] : $_POST['school'];
        } else {
            $con['schoolId'] = "";
        }
        if (!empty($_POST['grade'])) {
            $con['schoolId'] = mb_substr($_POST['grade'], 0, 7);
            $con['gradeId'] = $_POST['grade'];
        } else {
            $con['gradeId'] = "";//说明已经不是年级管理员,而是其他类型的管理员
        }
        $con['name'] = $_POST['username'];
        $con['role'] = $_POST['role'];
        if (M('edmin')->save($con) || M('edmin')->save($con) === 0) {
            $res = JsonParse('0', '修改成功');
            WriteLog($_POST['id']);
        }
        $this->ajaxReturn($res);
    }

    //更新缴费时间
    public function updateschoolbill()
    {
        $res = array('tag' => '0', 'msg' => '网络错误');
        if (IS_POST) {
            $condition['schoolId'] = $_POST['school'];
            $con['bill_num'] = $_POST['bill'];
            $con['paytime'] = strtotime($_POST['time']);
            if (!empty($con['paytime'])) {
                $date = date_create($_POST['time']);//创建一个时间对象-对象要求为时间格式
                $dates = date_add($date, date_interval_create_from_date_string("1 year"));
                $dates = objectToArray($dates);
                if (time() < strtotime($dates['date'])) {
                    $con['status'] = 1;
                }
            }
            if (M('school')->where($condition)->save($con)) {
                $res = array('tag' => '1', 'msg' => '修改成功');
            } else {
                $res = array('tag' => '2', 'msg' => '修改失败');
            }
        }
        $this->ajaxReturn($res);
    }

    //查看日志记录
    public function reviewLogs()
    {
        $num = 25;
        $con = [];
        if (empty($_GET['p'])) {
            $pages = 1;
        } else {
            $pages = $_GET['p'];
        }
        if (!empty($_GET['startime']) || !empty($_GET['endtime'])) {
            $start = !empty($_GET['startime']) ? strtotime($_GET['startime']) : "0";
            $end = !empty($_GET['endtime']) ? strtotime(date('Y-m-d 23:59:59', strtotime($_GET['endtime']))) : time();
            $con['time'] = array(array('gt', $start), array('lt', $end));
        } else {
            $_GET['startime'] = getDay(1);
            $start = getDay(1, 1, 1);//昨天,时间戳,0时开始
            $_GET['endtime'] = getDay();
            $end = getDay(0, 1, 1);//今天,时间戳,0时结束
            $con['time'] = array(array('gt', $start), array('lt', $end));
        }
        if (!empty($_GET['name'])) {
            $con['name'] = array('like', '%' . $_GET['name'] . '%');
        }

        $Ip = new \Org\Net\IpLocation('UTFWry.dat'); // 实例化ip分类
        $role = $_SESSION['edminInfo']['role'];
        //显示系统角色权限,用户只能看到自己权限下的日志记录。
        if (!empty($role)) {
            switch ($role) {
                case '1':
                    break;//超级管理员可以所有人的
                case '3':
                    $con['role'] = array('in', '3,5');
                    $con['schoolId'] = $_SESSION['edminInfo']['school'];
                    break;//学校管理员只能看自己和年级管理员
                case '5':
                    $con['role'] = 5;
                    $con['schoolId'] = $_SESSION['edminInfo']['school'];
                    break;//学校管理员只能看自己和年级管理员
                default:
                    $con['role'] = array('neq', '1');
                    break;//除了超级管理员外不能看到日志
            }
        }
        $info = M('logs')->join('edmin on eid=userid')->where($con)->order('time desc')->limit($num)->page($_GET['p'], $num)->field('logid as id,name,time,c,a,ip')->select();
        if (!empty($info)) {
            foreach ($info as $key => $val) {
                $info[$key]['p'] = (intval($pages) - 1) * $num;//为了序号的分页
                $ip_info = $Ip->getlocation($info[$key]['ip']);
                $info[$key]['city'] = $ip_info['country'];
            }
        }
        $this->assign('list', $info);
        $count = M('logs')->join('edmin on eid=userid')->where($con)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, $num);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);// 赋值分页输出
        $this->display('Public/header');
        $this->display();
        $this->display('Public/footer');
    }

    //日志详情
    public function logdetail()
    {
        $Ip = new \Org\Net\IpLocation('UTFWry.dat'); // 实例化ip分类
        $role = $_SESSION['edminInfo']['role'];
        if (!empty($role) && !empty($_GET['id'])) {
            $con['logid'] = $_GET['id'];
            switch ($role) {
                case '1':
                    break;//超级管理员可以所有人的
                case '3':
                    $con['role'] = array('in', '3,5');
                    break;//学校管理员只能看自己和年级管理员
                case '5':
                    $con['role'] = '5';
                    break;//学校管理员只能看自己和年级管理员
                default:
                    $con['role'] = array('neq', '1');
                    break;//除了超级管理员外不能看到日志
            }
        }
        //获取当前日志的人员名单。
        $log_role = M('logs')->join('edmin on eid=userid')->where($con)->getField('role');
        switch ($log_role) {
            case '3':
            case '5':
                $info = M('logs')->join(array('edmin on eid=userid', 'role on edmin.role=role.roleId', 'school on edmin.schoolId = school.schoolId'))->where($con)->order('time desc')->field('logid as id,name,time,c,a,ip,agent,role,rolename,schoolname,logs.id as tagid')->find();
                break;
            default:
                $info = M('logs')->join('edmin on eid=userid')->join('role on edmin.role=role.roleId')->where($con)->order('time desc')->field('logid as id,name,time,c,a,ip,agent,rolename,role,logs.id as tagid')->find();
                break;
        }
        if (!empty($info)) {
            $ip_info = $Ip->getlocation($info['ip']);
            $info['city'] = $ip_info['country'];
            $info['agent'] = getOS($info['agent']) . " - " . get_client_browser($info['agent'], " - ");
//            上传的控制器需要显示文件名,通过切割,分割出文件名和相关id
            if ($info['c'] == 'upload') {
                $arr = explode('--exp--', $info['tagid']);
                $info['file'] = substr($arr[0], 2);
                $info['tagid'] = $arr[1];
            }
        } else {
            alertToBack('日志不存在');
        }
        $this->assign("info", $info);
        $this->display('Public/header');
        $this->display();
        $this->display('Public/footer');
    }

    //用户修改个人信息,页面渲染
    public function edituserinfo()
    {
        $con['eid'] = $_SESSION['edminInfo']['userId'];
        $info = M('edmin')->where($con)->find();
        $this->assign('info', $info);
        $this->display('Public/header');
        $this->display('User/userinfo');
        $this->display('Public/footer');
    }

    //意见反馈
    public function feedback()
    {
        if (IS_POST) {
            $data['userId'] = $_SESSION['edminInfo']['userId'];
            $data['title'] = $_POST['title'];
            $data['content'] = $_POST['content'];
            $data['level'] = $_POST['tags'];
            $data['time'] = time();
            if (!M('edmin_feedback')->add($data)) {
                alertToBack('发布失败~');
            }
        }
        $level = M('system_variables')->where('type=\'edmin_feedback_level\'')->select();
        $this->assign('level', $level);
        $this->display('Public/header');
        $this->display('User/feedback');
        $this->display('Public/footer');
    }

    //其他管理员能看到的意见反馈列表
    public function feedbacklist()
    {
        $num = 15;
        if (!empty($_GET['type'])) {
            $con['level'] = $_GET['type'];
        }
        if (empty($_GET['p'])) {
            $_GET['p'] = 1;
        }
        //管理员或超级管理员
        if (in_array($_SESSION['edminInfo']['role'], C('AdminUser'))) {
            $list = M('edmin_feedback')->join('system_variables on level = system_variables.id')->where($con)->order('time desc,awaitnum desc')->limit($num)->page($_GET['p'], $num)->select();
        } else {
            //这里的意见反馈只检索出该给用户看的~
            $con['level'] = array('NOT in', array('feedback_good', 'feedback_forward', 'feedback_update'));
            $con['isAnswer'] = 1;
            $list = M('edmin_feedback')->join('system_variables on level = system_variables.id')->where($con)->order('time desc,awaitnum desc')->limit($num)->page($_GET['p'], $num)->select();;
        }
        $type = M('system_variables')->where("type='edmin_feedback_level'")->select();
        $this->assign('list', $list);
        $this->assign('type', $type);
        $count = M('edmin_feedback')->join('system_variables on level = system_variables.id')->where($con)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, $num);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);// 赋值分页输出
        $this->display('Public/header');
        $this->display('User/feedbacklist');
        $this->display('Public/footer');
    }

    //编辑意见反馈
    public function editfeedback()
    {
        if (!empty($_GET['id'])) {
            $con['edmin_feedback.feedbackId'] = $_GET['id'];
            $info = M('edmin_feedback')->join('system_variables on level = system_variables.id')->where($con)->find();
        }
        if (empty($_GET['id']) || empty($info)) {
            alertToBack('找不到该条意见反馈');
        } else {
            if ($info['isanswer'] == 1) {
                $info = M('edmin_feedback')->join('edmin_feedback_answer on edmin_feedback.feedbackId = edmin_feedback_answer.feedbackId')->join('system_variables on level = system_variables.id')->where($con)->find();
            }
        }
        $type = M('system_variables')->where("type='edmin_feedback_level'")->select();
        $this->assign('info', $info);
        $this->assign('type', $type);
        $this->display('Public/header');
        $this->display('user/editfeedback');
        $this->display('Public/footer');
    }

    //意见反馈详情
    public function feedbackdetail()
    {
        if (!empty($_GET['id'])) {
            $con['feedbackId'] = $_GET['id'];
            $info = M('edmin_feedback')->join('system_variables on level = system_variables.id')->where($con)->find();
        }
        if (empty($_GET['id']) || empty($info)) {
            alertToBack('找不到该条意见反馈');
        } else {
            if ($info['isanswer'] == 1) {
                $answer = M('edmin_feedback_answer')->join('edmin on edminId = eid')->where($con)->field('name,answer,responsetime')->find();
            }
        }
        $type = M('system_variables')->where("type='edmin_feedback_level'")->select();
        $this->assign('info', $info);
        $this->assign('answer', $answer);
        $this->assign('type', $type);
        $this->display('Public/header');
        $this->display('user/feedbackdetail');
        $this->display('Public/footer');
    }

    //保存意见反馈
    public function savefeedback()
    {
        $model = new \Think\Model();
        $model->startTrans();
        if (IS_POST) {
            $con['feedbackId'] = $_GET['id'];
            $data['title'] = $_POST['title'];
            $data['content'] = $_POST['content'];
            $data['level'] = $_POST['tags'];
            if (isset($_POST['open'])) {
                $data['isAnswer'] = 1;
            } else {
                $data['isAnswer'] = 0;
            }
            if (!empty($_POST['tag'])) {
                $data['remarks'] = $_POST['tag'];
            }
            $data['edittime'] = time();
            $flag_feed = M('edmin_feedback')->where($con)->save($data);
            //保存是否成功
            if ($flag_feed || $flag_feed === 0) {
                //如果有管理员回复的话
                if (!empty($_POST['answers'])) {
                    $answer = M('edmin_feedback_answer')->where('feedbackId=' . $con['feedbackId'])->find();
                    //如果曾经回复过,则需要覆盖掉
                    if ($answer) {
                        $datas['answerId'] = $answer['answerid'];
                        $update['answer'] = $_POST['answers'];
                        $update['responseTime'] = time();
                        $update['edminId'] = $_SESSION['edminInfo']['userId'];
                        if (M('edmin_feedback_answer')->where($datas)->save($update)) {
                            $model->commit();
                            alertToUrl(__CONTROLLER__ . "/feedbacklist", '管理员回复成功');
                        } else {
                            $model->rollback();
                            alertToBack('发布修改失败~');
                        }
                    }//如果没有回复过,则需要新建
                    else {
                        $update['answer'] = $_POST['answers'];
                        $update['feedbackId'] = $con['feedbackId'];
                        $update['responseTime'] = time();
                        $update['edminId'] = $_SESSION['edminInfo']['userId'];
                        $update['isDone'] = isset($_POST['open']) ? '1' : '0';
                        if (M('edmin_feedback_answer')->add($update)) {
                            $model->commit();
                            alertToUrl(__CONTROLLER__ . "/feedbacklist", '管理员回复成功');
                        } else {
                            $model->rollback();
                            alertToBack('发布修改失败~');
                        }
                    }
                } else {
                    $model->commit();
                    alertToUrl(__CONTROLLER__ . "/feedbacklist", '修改成功');
                }
            } else {
                $model->rollback();
                alertToBack('发布修改失败~');
            }
        }
    }

    //增加点击量
    public function addClickNum()
    {
        $con['feedbackId'] = $_POST['id'];
        if (M('edmin_feedback')->where($con)->setInc('clicknum')) {
            $res['tag'] = 1;
        } else {
            $res['tag'] = 0;
        }
        $this->ajaxReturn($res);
    }

    //系统公告
    public function News()
    {
        $list = M('school')->field('schoolId,schoolName')->select();
        $tag = M('system_variables')->where("type='edmin_news_type'")->select();
        $title = array('title' => '创建系统公告', 'subtitle' => '编辑系统公告。');
        $this->assign('title', $title);
        $this->assign('list', $list);
        $this->assign('tag', $tag);
        $this->display('Public/header');
        $this->display();
        $this->display('Public/footer');
    }

    //公告详情
    public function newdetail()
    {
        $con['newId'] = $_GET['id'];
        $info = M('edmin_news')->where($con)->find();
        if (!empty($info) && !empty($_GET['id'])) {
            $info = M('edmin_news')->join('system_variables on id = tag')->where($con)->find();
            $this->assign('info', $info);
            $this->display('Public/header');
            $this->display();
            $this->display('Public/footer');
        } else {
            alertToUrl(__CONTROLLER__ . 'newprelist', '查无此系统公告');
        }

    }

    //修改系统公告
    public function editNew()
    {
        $con['newId'] = $_GET['id'];
        $info = M('edmin_news')->where($con)->find();
        if (!empty($info) && !empty($_GET['id'])) {
            $list = M('school')->field('schoolId,schoolName')->select();
            $info = M('edmin_news')->where($con)->find();
            if ($info['school']) {
                $info['school'] = explode(',', $info['schoolid']);
            } else {
                $info['school'] = 0;
            }
            $tag = M('system_variables')->where("type='edmin_news_type'")->select();
            $title = array('title' => '系统公告编辑页', 'subtitle' => '编辑系统公告。');
            $this->assign('title', $title);
            $this->assign('info', $info);
            $this->assign('list', $list);
            $this->assign('tag', $tag);
            $this->display('Public/header');
            $this->display('system/News');
            $this->display('Public/footer');
        } else {
            alertToUrl(__CONTROLLER__ . 'newprelist', '查无此系统公告');
        }

    }

    //保存系统公告
    public function saveNew()
    {
        if (IS_POST && !empty($_POST)) {
            $data['title'] = $_POST['title'];
            $data['edminId'] = $_SESSION['edminInfo']['userId'];
            $data['content'] = $_POST['content'];
            $data['level'] = $_POST['level'];
            $data['time'] = $_POST['starttime'] ? strtotime($_POST['starttime']) : time();
            $data['endtime'] = $_POST['endtime'] ? strtotime(date('Y-m-d 23:59:59', strtotime($_POST['endtime']))) : 0;
            $data['tag'] = $_POST['tags'];
            $data['isDone'] = $_POST['isDone'] == 'on' ? 1 : 0;
            if ($_POST['school'][0] !== '1' || !empty($_POST['school'])) {
                $data['schoolId'] = implode(',', $_POST['school']);
            }
            $con['newId'] = $_POST['id'];
            $info = M('edmin_news')->where($con)->find();
            if (!empty($info) && !empty($_POST['id'])) {
                $con['newId'] = $_POST['id'];
                $flag = M('edmin_news')->where($con)->save($data);
            } else {
                $flag = M('edmin_news')->add($data);
            }
            if ($flag === 0 || $flag) {
                if ($data['isDone']) {
                    alertToUrl(__CONTROLLER__ . '/newlist', '发布成功');
                } else {
                    alertToUrl(__CONTROLLER__ . '/newsnotdone', '已经保存至草稿箱');
                }
            } else {
                alertToBack('网络错误,请稍后再试');
            }
        } else {
            alertToBack('保存失败');
        }


    }

    //公告列表页
    public function newlist()
    {
        //判断是否显示发布区域,只有超级管理员和管理员才能发布
        $tag = $_SESSION['edminInfo']['role'];
        if ($tag == 1 || $tag == 2) {
            $flag = 1;
        }
        $con['isDone'] = 1;
        if ($_GET['type']) {
            $con['tag'] = $_GET['type'];
        }
        if (!empty($_SESSION['edminInfo']['school'])) {
            $con['schoolId'] = array('like', '%' . $_SESSION['edminInfo']['school'] . '%');
        }
        $where = "endtime > " . time() . " or endtime =0";
        $order = 'level desc,time desc';
        if (!empty($_GET['order'])) {
            switch ($_GET['order']) {
                case 'outdate':
                    $where = "endtime <" . time() . ' and endtime!=0';//忘了怎么写数组形式。。。就这样吧。。。
                    break;
                case 'time':
                    $order = "time desc";
                    break;
                case 'level':
                    $order = "level desc";
                    break;
                case 'longterm':
                    $where = "endtime =0";
                    break;
            }
        }
        $type = M('system_variables')->where("type='edmin_news_type'")->select();
        $list = M('edmin_news')->join('system_variables on id = tag')->where($where)->where($con)->order($order)->select();
        $title = array('title' => '系统公告专区', 'subtitle' => '查看所有系统消息。', 'tag' => '0');
        $this->assign('title', $title);
        $this->assign('type', $type);
        $this->assign('flag', $flag);
        $this->assign('list', $list);
        $this->display('Public/header');
        $this->display();
        $this->display('Public/footer');
    }

    //系统公告草稿箱
    public function newsnotdone()
    {
        //判断是否显示发布区域,只有超级管理员和管理员才能发布
        $tag = $_SESSION['edminInfo']['role'];
        if ($tag == 1 || $tag == 2) {
            $flag = 1;
        }
        $con['isDone'] = 0;
        $con['edminId'] = $_SESSION['edminInfo']['userId'];//只有本人保存的草稿才能看到
        if ($_GET['type']) {
            $con['tag'] = $_GET['type'];
        }
        if (!empty($_SESSION['edminInfo']['school'])) {
            $con['schoolId'] = array('like', '%' . $_SESSION['edminInfo']['school'] . '%');
        }
        $where = "endtime > " . time() . " or endtime =0";
        $order = 'level desc,time desc';
        if (!empty($_GET['order'])) {
            switch ($_GET['order']) {
                case 'outdate':
                    $where = "endtime <" . time() . ' and endtime!=0';
                    break;
                case 'time':
                    $order = "time desc";
                    break;
                case 'level':
                    $order = "level desc";
                    break;
                case 'longterm':
                    $where = "endtime =0";
                    break;
            }
        }
        $type = M('system_variables')->where("type='edmin_news_type'")->select();
        $list = M('edmin_news')->join('system_variables on id = tag')->where($where)->where($con)->order($order)->select();
        $title = array('title' => '系统公告草稿箱', 'subtitle' => '点击进入相应的系统公告进行再次编辑', 'tag' => '1');
        $this->assign('title', $title);
        $this->assign('type', $type);
        $this->assign('flag', $flag);
        $this->assign('list', $list);
        $this->display('Public/header');
        $this->display('system/newlist');
        $this->display('Public/footer');
    }
}
