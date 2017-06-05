<?php
/**
 * Created by PhpStorm.
 * User: Chan
 * Date: 17/3/7
 * Time: 下午3:02
 */

namespace Behavior;

use Think\Behavior;

class ClearSessionBehavior extends Behavior
{
    public function run(&$params)
    {
        //关闭浏览器时,清空session
        /**
         * @param $arr 前面函数传参
         * 以上是arr传递参数的格式。
         */
        if (C('OPERATION_ON')) {

            session('edminInfo', null);
        }
    }
}