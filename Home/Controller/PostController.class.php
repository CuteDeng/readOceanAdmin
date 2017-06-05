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

class PostController extends CommonController
{
    //分答管理
    public function answer()
    {
        $num = 25;
        if (empty($_GET['p'])) {
            $_GET['p'] = 1;
        }
        //通过点赞数来排序
        $list = M('score_answer_question_answer')->join('score_answer_question on score_answer_question.questionId = score_answer_question_answer.questionId')->group('score_answer_question_answer.questionId')->where('checkState !=-1')->field('questionDescription as topicName,score_answer_question.questionId,readPay,sum(score_answer_question_answer.thumbUpNumbers) as thumbUpNumbers,count(*) as posts')->order('sum(score_answer_question_answer.thumbUpNumbers) desc')->limit($num)->page($_GET['p'], $num)->select();

        for ($i = 0; $i < count($list); $i++) {
            $list[$i]['p'] = (intval($_GET['p']) - 1) * $num;
            $list[$i]['best'] = M('score_answer_question_answer')->where('questionId = \'' . $list[$i]['questionid'] . '\' and isBestAnswer=1')->getField('answerContent');

        }
        $this->assign('list', $list);
        //分页
        $count = M('score_answer_question_answer')->join('score_answer_question on score_answer_question.questionId = score_answer_question_answer.questionId')->group('score_answer_question_answer.questionId')->where($con)->where('checkState != -1')->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, $num);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);// 赋值分页输出
        $this->display('public/header');
        $this->display('Post/post');
        $this->display('public/footer');
    }

    //社区管理
    public function community()
    {
        $num = 25;
        if (!empty($_GET['bookname'])) {
            $con['name'] = array('like', "%" . $_GET['bookname'] . "%");
        }
        if (empty($_GET['p'])) {
            $_GET['p'] = 1;
        }
        //通过帖子数和点赞数来排序
        $list = M('forum_topic')->join('books on id = forum_topic.bookId')
            ->field('topicId,topicName,remark,postNum as posts,thumbNumbers as \'like\'')
            ->where($con)->order('posts desc,thumbNumbers desc')->limit($num)->page($_GET['p'], $num)->select();

        $totalnum = M('forum_post')->count();
        $this->assign('list', $list);
        //分页
        $count = M('forum_topic')->join('books on id = forum_topic.bookId')->where($con)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, $num);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('totalnum', $totalnum);// 赋值分页输出
        $this->display('Public/header');
        $this->display('Post/community');
        $this->display('Public/footer');
    }

    //删除分答问题
    public function deleltepost()
    {
        $con['questionId'] = $_GET['id'];
//        WriteLog($arr['id'] = $_GET['id']);
        if (empty($_GET['page'])) {
            $p = 1;
        } else {
            $p = $_GET['page'];
        }
        $new['checkState'] = '-1';
        if (M('score_answer_question')->where($con)->save($new)) {
            alertToUrl(__CONTROLLER__ . '/answer/p/' . $p, '删除成功');
        } else {
            alertToUrl(__CONTROLLER__ . '/answer/p/' . $p, '删除失败');
        }
    }

    //分答详情
    public function postdetail()
    {
        $num = 15;
        $con['questionId'] = $_GET['id'];
        if (empty($_GET['p'])) {
            $_GET['p'] = 1;
        }
        if (empty($_GET['page'])) {
            $p = 1;
        } else {
            $p = $_GET['page'];
        }
        $best = M('score_answer_question_answer')->join('score_answer_question on score_answer_question.questionId = score_answer_question_answer.questionId and isBestAnswer=1')->where('score_answer_question_answer.questionId=\'' . I('get.id') . '\'')->field('questionDescription,answerContent,answerId,score_answer_question_answer.thumbUpNumbers,answerTime,teacherId')->select();
        $info = M('score_answer_question_answer')->join('score_answer_question on score_answer_question.questionId = score_answer_question_answer.questionId and isBestAnswer!=1')->where('score_answer_question_answer.questionId=\'' . I('get.id') . '\'')->field('questionDescription,answerContent,answerId,score_answer_question_answer.questionId,score_answer_question_answer.thumbUpNumbers,answerTime,teacherId')->limit($num)->page($_GET['p'], $num)->select();
        $title = M('score_answer_question')->where('score_answer_question.questionId=\'' . I('get.id') . '\'')->field('questionDescription,studentId,questionTime')->select();;
        $title[0]['title'] = '问题:' . $title[0]['questiondescription'];
        $this->assign('info', $info);
        $this->assign('best', $best[0]);
        $this->assign('title', $title[0]);
        $count = M('score_answer_question_answer')->where($con)->where('isBestAnswer!=1')->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, $num);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);// 赋值分页输出
        $this->display('public/header');
        $this->display('Post/detail');
        $this->display('public/footer');
    }

    //删除分答答案
    public function delelteanswer()
    {
        $con['answerId'] = I('get.id');
//        WriteLog($arr['id'] = $_GET['id']);
        if (empty($_GET['page'])) {
            $p = 1;
        } else {
            $p = $_GET['page'];
        }
//        $new['checkState']='-1';
        if (M('score_answer_question_answer')->where($con)->delete()) {
            alertToUrl(__CONTROLLER__ . '/postdetail/id/' . $_GET['question'], '删除成功');
        } else {
            alertToUrl(__CONTROLLER__ . '/postdetail/id/' . $_GET['question'], '删除失败');
        }
    }

    //社区帖子
    public function communication()
    {
        $num = 25;
        if (empty($_GET['p'])) {
            $_GET['p'] = 1;
        }
        $con['topicId'] = $_GET['id'];
        $info = M('forum_topic')->join('books on id = forum_topic.bookId')->where($con)->select();
        $list = M('forum_post')->where($con)->order('buildTime desc')->order('thumbUpNumbers desc')->limit($num)->page($_GET['p'], $num)->select();

        $count = M('forum_post')->where($con)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, $num);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出

        $this->assign('info', $info[0]);
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('list', $list);

        $this->display('public/header');
        $this->display();
        $this->display('public/footer');
    }

    //删除社区帖子
    public function deletecomment()
    {
        $con['postId'] = $_GET['id'];
//        WriteLog($arr['id']=$_GET['id']);
        if (empty($_GET['page'])) {
            $p = 1;
        } else {
            $p = $_GET['page'];
        }
//        $new['checkState']='-1';
        if (M('forum_post')->where($con)->delete()) {
            alertToUrl(__CONTROLLER__ . '/communication/id/' . $_GET['topic'], '删除成功');
        } else {
            alertToUrl(__CONTROLLER__ . '/communication/id/' . $_GET['topic'], '删除失败');
        }
    }

    //书本题库
    public function test()
    {
        $this->display();
    }

    //书本任务
    public function tasks()
    {
        $this->display();
    }
}