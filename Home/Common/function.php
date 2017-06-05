<?php
/**
 * Created by PhpStorm.
 * User: Chan
 * Date: 16/12/15
 * Time: 下午2:10
 */

/**
 * 获取学校所属地
 * $pro -> 省代号
 * $city -> 市代号
 *
 */
function getCity($pro, $city)
{
    $area = M('province')->join(' city on city.provinceId=province.provinceId')->order('province.provinceId')->select();
    for ($i = 0; $i < count($area); $i++) {
        if ($area[$i]['provinceid'] == $pro && $area[$i]['cityid'] == $city) {
            return $area[$i]['provincename'] . ' / ' . $area[$i]['cityname'];
        }
    }
}

/**
 * 获取年级班级数
 * @param $grade 年级id
 */
function getGradeNum($grade)
{
    $num = M('classes')->where('gradeId=' . $grade)->count();
    return $num;
}

/**
 * 增加学校代号时,获取地区学校代号最大值,返回int类型的年级代码
 * @param $pro 省份
 * @param $city 城市
 * 当省市代号为44,4要减一补全0,学校代码7位(2p+2c+3s)
 * SELECT provinceId,cityId,count(*) FROM sea.school where provinceId=44 group by provinceId,cityId
 *  SELECT count(*) FROM sea.school where provinceId=44 and cityId=1
 */
function getSchoolNumber($pro, $city)
{
    $con['provinceId'] = $pro;
    $con['cityId'] = $city;

    $schoolcode = 1 + M('school')->where($con)->count();
    if ($city == 4 && $pro == 44) {
        $schoolcode = $schoolcode - 1;
    }
    switch (strlen($city)) {
        case 1:
            $city = '0' . $city;
            break;
        case 2:
            break;
    }
    switch (count($schoolcode)) {
        case 1:
            $schoolcode = $pro . $city . '00' . $schoolcode;
            break;
        case 2:
            $schoolcode = $pro . $city . '0' . $schoolcode;
            break;
        case 3:
            $schoolcode = $pro . $city . $schoolcode;
            break;
    }
    return $schoolcode;
}

/**
 * 增加年级Id
 * @param $school 学校id
 * @param $grade 年级
 */
function getGradeId($school, $grade)
{
    $gradecode = "" . $school . "" . $grade;
    return $gradecode;
}

/**
 * 拼接Ajax返回json数组
 * @param $tag 标志位
 * @param $msg 消息
 * @param $data 数据
 */
function JsonParse($tag = '0', $msg = '网络错误', $data = [])
{
    return array(
        'tag' => $tag,
        'msg' => $msg,
        'data' => $data,
    );
}

/**
 * 获取学生年级班级名,返回string类型的年级 班级名
 * $class -> 班级
 * $tag -> 0 只返回年级名字和班级名字;1返回学校-年级-班级
 */
function getStuClass($klass, $tag = 0)
{
    if (empty($klass)) {
        return '无归属班级';
    } else {
        $arr = [];
        $con['classId'] = $klass;
        $arr = M('classes')->join('grade on grade.gradeId = classes.gradeId')->join('school on school.schoolId = grade.schoolId')->where($con)->field("gradeName,className,schoolName,school.schoolId,classes.gradeId as grade,classes.graduatetime")->find();
        if (empty($arr)) {
            $grade = M('classes')->where($con)->getField('gradeId');
            $grade = substr($grade, 0, 7);
            $arr = M('classes')->join('school on school.schoolId = ' . $grade)->where($con)->field("className,schoolName,school.schoolId,classes.gradeId as grade,classes.graduatetime")->find();;
        }
        if ($tag) {
            if (!empty($arr['graduatetime'])) {
                $con['name'] = $arr['schoolname'] . '-' . $arr['graduatetime'] . '-毕业班';
            } else {
                $con['name'] = $arr['gradename'] . '-' . $arr['classname'];
            }
            $con['schoolid'] = $arr['schoolid'];
            return $con;
        } else {
            if (!empty($arr['graduatetime'])) {
                $con['name'] = $arr['schoolname'] . '-' . $arr['graduatetime'] . '-毕业班';
            } else {
                $con['name'] = $arr['gradename'] . '-' . $arr['classname'];
            }
            return $con['name'];
        }
    }
}

