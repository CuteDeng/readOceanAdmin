<?php
/*/**
 * Created by PhpStorm.
 * User: Chan
 * Date: 16/12/21
 * Time: 下午1:20
 */
namespace Home\Model;

use Think\Model;

class TeachingModel extends Model
{
    protected $trueTableName = 'teacher_class_relationship';

    /**
     * @return string
     */
    public function getTrueTableName()
    {
        return $this->trueTableName;
    }

    public function getTeachingClass($teacher)
    {
        $con['teacherId'] = $teacher;
        $arr = M('teacher_class_relationship')->where($con)->field('classid')->select();
        $klassto1 = [];//用来解除两层数组
        for ($i = 0; $i < count($arr); $i++) {
            array_push($klassto1, $arr[$i]['classid']);
        }
        return $klassto1;
    }

}