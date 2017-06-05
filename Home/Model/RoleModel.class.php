<?php
namespace Home\Model;

use Think\Model;

class RoleModel extends Model
{
    /* 整理权限 */
    public function sortPri($array)
    {
        $count = count($array);
        if ($count <= 0) {
            return false;
        }
        for ($i = 0; $i < $count; $i++) {
            for ($k = $count - 1; $k > $i; $k--) {
                if ($array[$k] < $array[$k - 1]) {
                    $tmp = $array[$k];
                    $array[$k] = $array[$k - 1];
                    $array[$k - 1] = $tmp;
                }
            }
        }
        return $array;
    }
}

?>