/**
 * 获取老师执教班级名,返回string类型的年级 班级名
 * $class -> 班级
 */
function getTeachClass($teacher)
{
    if (empty($teacher)) {
        return '无归属班级';
    } else {
        $con['teacherId'] = $teacher;
        $arr = M('teacher_class_relationship')->where($con)->field("classId,isMaster")->select();
//        dump($arr);exit;
        if (count($arr) == 0) {
            return '无归属班级';
        }
        if (count($arr) != 0) {
            $str = "";
            for ($i = 0; $i < count($arr); $i++) {
                $str .= getStuClass($arr[$i]['classid']) . ",";
            }
        }
        return $str;
    }
}

/**
 * 获取年级班级数
 * @param $klass varchar 班级代码
 * @param $stu boolean 是否返回人数,否只返回人数,是返回班级学生
 */
function getClassMates($klass, $stu = false)
{
    $con['classId'] = $klass;
    if ($stu) {
        $arr = M('user')->where($con)->field('userId,name')->select();
        return $arr;
    } else {
        $arr = M('user')->where($con)->count();
        return $arr;
    }
}

/**
 * 弹窗并跳转到上一页
 * @param string $message 提示信息
 */
function alertToBack($message)
{
    echo "<script>alert('$message');history.back();</script>";
    exit;
}

/**
 * 限制字符字数
 * @param string $message 提示信息
 */
function subtext($text, $length)
{
    if (mb_strlen($text, 'utf8') > $length)
        return mb_substr($text, 0, $length, 'utf8') . '...';
    return $text;
}

/**
 * 限制字符字数
 * @param string $start 开始位置
 * @param string $text 文本
 * @param string $length 文本长度
 */
function subtextnodot($start = 0, $text, $length)
{
    if (mb_strlen($text, 'utf8') > $length)
        return mb_substr($text, $start, $length, 'utf8');
    return $text;
}

/**
 * 结束时间和开始时间的获取
 * @param $time 时间戳
 * @param $tag 是否获取上个月,true代表是上个月的时间
 * @param $endflag 是否获取当前月的最后一天,0为否,1为是
 * @param $status 返回是否为时间戳 true 为时间戳,false为标准显示时间
 */
function getsptime($time, $tag = 0, $endflag = 0, $status = 0)
{
    $time = explode('-', date('Y-m-d H:i:s', $time));
    if ($tag) {
        if (!$endflag) {//上个月的第一天
            if ($time[1] == '1') {
                $times = date("Y-m-d 00:00:00", mktime(0, 0, 0, 12, 1, $time[0] - 1));
            } else {
                $times = date("Y-m-d 00:00:00", mktime(0, 0, 0, $time[1] - 1, 1, $time[0]));
            }
        } else {//上个月的最后一天
            if ($time[1] == '1') {
                $times = date("Y-m-d 23:59:59", mktime(0, 0, 0, 12, 31, $time[0] - 1));
            } else {
                $times = date("Y-m-" . date('t') . " 23:59:59", mktime(0, 0, 0, $time[1] - 1, 2, $time[0]));
            }
        }
    } else {
        if (!$endflag) {//这个月的第一天
            $times = date("Y-m-d 00:00:00", mktime(0, 0, 0, $time[1], $time[2], $time[0]));
        } else {//这个月的最后一天
            $times = date("Y-m-" . date('t') . " 23:59:59", mktime(0, 0, 0, $time[1], $time[2], $time[0]));
        }
    }
    if ($status) {
        return mktime($times);
    }
    return $times;
}

/**
 * 结束时间和开始时间的获取
 * @param $yesterday 是否为昨天
 * @param $status 是否为时间戳,1是,0不是
 * @param $tag 是否要加上时分秒,昨天为0:0:0,今天为23:59:59
 */
