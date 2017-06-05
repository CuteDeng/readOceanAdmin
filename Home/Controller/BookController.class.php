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

class BookController extends CommonController
{
    public function booklist()
    {
        $type = M('system_variables')->where("type='book_type'")->select();
        $suit = M('system_variables')->where("type='suit_grade'")->select();
        $num = 16;
        if (empty($_GET['p'])) {
            $_GET['p'] = 1;
        }
        if (!empty($_GET['type'])) {
            $con['categoryId'] = $_GET['type'];
        }
        if (!empty($_GET['suit'])) {
            $con['suit'] = $_GET['suit'];
        }
        if(!empty($_GET['name'])){
                $con['name'] = array('like', '%' . $_GET['name'] . '%');  
                $this->assign('sname',$_GET['name']);
        }
        $list = M('books')->where($con)->field('books.name,books.id,picUrl')->limit($num)->page($_GET['p'], $num)->select();
        $this->assign('list', $list);
        //分页
        $count = M('books')->where($con)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, $num);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('type', $type);
        $this->assign('suits', $suit);
        $this->display('public/header');
        $this->display();
        $this->display('public/footer');
    }

    //书本信息
    public function editbook()
    {
        $con['books.id'] = $_GET['id'];
        $info = M('books')->join('system_variables on system_variables.id=categoryId')->where($con)->field('books.id,books.name,system_variables.name as typename,recommend,introduction,pages,publicationdate,guideurl,picurl,author,publishinghouse,suit')->find();
        $type = M('system_variables')->where("type='book_type'")->select();
        $this->assign('type', $type);
        switch ($info['suit']) {
            case '1-2':
                $info['suits'] = 1;
                break;
            case '3-4':
                $info['suits'] = 2;
                break;
            case '5-6':
                $info['suits'] = 3;
                break;
        }
        $this->assign('info', $info);
        $this->display('Public/header');
        $this->display();
        $this->display('Public/footer');
    }

    //添加书本
    public function addbook()
    {
        $type = M('system_variables')->where("type='book_type'")->select();
        $this->assign('type', $type);
        $this->display('Public/header');
        $this->display();
        $this->display('Public/footer');
    }

    //保存图书
    public function savebook()
    {
//        header("Content-Type:text/html;charset=utf-8");
//        $upload = new \Think\Upload();// 实例化上传类
//        $upload->maxSize = 3145728;// 设置附件上传大小
//        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
//        $upload->rootPath = UPLOAD_BOOKIMG_URL . 'category_xiaoshuo/'; // 设置附件上传根目录
//        $upload->savePath = ''; // 设置附件上传（子）目录
//        // 上传文件
//        $info = $upload->upload();
//        $imgSrc = $info['imgsrc'];
//        echo $imgSrc['savepath'] . $imgSrc['savename'];
//        if (!$info) {// 上传错误提示错误信息
//            $this->error($upload->getError());
//        } else {// 上传成功
//            echo $info['savepath'] . $info['savename'];
//        }
        dump($_POST);
        dump($_FILES);
    }

    //书本题库
    public function test()
    {
        $type = M('system_variables')->where("type='book_type'")->select();
        $title = array('title' => '题库', 'subtitle' => '选择对应书籍添加书本相应题库', 'url' => 'addquestion');
        $num = 25;
        if (empty($_GET['p'])) {
            $_GET['p'] = 1;
        }
        if (!empty($_GET['id'])) {
            $con['id'] = $_GET['id'];
        }
        if (!empty($_GET['bookname'])) {
            $con['books.name'] = array('like', '%' . $_GET['bookname'] . '%');
        }
        if (IS_GET && !empty($_GET['type'])) {
            $con['categoryId'] = $_GET['type'];
            $list = M('books')->join('system_variables on categoryId = system_variables.id ')->where($con)->field('books.name,books.id,picUrl')->limit($num)->page($_GET['p'], $num)->select();
        }
        if (empty($_GET['type'])) {
            $list = M('books')->where($con)->field('name,id,picUrl')->limit($num)->page($_GET['p'], $num)->select();
        }
        for ($i = 0; $i < count($list); $i++) {
            $list[$i]['single'] = M('single_choice_questions')->where('bookId=\'' . $list[$i]['id'] . '\'')->count();
            $list[$i]['multiple'] = M('multiple_choice_questions')->where('bookId=\'' . $list[$i]['id'] . '\'')->count();
            $list[$i]['judge'] = M('true_or_false_questions')->where('bookId=\'' . $list[$i]['id'] . '\'')->count();
            $list[$i]['p'] = (intval($_GET['p']) - 1) * $num;
        }
        $this->assign('list', $list);
        $this->assign('title', $title);
        //分页
        $count = M('books')->where($con)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, $num);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('type', $type);// 赋值分页输出
        $this->display('Public/header');
        $this->display('Book/base');
        $this->display('Public/footer');
    }

    //添加书本题库
    public function addquestion()
    {
        $con['id'] = $_GET['id'];
        $info = M('books')->where($con)->field('name,id')->find();
        $this->assign('info', $info);
        $this->display('Public/header');
        $this->display();
        $this->display('Public/footer');
    }

    //书本任务,看到对应的学校的老师和学生所发布的任务
    public function tasks()
    {
        $type = M('system_variables')->where("type='book_type'")->select();
        $title = array('title' => '任务', 'subtitle' => '查看教师或个人所发布任务');
        $num = 10;
        $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
        if (empty($_GET['p'])) {
            $_GET['p'] = 1;
        }
        if (!empty($_SESSION['edminInfo']['school'])) {
            $con['schoolId'] = $_SESSION['edminInfo']['school'];
            $teacherlist = $Model->query("SELECT * FROM task join user on userId = teacherId where TO_DAYS(NOW()) < TO_DAYS(task.endDate) and schoolId=" . $con['schoolId'] . " order by task.endDate asc");
            $studentlist = M('student_book_subscription_relationship')->join('user on userId = studentId')->join('books on bookId = student_book_subscription_relationship.bookId')->where($con)->limit(10)->field('studentId,bookId,isdone,books.name as bookname,user.name,createTime,updateTime')->select();
        } else {
            $teacherlist = $Model->query(" SELECT taskId,user.name,taskTitle,taskDescribe,startDate,endDate,publishDate FROM task join user on userId = teacherId WHERE TO_DAYS(NOW()) < TO_DAYS(task.endDate) order by task.endDate asc;");
            $studentlist = M('student_book_subscription_relationship')->join('user on userId = studentId')->join('books on books.id = bookId')->limit(10)->order('createtime desc')->field('studentId,bookId,isdone,books.name as bookname,user.name,createTime,assessTime,user.picUrl,assess')->select();
        }
        $this->assign('teacherlist', $teacherlist);
        $this->assign('studentlist', $studentlist);
        $this->assign('title', $title);
        //分页
        $count = M('books')->where($con)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, $num);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('type', $type);// 赋值分页输出
        $this->display('Public/header');
        $this->display('Book/booktask');
        $this->display('Public/footer');
    }

    public function taskdetail()
    {
        //获取教师任务以及对应书本
        $num = 30;
        $con['taskId'] = $_GET['id'];
        $info = M('task')->join('user on userId = teacherId')->where($con)->limit(1)->find();
        $bookinfo = M('task_book_relationship')->join('books on id = bookId')->where($con)->field('books.name as bookname,books.id  as bookid')->select();
        for ($i = 0; $i < count($bookinfo); $i++) {
            $info['bookname'] .= ' 《' . $bookinfo[$i]['bookname'] . '》, ';
        }
        $studentlist = M('task_student_relationship')->join('user on userId = studentId')->where($con)->field('name,isdone,time,assess,assesstime,userid as studentid,classid')->limit($num)->page($_GET['p'], $num)->select();

        $this->assign('info', $info);// 赋值分页输出
        $this->assign('bookinfo', $bookinfo);// 赋值分页输出
        $this->assign('studentlist', $studentlist);// 赋值分页输出

        $count = M('task_student_relationship')->join('user on userId = studentId')->where($con)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, $num);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);// 赋值分页输出
        $this->display('Public/header');
        $this->display();
        $this->display('Public/footer');
    }

    public function teachertask()
    {
        $num = 15;
        if (!empty($_SESSION['edminInfo']['school'])) {
            $con['schoolId'] = $_SESSION['edminInfo']['school'];
            $teacherlist = M('task')->join('user on userId = teacherId ')->where($con)->order('publishDate desc')->limit($num)->page($_GET['p'], $num)->field('taskId,user.name,taskTitle,taskDescribe,startDate,endDate,publishDate')->select();
        } else {
            $teacherlist = M('task')->join('user on userId = teacherId ')->order('publishDate desc')->limit($num)->page($_GET['p'], $num)->field('taskId,user.name,taskTitle,taskDescribe,startDate,endDate,publishDate')->select();
        }
        if (empty($_GET['p'])) {
            $_GET['p'] = 1;
        }
        $this->assign('teacherlist', $teacherlist);
        $count = M('task')->join('user on userId = teacherId ')->where($con)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, $num);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);// 赋值分页输出
        $this->display('Public/header');
        $this->display('book/teachertask');
        $this->display('Public/footer');
    }

    public function studentlst()
    {
        $num = 15;
        if (!empty($_SESSION['edminInfo']['school'])) {
            $con['schoolId'] = $_SESSION['edminInfo']['school'];
            $studentlist = M('student_book_subscription_relationship')->join('user on userId = studentId')->join('books on bookId = student_book_subscription_relationship.bookId')->where($con)->limit($num)->page($_GET['p'], $num)->field('studentId,bookId,isdone,books.name as bookname,user.name,createTime,updateTime')->select();
        } else {
            $studentlist = M('student_book_subscription_relationship')->join('user on userId = studentId')->join('books on books.id = bookId')->limit($num)->page($_GET['p'], $num)->order('createtime desc')->field('studentId,bookId,isdone,books.name as bookname,user.name,createTime,assessTime,user.picUrl,assess')->select();
        }
        if (empty($_GET['p'])) {
            $_GET['p'] = 1;
        }
        $this->assign('studentlist', $studentlist);
        $count = $studentlist = M('task_student_relationship')->join('user on userId = studentId')->where($con)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, $num);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);// 赋值分页输出
        $this->display('Public/header');
        $this->display('book/studentlst');
        $this->display('Public/footer');
    }
}