<?php
namespace Home\Controller;

use Think\Controller;
use Think\Verify;

class UserController extends Controller
{
    //验证码
    public function code()
    {
        ob_clean();
        $config = array(
            'useCurve' => false,            // 是否画混淆曲线
            'useNoise' => false,            // 是否添加杂点
            'length' => 3,               // 验证码位数
            'fontSize' => 18,              // 验证码字体大小(px)
            'imageH' => 35,               // 验证码图片高度
            'imageW' => 150,               // 验证码图片宽度
            'fontttf' => '5.ttf'  //验证码字体库
        );
        $verify = new Verify($config);
        $verify->entry();
    }

    //登录
    public function login()
    {
        $this->display();
    }

    //退出登录
    public function logout()
    {
        WriteLog();
        session('edminInfo', null);
        alertToUrl(__CONTROLLER__ . '/login', '退出成功');
    }

    //保存用户信息
    public function saveuser()
    {
        $res['tag'] = 1;
        if (ISPOST) {
            $con['eid'] = $_SESSION['edminInfo']['userId'];
            $con['name'] = $_POST['username'];
            if (!empty($_POST['pwd'])) {
                $con['pwd'] = sha1($_POST['pwd']);
            }
            $result = M('edmin')->save($con);
            if (false !== $result || 0 !== $result) {
                $res = JsonParse('0', '成功');
                $_SESSION['edminInfo']['username'] = $con['name'];
            }
        }
        $this->ajaxReturn($res);
    }

    //判断是否可以登录
    public function checkLogin()
    {
        if (!empty($_POST)) {
            $verify = new Verify();
            if ($verify->check($_POST['code'])) {
                $flag = D('User')->checkusr($_POST['username'], sha1(md5($_POST['pwd'])));
                if ($flag == 3) {
                    WriteLog();
                    $res=JsonParse('1','登录成功');
                } else if ($flag == 2) {
                    $res=JsonParse('0','该用户未激活,请联系管理员');
                } else if ($flag == 4) {
                    $res=JsonParse('0','网络错误,请重试');
                } else {
                    $res=JsonParse('0','用户名或密码错误');
                }
            } else {
              $res=JsonParse('0','验证码错误');
            }
        }
        $this->ajaxReturn($res);
    }
}