function getDay($yesterday = false, $status = false, $tag = false)
{
    $time = time();
    if ($yesterday) {
        if ($tag) {
            $time = date('Y-m-d 0:0:0', $time - 24 * 60 * 60);
        } else {
            $time = date('Y-m-d', $time - 24 * 60 * 60);
        }
    } else {
        if ($tag) {
            $time = date('Y-m-d 23:59:59', $time);
        } else {
            $time = date('Y-m-d', $time);
        }
    }
    if ($status) {
        $time = strtotime($time);
    }
    return $time;

}

function alertToUrl($url, $message)
{
    echo "<script>alert('$message');window.location.href='$url';</script>";
    exit;
}

function GoToUrl($url)
{
    echo "<script>window.location.href='$url';</script>";
    exit;
}

function getUid($namespace = '')
{
    static $guid = '';
    $uid = uniqid("", true);
    $data = $namespace;
    $data .= $_SERVER['REQUEST_TIME'];
    $data .= $_SERVER['HTTP_USER_AGENT'];
    $data .= $_SERVER['LOCAL_ADDR'];
    $data .= $_SERVER['LOCAL_PORT'];
    $data .= $_SERVER['REMOTE_ADDR'];
    $data .= $_SERVER['REMOTE_PORT'];
    $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
    $guid =
        substr($hash, 0, 8) .
        '-' .
        substr($hash, 8, 4) .
        '-' .
        substr($hash, 12, 4) .
        '-' .
        substr($hash, 16, 4) .
        '-' .
        substr($hash, 20, 12);
    return $guid;
}

/**
 * crul抓取数据的自定义通用函数
 * $url提交或者第三方api接口的url,如需要传参,则需要将参数事先拼接好
 * $method => method:post/get
 * $arr post的请求数组
 * $httpsflag是否为https,true代表->是
 * $header某些第三方api需要的apikey,如百度API
 */
