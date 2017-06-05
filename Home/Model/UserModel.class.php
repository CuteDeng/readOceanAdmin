<?php
/*
 * Created by PhpStorm.
 * User: Chan
 * Date: 16/12/21
 * Time: 下午1:20
 */
namespace Home\Model;

use Think\Model;

class UserModel extends Model
{
    //用户登录判断
    public function checkusr($usrname, $pwd)
    {
        $con['usr'] = $usrname;
        $con['pwd'] = $pwd;
        $user = M('edmin')->where($con)->find();
        if ($user) {
            if ($user['status'] != 1) {
                $data['lasttime'] = time();
                $data['lastip'] = $_SERVER["REMOTE_ADDR"];
                $data['marks'] = sha1(mt_rand());//随机码,作为单机登录标志
                if (M('edmin')->where($con)->save($data)) {
                    $user = M('edmin')->where($con)->find();
                    //如果没有权限也让他进入系统,但是不给任何系统权限
                    $role = M('role')->join('edmin on edmin.role = role.roleId')->where($con)->getField('privilege');
                    $privilege=M('privilege')->query("select * from privilege where pid in (" . $role . ")");
                    if (empty($role)) {
                        $role = 1;
                    }
                    $arr = array('username' => $user['name'],
                        'userId' => $user['eid'],
                        'logintime' => date("Y-m-d H:i:s", $user['lasttime']),
                        'role' => $user['role'],
                        'school' => $user['schoolid'],
                        'grade' => $user['gradeid'],
                        'remark' => $data['marks'],
                        'privilege' => $privilege,
                        'prilist' => explode(',', $role),
                    );
                    session(array('name' => 'edminInfo', 'expire' => time() + 0.2 * 1800));//设置过期时间,为3分钟
                    session('edminInfo', $arr);
                    S('remarks',$data['marks'],100);//remarks作为后台remark的单点标示,以秒作单位
                    S('right',$privilege,6000);//remarks作为后台remark的单点标示,以秒作单位
                    return 3;//登陆成功
                } else {
                    return 4;//session写入错误
                }
            } else {
                return 2;//该用户未激活,请联系超级管理员
            }
        } else {
            return 1;//该用户不存在
        }
    }

    //检查用户名是否存在
    public function checkusername($username)
    {
        $con['usr'] = $username;
        if (M('edmin')->where($con)->count() != 0) {
            return false;
        } else {
            return true;
        }

    }

    //新增用户的model类,根据第二个参数的值,确定身份。
    public function adduser($data, $type = 'user_type_student')
    {
        $data['type'] = $type;
        $data['status'] = '1';
        $data['password'] = sha1(md5($data['idcard']));//默认密码
        $con['userId'] = getUid();
        while (1) {
            $flag = M('user')->where($con)->select();
            if ($flag) {
                $con['userId'] = getUid();
            } else {
                $data['userId'] = $con['userId'];
                break;
            }
        }
        switch ($data['sex']) {
            case '男':
                $data['picUrl'] = 'http://ro.bnuz.edu.cn/user/default/img_boy.png';
                break;
            case '女':
                $data['picUrl'] = 'http://ro.bnuz.edu.cn/user/default/img_girl.png';
                break;
        }
        $data['enrollTime'] = date('Y-m-d H:i:s', time());
        if ($type == 'user_type_teacher') {
            $data['picUrl'] = 'http://ro.bnuz.edu.cn/user/default/img_default.png';
        }
        if (M('user')->add($data)) {
            switch ($type) {
                case 'user_type_teacher':
                    if (!$this->initRankandScore($data['userId'])) {
                        return 0;//说明失败了
                    } else {
                        break;
                    }
                case 'user_type_student':
                    if (!$this->initRankandScore($data['userId'])) {
                        return 0;//说明失败了
                    } else {
                        break;
                    }
            }
            return 1;//说明成功了
        } else {
            return 0;//说明失败了
        }
    }

    //初始化学生等级和积分
    private function initRankandScore($userId)
    {
        $Score['userId'] = $Rank['userId'] = $userId;
        $Rank['rank'] = 1;//初始等级为1
        $Score['totalIntegral'] = 0;//初始积分均为0
        $Score['costIntegral'] = 0;
        if (M('user_rank')->add($Rank) && M('user_integral')->add($Score)) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * 修改用户状态位
     * @param $userId 用户id
     * @param $action 对应用户行为,open(1),close(0),cantgraduate(2),nopay(3),payafter,cangraduate
     */
    public function changeUserStatus($userId, $action)
    {
        switch ($action) {
            case 'open':
                $con['status'] = 1;
                break;//激活用户
            case 'close':
                $con['status'] = 0;
                break;//禁用用户
            case 'cangraduate':
                $data['status'] = 1;
                $con['status'] = 2;
                break;//用户毕业
            case 'cantgraduate':
                $data['status'] = 2;
                $con['status'] = 1;
                break;//用户毕业后仍然让他登录
            case 'nopay':
                $data['status'] = 1;
                $con['status'] = 3;
                break;//学校未付款
            case 'payafter':
                $data['status'] = 3;
                $con['status'] = 1;
                break;//学校已付款,恢复登录
        }
        $data['userId'] = $userId;
        if (M('user')->where($data)->save($con)) {
            return 1;//成功
        } else {
            return 0;//失败
        }
    }
}