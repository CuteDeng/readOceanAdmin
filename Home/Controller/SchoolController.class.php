<?php
/**
 * Created by PhpStorm.
 * User: Chan
 * Date: 16/12/12
 * Time: 下午3:48
 */

namespace Home\Controller;

use Think\Controller;
use Think\Page;
use Component\CommonController;

class SchoolController extends CommonController
{
    protected $schoolId;//学校id
    protected $gradeId;//学校年级id

//    //控制学校管理员权限
    public function __construct()
    {
        parent::__construct();
        if (!empty($_SESSION['edminInfo']['school'])) {
            $this->schoolId = $_SESSION['edminInfo']['school'];
            if (!empty($_SESSION['edminInfo']['grade'])) {
                $this->gradeId = $_SESSION['edminInfo']['grade'];
            }
        }

    }

    //学校管理员无权进入此方法
    public function school()
    {
        if (empty($_SESSION['edminInfo']['school'])) {
            if (!empty($_GET['schoolName'])) {
                $con['schoolName'] = array('like', '%' . $_GET['schoolName'] . '%');
            }
            $list = M('school')->where($con)->select();
            for ($i = 0; $i < count($list); $i++) {
                $list[$i]['city'] = getCity($list[$i]['provinceid'], $list[$i]['cityid']);
            }
            $this->assign('list', $list);
            $this->display('public/header');
            $this->display();
            $this->display('public/footer');
        } else {
            alertToBack('抱歉,您无权进入此功能');
        }

    }

