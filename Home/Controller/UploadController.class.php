<?php
/**
 * Created by PhpStorm.
 * User: Chan
 * Date: 16/12/12
 * Time: 下午3:48
 * Note: 数据导入
 */

namespace Home\Controller;

use Think\Controller;
use Component\CommonController;

class UploadController extends CommonController
{
    //批量上传,上传excel的公用函数
    protected function Excelupload($con)
    {
        header("Content-Type:text/html;charset=utf-8");
        $upload = new \Think\Upload($con);// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('xls', 'xlsx');// 设置附件上传类
        // 上传文件
        $info = $upload->uploadOne($_FILES['excelData']);
        $filename = './Uploads/' . $info['savepath'] . $info['savename'];
        $exts = $info['ext'];
        if (!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        } else {// 上传成功
            return (array('filename' => $filename, 'ext' => $exts));
        }
    }

    //PHPExcel的上传通用函数,entity用来判断一些必要的数据,例如列数等。
    protected function ExcelImport($filename, $exts = 'xls', $entity)
    {
        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能import导入
        import("Org.Util.PHPExcel");
        //创建PHPExcel对象，注意，不能少了\
        $PHPExcel = new \PHPExcel();
        //如果excel文件后缀名为.xls，导入这个类
        if ($exts == 'xls') {
            import("Org.Util.PHPExcel.Reader.Excel5");
            $PHPReader = new \PHPExcel_Reader_Excel5();
        } else if ($exts == 'xlsx') {
            import("Org.Util.PHPExcel.Reader.Excel2007");
            $PHPReader = new \PHPExcel_Reader_Excel2007();
        }
        //载入文件
        $PHPExcel = $PHPReader->load($filename);
        //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推

        //通过传入的entity获取相关配置
        $currentSheet = $PHPExcel->getSheet($entity['sheetNum']);

        //获取总列数
//        $allColumn = $currentSheet->getHighestColumn();//大写字母代表最大值
        $allColumn = $entity['maxColumn'];//大写字母代表最大值
        //获取总行数
        $allRow = $currentSheet->getHighestRow();//数字表示最大值

        //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
        for ($currentRow = 1; $currentRow <= $allRow; $currentRow++) {
            //从哪列开始，A表示第一列
            for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
                //数据坐标
                $address = $currentColumn . $currentRow;
                //读取到的数据，保存到数组$arr中
                $cell = $currentSheet->getCell($address)->getValue();
                if (!empty($cell)) {
                    $data[$currentRow][$currentColumn] = $cell;
                }
                if ($cell instanceof PHPExcel_RichText) {
                    $cell = $cell->__toString();
                }
            }

        }
        return $data;//将excel里面的数据传出
        //在这里通过不同的entity来入不同的库
    }

    //批量导入学生
    public function classmates()
    {
        $classmate = C('klass');//引用配置
        $entity = array('maxColumn' => C('longs')[count($classmate)], 'sheetNum' => 0);
        $config = $this->Excelupload(array('savePath' => 'class/'));//获取相关文件值
        $data = $this->ExcelImport($config['filename'], $config['ext'], $entity);
        $model = new \Think\Model();
        $model->startTrans();
        $tag = 0;//事务标签
        $user = M('user');
        foreach ($data as $key => $item) {
            $arr = [];
            if ($key > 2) {
                for ($i = 0; $i < count($classmate); $i++) {
                    $arr[$classmate[$i]] = $item[C('longs')[$i + 1]];
                }
                $arr['classId'] = I('post.id');
                $arr['schoolId'] = I('post.schoolid');
                if (D('user')->adduser($arr)) {
                    $tag += 1;
                }
            }
        }
        if ($tag == count($data) - 2) {
            $model->commit();
            WriteLog($config['filename'] . '--exp--' . $arr['classId']);//将文件名和学校id一同保存,--exp--作为分割符
            alertToUrl(__MODULE__ . '/school/classmate/id/' . $_POST['id'], "学生添加成功");
        } else {
            $model->rollback();
        }
    }

    //批量导入教师
    public function teachers()
    {
        $teacher = C('teacher');//引用配置
        $entity = array('maxColumn' => C('longs')[count($teacher)], 'sheetNum' => 0);
        $config = $this->Excelupload(array('savePath' => 'teacher/'));//获取相关文件值
        $data = $this->ExcelImport($config['filename'], $config['ext'], $entity);
        $model = new \Think\Model();
        $model->startTrans();
        $tag = 0;//事务标签
        $user = M('user');
        foreach ($data as $key => $item) {
            $arr = [];
            if ($key > 2) {
                for ($i = 0; $i < count($teacher); $i++) {
                    $arr[$teacher[$i]] = $item[C('longs')[$i + 1]];
                }
                $arr['schoolId'] = I('post.schoolid');
                if (D('user')->adduser($arr, 'user_type_teacher')) {
                    $tag += 1;
                }
            }
        }
        if ($tag == count($data) - 2) {
            $model->commit();
            WriteLog($config['filename'] . '--exp--' . $arr['schoolId']);//将文件名和学校id一同保存,--exp--作为分割符
            alertToUrl(__MODULE__ . '/school/teacher?schoolid=' . $_POST['id'], "教师添加成功");
        } else {
            $model->rollback();
        }
    }

    //批量导入题库
    public function questions()
    {
        $tag1 = 0;//判断题的标志位
        $tag2 = 0;//单选题的标志位
        $tag3 = 0;//多选题的标志位
        $column = C('longs');
        $truefalse = C('truefalse');//引用配置
        $entity1 = array('maxColumn' => C('longs')[count($truefalse)], 'sheetNum' => 0);

        $single = C('single');//引用配置
        $entity2 = array('maxColumn' => C('longs')[count($single)], 'sheetNum' => 1);

        $multiple = C('multiple');//引用配置
        $entity3 = array('maxColumn' => C('longs')[count($multiple)], 'sheetNum' => 2);

        $config = $this->Excelupload(array('savePath' => 'question_bank/'));//获取相关文件值

        $data_truefalse = $this->ExcelImport($config['filename'], $config['ext'], $entity1);
        $data_single = $this->ExcelImport($config['filename'], $config['ext'], $entity2);
        $data_multiple = $this->ExcelImport($config['filename'], $config['ext'], $entity3);

        $book = $_GET['id'];
        $bookname = M('books')->where('id=\'' . $book . '\'')->getField('name');
        $model = new \Think\Model();
        $model->startTrans();
        //处理判断题题库数据
        foreach ($data_truefalse as $key => $item) {
            $arr = [];
            //$key 行
            if ($key > 2) {
                //column 列
                for ($i = 0; $i < count($truefalse); $i++) {
                    $arr[$truefalse[$i]] = $item[$column[$i + 1]];
                }
                if (D('QuestionBank')->addtrueorfalse($book, $arr)) {
                    $tag1 += 1;
                }
            }
        }
        //处理单选题题库数据
        foreach ($data_single as $key => $item) {
            $arr = [];
            //$key 行
            if ($key > 2) {
                //column 列
                for ($i = 0; $i < count($single); $i++) {
                    $arr[$single[$i]] = $item[$column[$i + 1]];
                }
                if (D('QuestionBank')->addsingle($book, $arr)) {
                    $tag2 += 1;
                }
            }
        }
//        处理多选题题库数据
        foreach ($data_multiple as $key => $item) {
            $arr = [];
            //$key 行 因为表保护会多了一行
            if ($key > 2) {
                //column 列
                for ($i = 0; $i < count($multiple); $i++) {
                    $arr[$multiple[$i]] = $item[$column[$i + 1]];
                }
                if (D('QuestionBank')->addmultiple($book, $arr)) {
                    $tag3 += 1;
                }
            }
        }
        if ($tag1 == count($data_truefalse) - 2 && $tag2 == count($data_single) - 2 && $tag3 == count($data_multiple) - 2) {
            $model->commit();
            WriteLog($config['filename'] . '--exp--' . $book);
            alertToUrl(__MODULE__ . '/book/test', '《' . $bookname . '》 的题库导入成功');
        } else {
            $model->rollback();
            alertToBack('导入失败,请稍后再试');
        }
    }

    //simiditor上传图片接口
    public function Imgupload()
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录
        // 上传单个文件
        $info = $upload->upload();
        if (!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        } else {// 上传成功 获取上传文件信息
            foreach ($info as $file) {
                $url = HOST_URL . 'Uploads/' . $file['savepath'] . $file['savename'];//返回给前台的src图片路经地址。
                //预留接口 ************
                //在这里可以把图片地址写入数据库 或者对图片进行操作 例如生成缩略图

                //这里返回每一次的URL pulpload 规则 参见 编辑器js
                $this->ajaxReturn($url, 'EVAL');
            }
        }
    }

    //simiditor上传图片接口
    public function RoImgupload()
    {
        $upload = new \Think\Upload();// 实例化上传类
        $config = array(
            'maxSize' => 3145728,
            'rootPath' => './Uploads/',
            'savePath' => 'picture/',
            'saveName' => array('uniqid', ''),
            'exts' => array('jpg', 'gif', 'png', 'jpeg'),
            'autoSub' => true,
            'subName' => array('date', 'Ymd'),
        );
        $ftpConfig = array(
            'host' => '172.20.13.11', //服务器
            'port' => 21, //端口
            'timeout' => 90, //超时时间
            'username' => 'read_ocean', //用户名
            'password' => 'a.123456', //密码
        );
        $upload = new \Think\Upload($config, 'Ftp', $ftpConfig);// 实例化上传类
        // 上传文件
        $info = $upload->upload();
        $info = $info['fileData'];
        $arr['file_path'] = 'http://ro.bnuz.edu.cn/Uploads/' . $info['savepath'] . $info['savename'];//返回给前台的src图片路经地址。
        $this->ajaxReturn($arr);
    }
}