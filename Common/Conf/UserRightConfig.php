<?php
/**
 * Created by PhpStorm.
 * User: Chan
 * Date: 17/3/7
 * Time: 下午3:12
 * Note: 用户权限配置,每个id值对应为后台edmin表的roleId,此配置有点类似于用户权限分组,如添加不同的管理员需要对齐进行相应修改。
 * Remarks:1-超级管理员,2-普通管理员,3-学校管理员,4-测试账户,5-年级管理员,6-数据运营
 */
return array(
    //超级管理员,可以进入到RoleController
    //SudoStr才能判断成功,不解之谜。
    'Sudo'=>array(1,2),
    'SudoStr'=>'1',
    //管理员权限表
    'AdminUser' => array(1, 2),
    //学校管理员列表
    'SchoolUser' => array(3, 5),
    //
    'UploadUser' => '1,2,4',
    'EditRoleRight' => '1',
);