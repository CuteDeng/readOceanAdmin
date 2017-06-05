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

class InfoStatisticController extends CommonController
{
    public function info()
    {
        alertToBack("努力开发中,敬请期待!");
    }
}