function http_curl($url, $method = 'get', $arr = '', $httpsflag = 0, $headerflag = 0)
{
    $ch = curl_init();
    //判断method类型
    if ($method == 'post') {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
    }
    //判断是否为加密
    if ($httpsflag) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
    }
    //判断需不需要apikey
    if ($headerflag || empty($headerflag)) {
        // 添加apikey到header
        $header = array(
            'apikey:' . C('apikeys'),
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $res = curl_exec($ch);
    curl_close($ch);
    if (curl_errno($ch)) {
        var_dump(curl_error($ch));
    } else {
        return $res;
    }
}

function getUserName($id)
{
    return $name = M('user')->where('userId=\'' . $id . '\'')->getField('name');
}

function getpages($page)
{
    return empty($page) ? '/p/1' : '/p/' . $page;
}

function getTypelist($type)
{
    return empty($type) ? '' : '/type/' . $type;
}

function getTypeName($type, $fatherType = '')
{
    $con['id'] = $type;
    $name = M('system_variables')->where($con)->getField('name');
    echo $name;
}

//获取ip地址
function getClientIp($type = 0)
{
    $type = $type ? 1 : 0;
    static $ip = NULL;
    if ($ip !== NULL) return $ip[$type];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos = array_search('unknown', $arr);
        if (false !== $pos) unset($arr[$pos]);
        $ip = trim($arr[0]);
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}


/**
 * 按照实际系统的年月日
 * @param $y 年
 * @param $m 月
 * @param $d 日
 * @param $tag 0位字符串,1为时间戳
 * @param $flag 是否要补全时分秒,1按照当前补全,0不补全,
 */
function getTime($y, $m, $d, $tag = 0, $flag = 0)
{
    switch ($flag) {
        case 0:
            $time = date('Y-m-d H:i:s', mktime(0, 0, 0, $m, $d, $y));
            break;
        case 1:
            $time = date('Y-m-d H:i:s', mktime(date('H', time()), date('i', time()), date('s', time()), $m, $d, $y));
            break;
    }
    if ($tag) {
        return mktime($time);
    } else {
        return $time;
    }
}

//对象转数组,使用get_object_vars返回对象属性组成的数组
function objectToArray($obj)
{
    $arr = is_object($obj) ? get_object_vars($obj) : $obj;
    if (is_array($arr)) {
        return array_map(__FUNCTION__, $arr);
    } else {
        return $arr;
    }
}

//获取正确返回路径
function getRightUrl()
{
    $str = "";
    if (!empty($_GET['type'])) {
        $str .= '/type/' . $_GET['type'];
    }
    if (!empty($_GET['p'])) {
        $str .= '/p/' . $_GET['p'];
    }
    if (!empty($_GET['role'])) {
        $str .= '/role/' . $_GET['role'];
    }
    return $str;
}

//根据变量修改变量值
function valueChange($keyword, $values)
{
    $urls = __SELF__;
    if (strpos($urls, $keyword) === false) {
        //如果存在.html结尾,先把html去掉,再修改url。
        if (strpos($urls, '.html') > 0) {
            $urls = substr($urls, 0, strlen($urls) - 5);
        }
        //如果存在get参数,在get参数里面替换
        if (count(explode("?", $urls)) > 1) {
            $arr = explode("?", $urls);
            $arr[0] .= '/' . $keyword . '/' . $values;
            $urls = implode("?", $arr);
        } else {
            $urls .= '/' . $keyword . '/' . $values;
        }
    } else {
        $urls = str_replace($keyword . '/' . $_GET[$keyword], $keyword . '/' . $values, $urls);
    }
    echo $urls;
}

///**
// * 格式化字节大小
// * @param  number $size      字节数
// * @param  string $delimiter 数字和单位分隔符
// * @return string            格式化后的带单位的大小
// *
function format_bytes($size, $delimiter = '')
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

//写入日志,$data为相关的id值
function WriteLog($data = "")
{
    if (C('OPERATION_ON')) {
        $arr['userid'] = $_SESSION['edminInfo']['userId'];
        $arr['ip'] = getClientIp();
        $arr['time'] = strtotime(date("Y-m-d H:i:s"));
        //全部转换为小写以免有其他的问题
        $arr['c'] = strtolower(CONTROLLER_NAME);
        $arr['a'] = strtolower(ACTION_NAME);
        $arr['agent'] = $_SERVER['HTTP_USER_AGENT'];
        //获取传参的值
        if (!empty($data)) {
            $arr['id'] = $data;
        }
        if (M('logs')->add($arr)) {
            return true;
        } else {
            \Think\Log::write(date("Y-m-d H:i:s") . $arr['c'] . '的' . $arr['a'] . '系统日志记录失败', 'WARN', true);
        }
    }
}

//获取用户操作系统
function getOS($agent)
{
//    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $agent = strtolower($agent);
    if (strpos($agent, 'windows nt')) {
        $platform = 'Windows';
    } elseif (strpos($agent, 'macintosh')) {
        $platform = 'Macintosh';
    } elseif (strpos($agent, 'ipod')) {
        $platform = 'Ipod';
    } elseif (strpos($agent, 'ipad')) {
        $platform = 'Ipad';
    } elseif (strpos($agent, 'iphone')) {
        $platform = 'Iphone';
    } elseif (strpos($agent, 'android')) {
        $platform = 'Android';
    } elseif (strpos($agent, 'unix')) {
        $platform = 'Unix';
    } elseif (strpos($agent, 'linux')) {
        $platform = 'Linux';
    } else {
        $platform = 'other';
    }
    return $platform;
}

//获取浏览器信息,$glue为胶水,
function get_client_browser($agent = "", $glue = null)
{
    $browser = array();
    if (empty($agent)) {
        $agent = $_SERVER['HTTP_USER_AGENT']; //获取客户端信息
    }

    /* 定义浏览器特性正则表达式 */
    $regex = array(
        'ie' => '/(MSIE) (\d+\.\d)/',
        'chrome' => '/(Chrome)\/(\d+\.\d+)/',
        'firefox' => '/(Firefox)\/(\d+\.\d+)/',
        'opera' => '/(Opera)\/(\d+\.\d+)/',
        'safari' => '/Version\/(\d+\.\d+\.\d) (Safari)/',
    );
    foreach ($regex as $type => $reg) {
        preg_match($reg, $agent, $data);
        if (!empty($data) && is_array($data)) {
            $browser = $type === 'safari' ? array($data[2], $data[1]) : array($data[1], $data[2]);
            break;
        }
    }
    return empty($browser) ? false : (is_null($glue) ? $browser : implode($glue, $browser));
}
