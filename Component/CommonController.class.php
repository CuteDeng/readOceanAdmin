<?php
namespace Component;

use Think\Controller;
use Think\Verify;

//继承此类可以判断是否登录，只有登陆用户才能访问该方法
class CommonController extends Controller
{
    public function __construct()
    {
        if (empty($_SESSION['edminInfo'])) {
            GoToUrl(__MODULE__ . "/user/login");
        } else {
            $con['eid'] = $_SESSION['edminInfo']['userId'];
            //判断缓存是否存在,如不存在重新取数据库的单点标志位
            $rec = S('remarks');
            if (empty($rec)) {
                $marks = M('edmin')->where($con)->getField('marks');
                S('remarks', $marks, 90);//保持一分半不向数据库读取remarks
            }
            if (S('remarks') != $_SESSION['edminInfo']['remark']) {
                alertToUrl(__MODULE__ . "/user/login", "对不起,您的账户已在其他地方登录,如非本人操作,建议修改密码。");
            }
        }
        //检查用户权限
//            $pcheck['controller'] = strtolower(CONTROLLER_NAME);
//            $pcheck['action'] = strtolower(ACTION_NAME);
//            $action = M('privilege')->where($pcheck)->getField('pid');
//            if (!empty($action)) {
//                if (!in_array($action, $_SESSION['edminInfo']['prilist'])) {
//                    alertToBack("对不起,您没有相关权限。");
//                }
//            }
        //判断方法是否存在于数组,若是则记录日志,特殊情况特殊处理
        if (in_array(strtolower(ACTION_NAME), C('WriteLog.' . strtolower(CONTROLLER_NAME)))) {
            //获取传参的值
            if (!empty($_GET['id'])) {
                $arr['id'] = $_GET['id'];
            }

            if (!empty($_GET['gid'])) {
                $arr['id'] = $_GET['gid'];
            }
            if (!empty($_POST['rank'])) {
                $arr['id'] = $_POST['rank'];
            }
            WriteLog($arr['id']);
        }
        parent::__construct();
    }

    public function code()
    {
        ob_clean();
        $config = array(
            'useCurve' => true,            // 是否画混淆曲线
            'useNoise' => true,            // 是否添加杂点
            'length' => 4,               // 验证码位数
            'fontSize' => 30,              // 验证码字体大小(px)
            'imageH' => 50,               // 验证码图片高度
            'imageW' => 180,               // 验证码图片宽度
        );
        $verify = new Verify($config);
        $verify->entry();
    }

    public function mail()
    {
        if (empty(session('userInfo')['id'])) {
            $data['states'] = -1;
            $data['content'] = L('oth_4');
        } else {
            if (!empty($_POST['message'])) {
                $mail = D('mail');
                $con['send_id'] = session('userInfo')['id'];
                $con['receiver_id'] = $_POST['receiver_id'];
                $con['message'] = $_POST['message'];
                $con['time'] = time();
                if ($mail->add($con)) {
                    $data['states'] = 1;
                    $data['content'] = L('oth_5');
                }
            } else {
                $data['states'] = 0;
                $data['content'] = L('oth_6');
            }
        }
        echo json_encode($data);
    }

}

?>