    //年级列表页
    public function grade()
    {
        $num = 25;
        if (empty($_GET['p'])) {
            $_GET['p'] = 1;
        }
        if (!empty($_GET['schoolname'])) {
            $con['schoolName'] = array('like', '%' . $_GET['schoolname'] . '%');
        }
        if (!empty($this->schoolId) || $_GET['school']) {
            $con['grade.schoolId'] = !empty($this->schoolId) ? $this->schoolId : $_GET['school'];
        }
        if (!empty($this->gradeId) || !empty($_GET['gid'])) {
            $con['gradeId'] = empty($this->gradeId) ? $_GET['gid'] : $this->gradeId;
        }
        $list = M('grade')->join('school on school.schoolId = grade.schoolId')->where($con)->field('gradeid,gradename,schoolname')->limit($num)->page($_GET['p'], $num)->select();
        for ($i = 0; $i < count($list); $i++) {
            $list[$i]['p'] = (intval($_GET['p']) - 1) * $num;
        }
        $this->assign('list', $list);
        //分页
        $count = M('grade')->join('school on school.schoolId = grade.schoolId')->where($con)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, $num);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);// 赋值分页输出
        $this->display('public/header');
        $this->display();
        $this->display('public/footer');
    }

    //班级信息
    public function klass()
    {
        $num = 25;
        if (IS_POST) {
            $con['schoolId'] = '10000000';
        }
        if (empty($_GET['p'])) {
            $_GET['p'] = 1;
        }
        if (!empty($_GET['school'])) {
            $con['schoolName'] = array('like', '%' . $_GET['school'] . '%');
        }
        if (!empty($this->schoolId)) {
            $con['grade.schoolId'] = $this->schoolId;
        }
        if (!empty($this->gradeId) || !empty($_GET['gid'])) {
            $con['classes.gradeId'] = empty($this->gradeId) ? $_GET['gid'] : $this->gradeId;
        }

        $list = M('classes')->join('grade on grade.gradeId = classes.gradeId')->join("school on school.schoolId=grade.schoolId")->where($con)->where('className is not null')->field('classId,gradename,schoolname,classname')->limit($num)->page($_GET['p'], $num)->select();
        for ($i = 0; $i < count($list); $i++) {
            $list[$i]['p'] = (intval($_GET['p']) - 1) * $num;
        }
        if ($_SESSION['edminInfo']['role'] == 3) {
            $grade = M('grade')->where('schoolId=' . $_SESSION['edminInfo']['school'])->select();
            $this->assign('grade', $grade);
        }
        $this->assign('list', $list);
        //分页
        $count =  M('classes')->join('grade on grade.gradeId = classes.gradeId')->join("school on school.schoolId=grade.schoolId")->where($con)->where('className is not null')->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, $num);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);// 赋值分页输出
        $this->display('public/header');
        $this->display();
        $this->display('public/footer');
    }

    //学生展示列表
    public function stu()
    {
        $num = 25;
        if (empty($_GET['p'])) {
            $_GET['p'] = 1;
        }
        if (!empty($_GET['school'])) {
            $con['schoolName'] = array('like', '%' . $_GET['school'] . '%');
        }
        if (!empty($_GET['name'])) {
            $con['name'] = array('like', '%' . $_GET['name'] . '%');
        }
        if (!empty($this->schoolId)) {
            $con['user.schoolId'] = $this->schoolId;
        }
        if (!empty($this->gradeId)) {
            $con['classes.gradeId'] = $this->gradeId;
        }
        $con['type'] = 'user_type_student';
        $data = M('user')->join('classes on classes.classId = user.classId')
        ->join(' school on school.schoolId=user.schoolId')->where($con);
        $list = $data->order('user.schoolId')->field('userId,name,user.classId,schoolName,user.status,lastTime,user.type')
        ->page($_GET['p'], $num)->select();

        for ($i = 0; $i < count($list); $i++) {
            $list[$i]['p'] = (intval($_GET['p']) - 1) * $num;
            $list[$i]['classname'] = getStuClass($list[$i]["classid"]);
        }
        // dump($list);
        // die;
        $this->assign('list', $list);// 赋值数据集
        //分页
        $count = M('user')->join('classes on classes.classId = user.classId')
        ->join(' school on school.schoolId=user.schoolId')->where($con)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, $num);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);// 赋值分页输出
        $this->display('public/header');
        $this->display();
        $this->display('public/footer');
    }

    //禁用教师或学生用户
    public function closeuser()
    {
        if (empty($_GET['p'])) {
            $_GET['p'] = 1;
        }
        if (empty($_GET['type'])) {
            $_GET['type'] = 0;
        }
        $con['userId'] = $_GET['id'];
        $reset['status'] = 0;
        if (M('user')->where($con)->save($reset)) {
            if($_GET['usertype'] == 'user_type_student')
                alertToUrl(__CONTROLLER__ . '/stu' . getRightUrl(), '用户已禁用');
            if($_GET['usertype'] == 'user_type_teacher')
                alertToUrl(__CONTROLLER__ . '/teacher' . getRightUrl(), '用户已禁用');
        } else {
            alertToBack('网络错误');
        }
    }

    //启用教师或学生用户
    public function openuser()
    {
        if (empty($_GET['p'])) {
            $_GET['p'] = 1;
        }
        if (empty($_GET['type'])) {
            $_GET['type'] = 0;
        }
        $con['userId'] = $_GET['id'];
        $reset['status'] = 1;
        if (M('user')->where($con)->save($reset)) {
            if($_GET['usertype'] == 'user_type_student')
                alertToUrl(__CONTROLLER__ . '/stu' . getRightUrl(), '用户已启用');
            if($_GET['usertype'] == 'user_type_teacher')
                alertToUrl(__CONTROLLER__ . '/teacher' . getRightUrl(), '用户已启用');
        } else {
            alertToBack('网络错误');
        }
    }

    //教师展示页面
    public function teacher()
    {
        $num = 25;
        if (empty($_GET['p'])) {
            $_GET['p'] = 1;
        }
        if (!empty($_GET)) {
            if (!empty($_GET['school'])) {
                $con['schoolName'] = array('like', '%' . $_GET['school'] . '%');
            }
            if (!empty($_GET['name'])) {
                $con['name'] = array('like', '%' . $_GET['name'] . '%');
            }
            if (!empty($_GET['schoolid'])) {
                $con['user.schoolId'] = $_GET['schoolid'];
            }
        }
        if (!empty($this->schoolId)) {
            $con['user.schoolId'] = $this->schoolId;
        }
        //处理年级管理员管理教师问题
        if (!empty($this->gradeId)) {
            //先筛选出对应年级下的班级
            $klass['classes.gradeId'] = $this->gradeId;
            $classes = M('classes')->where($klass)->select();
            $arr = [];
            for ($i = 0; $i < count($classes); $i++) {
                array_push($arr, $classes[$i]['classid']);
            }
            $ccons['teacher_class_relationship.classId'] = array('in', $arr);//查找相应的班级的老师
            $teacher = M('teacher_class_relationship')->where($ccons)->select();
            if (!empty($teacher)) {
                //在筛选对应班级下的老师
                $arr = [];
                for ($i = 0; $i < count($teacher); $i++) {
                    array_push($arr, $teacher[$i]['teacherid']);
                }
                $con['userId'] = array('in', $arr);
            }
        }
        $con['type'] = 'user_type_teacher';
        $list = M('user')->join('school on school.schoolId=user.schoolId')->where($con)
        ->field('userId,name,user.classId,schoolName,user.status,user.type')->limit($num)->page($_GET['p'], $num)->select();
        for ($i = 0; $i < count($list); $i++) {
            $list[$i]['p'] = (intval($_GET['p']) - 1) * $num;
            $list[$i]['classname'] = getTeachClass($list[$i]["userid"]);
        }
        $this->assign('list', $list);// 赋值数据集
        // dump($list);
        // die;
        //分页
        $count = M('user')->join('school on school.schoolId=user.schoolId')->where($con)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, $num);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);// 赋值分页输出
        $this->display('public/header');
        $this->display();
        $this->display('public/footer');
    }

    //寻找城市
    public function findCity()
    {
        if (!empty($_GET['fid'])) {
            $res = M('city')->where('provinceId=%d', $_GET['fid'])->select();
        }
        $str = "<select style='margin-top: 5px;' class='form-control col-sm-5' id='level2' name='city' required>";
        if (!empty($res)) {
            $str .= "<option value=''> 请选择</option>";
            foreach ($res as $v) {
                $str .= "<option value='" . $v['cityid'] . "'> " . $v['cityname'] . "</option>";
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

    //编辑年级静态页
    public function editgrade()
    {
        $list = M('classes')->join('grade on grade.gradeId = classes.gradeId')->join("school on school.schoolId=grade.schoolId")->where("classes.gradeId=" . $_GET['id'])->field('className,gradeName,classId,schoolName')->select();
        $this->assign('list', $list);
        $this->display('public/header');
        $this->display();
        $this->display('public/footer');
    }

    //保存班级名称
    public function saveclassname()
    {
        $model = new \Think\Model();
        $model->startTrans();
        $tag1 = 0;
        $tag = 0;
        if (IS_POST && !empty($_POST)) {
            for ($i = 0; $i < count($_POST['classid']); $i++) {
                $con['classId'] = $_POST['classid'][$i];
                $con['className'] = $_POST['classname'][$i];
                if (M('classes')->save($con) || M('classes')->save($con) === 0) {
                    $tag1 += 1;
                }
            }
            $length_id = count($_POST['classid']);
            $length_classname = count($_POST['classname']);
            $tag = $length_classname;//因为名字长度最长,且一定等于数据传输量

            if ($length_id < $length_classname) {
                for ($i = $length_classname - 1; $i >= $length_id; $i--) {
                    $add = [];
                    if (!empty($this->gradeId) || !empty($_GET['gid'])) {
                        $add['gradeId'] = !empty($this->gradeId) ? $this->gradeId : $_GET['gid'];
                    }
                    $klass['classId'] = getUid();
                    $add['className'] = $_POST['classname'][$i];
                    while (1) {
                        $flag = M('classes')->where($klass)->select();
                        if ($flag) {
                            $klass['classId'] = getUid();
                        } else {
                            $add['classId'] = $klass['classId'];
                            break;
                        }
                    }
                    if (M('classes')->add($add)) {
                        $tag1 += 1;
                    }
                }
            }
        }
        if ($tag1 != $tag) {
            $model->rollback();
            alertToBack('更新失败');
        } else {
            $model->commit();
            alertToUrl(U('school/grade'), '更新成功');
        }
    }

    //添加学校及基础设置
    public function addSchool()
    {
        if (IS_POST && !empty($_POST)) {
            $model = new \Think\Model();
            $model->startTrans();
            $tag = false;//事务标签
            $school['provinceId'] = $_POST['province'];
            $school['cityId'] = $_POST['city'];
            $school['schoolId'] = getSchoolNumber($school['provinceId'], $school['cityId']);
            $school['schoolName'] = $_POST['SchoolName'];
            if (M('school')->add($school)) {//学校的添加
                for ($i = 0; $i < $_POST['gradeNum']; $i++) {
                    $grade['schoolId'] = $school['schoolId'];
                    $grade['gradeName'] = $_POST['gradename'][$i];
                    $grade['gradeId'] = getGradeId($school['schoolId'], $_POST['grade'][$i]);
                    $arrnum = $_POST['classNum'];
                    if (M('grade')->add($grade)) {
                        for ($j = 0; $j < $arrnum[$i]; $j++) {
                            $klass['classId'] = getUid();
                            while (1) {
                                $flag = M('classes')->where($klass)->select();
                                if ($flag) {
                                    $klass['classId'] = getUid();
                                } else {
                                    break;
                                }
                            }
                            $klass['gradeId'] = $grade['gradeId'];
                            if (M('classes')->add($klass)) {
                                $tag = true;
                            }
                        }
                    }

                }
            }
            if (!$tag) {
                $model->rollback();
            } else {
                $model->commit();
                WriteLog($school['schoolId']);
                alertToUrl('school', '学校添加成功');
            }
        }
        $province = M('province')->select();
        $this->assign('type', $province);//渲染省份
        $this->display('public/header');
        $this->display();
        $this->display('public/footer');
    }

    //添加班级
    public function addclass()
    {
        $info = getStuClass($_GET['id'], 1);
        $this->assign('info', $info);
        $this->display('public/header');
        $this->display();
        $this->display('public/footer');
    }

    //添加学生
    public function addstu()
    {
        if (!empty($_GET['id'])) {
            $klassid = $_GET['id'];
            $info = getStuClass($_GET['id'], 1);
            $this->assign('info', $info);
        } else {
            alertToUrl(__CONTROLLER__ . '/klass', "页面出错");
            exit;
        }
        if (!empty($_POST)) {
            $data['name'] = $_POST['username'];
            $data['idcard'] = $_POST['idcard'];
            $data['classId'] = $_GET['id'];
            $info = getStuClass($_POST['id'], 1);
            $data['schoolId'] = $info['schoolid'];
            switch ($_POST['sex']) {
                case '1':
                    $data['sex'] = '男';
                    break;
                case '2':
                    $data['sex'] = '女';
                    break;
            }
            $klassid = $_GET['id'];
            if (D('user')->adduser($data)) {
                $userid = M('user')->where($data)->getField('userId');
                WriteLog($userid);//记录日志
                alertToUrl(__CONTROLLER__ . '/classmate/id/' . $klassid, "添加成功");
            } else {
                alertToUrl(__CONTROLLER__ . '/classmate/id/' . $klassid, "添加失败");
            }
        }
        $this->display('public/header');
        $this->display();
        $this->display('public/footer');
    }

    //添加老师
    public function addteachers()
    {
        if (!empty($_POST)) {
            $data['name'] = $_POST['username'];
            $data['idcard'] = $_POST['idcard'];
            $data['schoolId'] = $_POST['school'];
            switch ($_POST['sex']) {
                case '1':
                    $data['sex'] = '男';
                    break;
                case '2':
                    $data['sex'] = '女';
                    break;
            }
            if (D('user')->adduser($data, 'user_type_teacher')) {
                $userid = M('user')->where($data)->order('enrollTime desc')->getField('userId');
                WriteLog($userid);
                alertToBack('添加成功');
            } else {
                alertToBack('添加失败');
            }
        }
        $this->display('public/header');
        $this->display();
        $this->display('public/footer');
    }

    //同班同学
    public function classmate()
    {
        if ($_GET['id']) {
            $con['classId'] = $_GET['id'];
            $num = 25;
            if (empty($_GET['p'])) {
                $_GET['p'] = 1;
            }
            if (!empty($_GET['user'])) {
                $con['name'] = array('like', '%' . $_GET['user'] . '%');
            }
            $list = M('user')->where($con)->field('userId,name,sex,status,idcard,classId')->limit($num)->page($_GET['p'], $num)->select();

            for ($i = 0; $i < count($list); $i++) {
                $list[$i]['p'] = (intval($_GET['p']) - 1) * $num;
            }
            $this->assign('list', $list);// 赋值数据集
            //分页
            $count = M('user')->where($con)->count();// 查询满足要求的总记录数
            $Page = new \Think\Page($count, $num);// 实例化分页类 传入总记录数和每页显示的记录数
            $show = $Page->show();// 分页显示输出
            $this->assign('page', $show);// 赋值分页输出

            $this->display('public/header');
            $this->display();
            $this->display('public/footer');
        } else {
            alertToUrl(U('school/klass'), '页面出错');
        }

    }

    //每个学年升级学校
    public function upgrade()
    {
        //先判断学校更新是否可以升级
        $school['schoolId'] = $_GET['school'];

        $lasttime = M('school')->where($school)->getField('updatetime');//获取升级时间

        $time = date('M', time());//获取当前时间月份

        $res = array('tag' => '0', 'msg' => '网络错误。');//json返回数组

        if (date('Y', $lasttime) == date('Y', time())) {
            $res = array('tag' => '2', 'msg' => '学校本年度完成升级操作,请勿重复操作。');
        } elseif ($time < 7 || $time > 9) {
            $res = array('tag' => '3', 'msg' => '升学操作有效时间为每年的 7-9 月份,请勿在规定以外时间段进行升学操作。');
        } else {
            $model = new \Think\Model();
            $model->startTrans();
            //1.首先判断这个学校的最高年级数
            $arr = M('grade')->where($school)->select();
            $grade['gradeId'] = M('grade')->where($school)->getField('max(gradeId)');//这个学校最高的年级
            $arrClass = [];//年级群
            foreach ($arr as $key) {
                array_push($arrClass, $key['gradeid']);//集合成区间查询条件
            }

            //2.获取这个学校总共有多少个班级
            $grade_class['gradeId'] = array('in', $arrClass);
            $flag = M('classes')->where($grade_class)->count();//作为判断事务是否成功的标志位

            //3.将最高年级的毕业时间
            $graduate['graduatetime'] = date('Y', time());//毕业年份,默认为目前更新的年份
            $graduate['gradeId'] = $school['schoolId'] . "0";//更新年级id为学校id+0
            $flag_graduate = M('classes')->where($grade)->save($graduate);//修改毕业班信息,返回修改数量

            //4.将剩余年级的班级的年级id全部加1
            $flag_class = M('classes')->where($grade_class)->setInc('gradeId', '1');

            //5.修改学校的更新时间
            $flag_school = M('school')->where($school)->setField('updatetime', time());

            //最后判断是否可以提交或者回滚事件
            if ($flag + 1 == $flag_graduate + $flag_class + $flag_school) {
                WriteLog($school['schoolId']);
                $model->commit();
                $res = array('tag' => '1', 'msg' => '学校升学成功。');
            } else {
                $model->rollback();
            }
        }
        $this->ajaxReturn($res);
    }

    //重置密码
    public function reset()
    {
        $con['userId'] = $_GET['id'];
        $reset['password'] = sha1(md5('123123'));//默认密码
        $flag = M('user')->where($con)->save($reset);
        if ($flag || $flag === 0) {
            alertToBack('重置密码成功,密码重置为默认密码。');
        } else {
            alertToBack('网络错误');
        }
    }

    //学生信息修改
    public function userdetail()
    {
        $con['userId'] = $_GET['id'];
        $info = M('user')->join('school on school.schoolId = user.schoolId')->where($con)->select();
        $info[0]['gradeid'] = M('classes')->where('classId=\'' . $info[0]['classid'] . '\'')->getField('gradeId');
        $grade = M('grade')->where('schoolId = ' . $info[0]['schoolid'])->select();
        $class = M('classes')->where('gradeId=\'' . $info[0]['gradeid'] . '\'')->select();
        if (empty($info)) {
            alertToBack('查无此人');
        } else {
            $this->assign('info', $info[0]);
            $this->assign('grade', $grade);
            $this->assign('class', $class);
//            $this->assign('title', $title);
            $this->display('public/header');
            $this->display();
            $this->display('public/footer');
        }
    }

    //教师信息修改
    public function teacherdetail()
    {
        $con['userId'] = $_GET['id'];
        $info = M('user')->join('school on school.schoolId = user.schoolId')->where($con)->field('userid,name,sex,schoolname,type,idcard,user.schoolId')->select();
        $grade = M('grade')->where('schoolId =' . $info[0]['schoolid'])->select();
        $klass = M('teacher_class_relationship')->where('teacherId=\'' . $_GET['id'] . '\'')->field('classid')->select();
        $klassto1 = [];//用来解除两层数组
        for ($i = 0; $i < count($klass); $i++) {
            array_push($klassto1, $klass[$i]['classid']);
        }
        $grades = '';
        for ($i = 0; $i < count($grade); $i++) {
            $grades .= $grade[$i]['gradeid'] . ',';
        }
        $conn['gradeId'] = array('in', $grades);
        $classes = M('classes')->where($conn)->field('gradeId,classId,classname')->order('gradeid')->select();
        if (empty($info)) {
            alertToBack('查无此人');
        } else {
            $this->assign('info', $info[0]);
            $this->assign('classes', $classes);
            $this->assign('grade', $grade);
            $this->assign('klass', $klassto1);
            $this->display('public/header');
            $this->display();
            $this->display('public/footer');
        }
    }

    //修改用户信息
    public function editinfo()
    {
        $new['idcard'] = $_POST['idcard'];
        $new['classId'] = $_POST['classes'];
        $con['userId'] = $_GET['id'];
        if (M('user')->where($con)->save($new)) {
            alertToUrl(__CONTROLLER__ . '/userdetail/id/' . $con['userId'], '修改成功');
        } else {
            alertToBack('网路错误');
        }
    }

    //修改教师信息
    public function editteacher()
    {
        if (empty($_GET['id'])) {
            alertToBack('修改失败');
        } else {
            $now = $_POST['class'];
            $arr = D('teaching')->getTeachingClass($_GET['id']);
            $con['teacherId'] = $_GET['id'];
            $model = new \Think\Model();
            $model->startTrans();
            $tag1 = false;//判断现在的标签
            $tag2 = true;//判断现在的标签
            //判断现在的是不是在以前里面,如果没有,要增加
            for ($i = 0; $i < count($now); $i++) {
                if (!in_array($now[$i], $arr)) {
                    $con['classId'] = $now[$i];
                    if (M('teacher_class_relationship')->add($con)) {
                        $tag1 = true;
                    } else {
                        $tag1 = false;
                        break;
                    }
                } else {
                    $tag1 = true;
                }
            }
            //判断以前的是不是在现在里面,如果没有,要删除
            for ($j = 0; $j < count($arr); $j++) {
                if (!in_array($arr[$j], $now)) {
                    $con['classId'] = $arr[$j];
                    if (M('teacher_class_relationship')->where($con)->delete()) {
                        $tag2 = true;
                    } else {
                        $tag2 = false;
                        break;
                    }
                } else {
                    $tag2 = true;
                }
            }
            $cons['userId'] = $con['teacherId'];
            $reset['idcard'] = $_POST['idcard'];
            M('user')->where($cons)->save($reset);

            if ($tag1 && $tag2) {
                $model->commit();
                alertToUrl(__CONTROLLER__ . '/teacherdetail/id/' . $_GET['id'], '老师信息修改成功');
            } else {
                $model->rollback();
                alertToBack('老师信息修改失败');
            }
        }

    }

    //获取年级班级
    public function getGradeClass()
    {
        $con['gradeId'] = $_POST['grade'];
        $klass = M('classes')->where($con)->order('classname')->select();
        $this->ajaxReturn($klass);
    }

    //增加老师页面渲染
    public function addteacher()
    {
        if (!empty($this->schoolId)) {
            $con['schoolId'] = $this->schoolId;
        }
        $schoollist = M('school')->where($con)->field('schoolId,schoolname')->select();
        $this->assign('school', $schoollist);
        $this->display('public/header');
        $this->display();
        $this->display('public/footer');
    }

    //学校信息页
    public function schoolinfo()
    {
        if (IS_GET) {
            $school['schoolId'] = $_GET['school'];
            $info = M('school')->where($school)->find();
            if (!empty($info['paytime'])) {
                $info['paytime'] = date('Y-m-d', $info['paytime']);
                $date = date_create($info['paytime']);//创建一个时间对象
                $dates = date_add($date, date_interval_create_from_date_string("1 year"));
                $dates = objectToArray($dates);
                if (time() > strtotime($dates['date'])) {
                    $info['outdate'] = 1;
                }
            }
//            //当他没有升级时间,或者升级时间满足条件
            if (empty($info['updatetime']) || date("Y", $info['updatetime']) < date("Y", time())) {
                $time = date("M", time());
                if ($time > 7 && $time < 9) {
                    $info['updatestatus'] = 1;//表示可以升级
                }
            }
            $flag = M('user')->where($school)->where('status=2')->limit(1)->select();
            $info['graduate'] = $flag ? '1' : '0';//表示是否有毕业生被禁用,0表示没有,1表示有

            $grademate = M('classes')->join('user on classes.classId = user.classId')->where('gradeId like \'' . $school['schoolId'] . "_' and gradeId>" . $school['schoolId'] . "0")->field('gradeId,count(*) as number')->group('gradeId')->select();

            $this->assign("grade", $grademate);
            $this->assign("info", $info);
            $this->display('public/header');
            $this->display();
            $this->display('public/footer');
        } else {
            alertToUrl(__CONTROLLER__ . '/school', '传递参数有问题');
        }
    }

    //禁用学校已经毕业用户
    public function closegraduate()
    {
        $res = array('res' => '0', 'msg' => '网络错误');
        if (IS_POST) {
            $class['gradeId']=$_POST['sid'].'0';//说明是毕业生
            $stulist=M('user')->join('classes on user.classId=classes.classId')->where($class)->select();
            $userlist = [];//用来解除两层数组
            for ($i = 0; $i < count($stulist); $i++) {
                array_push($userlist, $stulist[$i]['userid']);
            }
            if(!empty($userlist)){
                $school['userId'] = array('in', $userlist);
                $school['schoolId'] = $_POST['sid'];
                $data['status'] = 2;
                if (M('user')->where($school)->where('status=1')->save($data)) {
                    $res = array('res' => '1', 'msg' => '修改成功。');
                    WriteLog($school['schoolId']);
                }
            }else{
                $res = array('res' => '0', 'msg' => '该校暂无毕业生。');
            }
        }
        $this->ajaxReturn($res, JSON);

    }

    //启用学校已经毕业用户
    public function opengraduate()
    {
        $res = array('res' => '0', 'msg' => '网络错误');
        if (IS_POST) {
            $school['schoolId'] = $_POST['sid'];
            $data['status'] = 1;
            if (M('user')->where($school)->where('status=2')->save($data)) {
                $res = array('res' => '1', 'msg' => '修改成功。');
                WriteLog($school['schoolId']);
            }
        }
        $this->ajaxReturn($res, JSON);

    }

    //管理学校信息
    public function schoolInformation()
    {
        $res = array('res' => '0', 'msg' => '网络错误');
        if (IS_POST) {
            if (!empty($_POST['sid'])) {
                $school['schoolId'] = $_POST['sid'];
                M('school')->where($school)->find();
                //可用的用户数
                $online = M('user')->where('status=1 or status=3')->where($school)->count();
                //所有用户数
                $all = M('user')->where($school)->count();
                //毕业生人数
                $graduate = M('user')->join('classes on classes.classId = user.classId')->where('gradeId=' . $school['schoolId'] . "0")->count();
                $res = array('res' => '1', 'msg' => '获取数据成功', 'online' => $online, "all" => $all, 'graduate' => $graduate);
            }
        }
        $this->ajaxReturn($res, "JSON");
    }

    //获取该学校各个年级的学生人数
    public function getSchoolMate()
    {
//        header('Content-Type:application/json; charset=utf-8');
        $res = array('res' => '0', 'msg' => '网络错误');
        if (!empty($_POST['sid'])) {
            $school['schoolId'] = $_POST['sid'];
            $grademate = M('classes')->join('user on classes.classId = user.classId')->join('grade on grade.gradeId= classes.gradeId')->where('classes.gradeId like \'' . $school['schoolId'] . "_' and classes.gradeId>" . $school['schoolId'] . "0 and user.status =1")->field('classes.gradeId,gradeName,count(*) as number')->group('gradeId')->select();
            if (!empty($grademate)) {
                $res = array('res' => '1', 'msg' => '获取数据成功');
                foreach ($grademate as $key => $value) {
                    $grademate[$key]['gradename'] = urlencode($value['gradename']);//只针对中文的部分进行修改
                }
                $res['data'] = urldecode(json_encode($grademate));
                $res['data'] = str_replace('"', "'", $res['data']);
            } else {
                $res = array('res' => '2', 'msg' => '该学校暂无相关学生');
            }

        }

        $this->ajaxReturn($res, "JSON");
    }

    //更改用户学校状态(未完成)
    public function changesSchool()
    {

    }

    //批量上传教师静态页
    public function schoolteacher(){
        $info= M('school')->where('schoolId='.$_GET['id'])->find();
        $this->assign('info',$info);
        $this->display('public/header');
        $this->display();
        $this->display('public/footer');
    }
}













