<?php
namespace Home\Controller;

use Think\Controller;
use Component\CommonController;

class IndexController extends Controller
{
    //首页
    public function index()
    {
        if (empty($_SESSION['edminInfo'])) {
            alertToUrl(__MODULE__ . "/user/login", "用户未登录");
        }
        //前五条意见反馈
        $feedback = M('edmin_feedback');
        $con['isAnswer'] = 1;
        $list = $feedback->join('edmin_feedback_answer on edmin_feedback_answer.feedbackId=edmin_feedback.feedbackId')->join('system_variables on level = id')->where($con)->order('responseTime desc,clicknum desc')->limit(5)->field('edmin_feedback.feedbackId as id,title,name,clicknum,responseTime')->select();
        $this->assign('list', $list);
        //系统公告

        $this->display('Public:header');
        $this->display();
        $this->display('Public:footer');
    }

    //获取本月任务情况
    public function getTask()
    {
        //getsptime(时间戳,上个月1,最后一天1,时间戳1)
        $begin = getsptime(time(), 1);
        $end = getsptime(time(), 1, 1);
        $time = explode('-', date('Y-m-d H:i:s', time()));
        $tasknum['month'] = $time['1'] == '1' ? '12' : ($time['1'] - 1);
        $con['publishDate'] = array('between', array($begin, $end));
        $tasknum['add'] = M('task')->where($con)->count();
        $tasknum['total'] = M('task')->count();
        $this->ajaxReturn($tasknum);
    }

    //获取最新回复的意见反馈
    public function getLastFeedback()
    {
        $feedback = M('feedback');
        $con['isAnswer'] = 1;
        $list = $feedback->where($con)->order('edittime desc')->limit(5)->select();
        cookie('feedback', $list);
        $this->ajaxReturn($list);
    }

//    //ie兼容页面
    public function IEWarning(){
        $this->display('index/warning');
    }
}