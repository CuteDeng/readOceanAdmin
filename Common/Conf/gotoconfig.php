<?php
/**
 * Created by PhpStorm.
 * User: Chan
 * Date: 17/4/5
 * Time: 下午10:27
 * Note:需要连接的日志配置
 */
return array(
//需要跳转的日志的配置
    "Goto" => array(
        'school' => array(
            'saveclassname' => HOST_URL . 'School/editgrade/id/',
            'addSchool' => HOST_URL . 'School/schoolinfo/school/',
            'addclass' => HOST_URL . 'School/editgrade/id/',
            'addstu' => HOST_URL . 'School/userdetail/id/',
            'upgrade' => HOST_URL . 'School/schoolinfo/school/',
            'reset' => HOST_URL . 'School/userdetail/id/',
            'editinfo' => HOST_URL . 'School/userdetail/id/',
            'editteacher' => HOST_URL . 'School/teacherdetail/id/',
            'addteachers' => HOST_URL . 'School/teacherdetail/id/',
        ),
        'role' => array(
            'roleEdit' => HOST_URL . 'Role/roleEdit/id/',
        ),
        'system' => array(
            'addusers' => HOST_URL . 'System/userinfo/id/',
            'comment' => HOST_URL . 'System/opiniondetail/id/',
            'reset' => HOST_URL . 'System/userinfo/id/',
            'closeuser' => HOST_URL . 'System/userinfo/id/',
            'openuser' => HOST_URL . 'System/userinfo/id/',
            'closestu' => HOST_URL . 'School/userdetail/id/',
            'openstu' => HOST_URL . 'School/userdetail/id/',
            'editinfo' => HOST_URL . 'System/userinfo/id/'
        ),
        'upload' => array(
            'classmates' => HOST_URL . 'School/classmate/id/',
            'teachers' => HOST_URL . 'School/teacher/schoolid/',
            'questions' => HOST_URL . 'Book/test/id/',
        ),
        'book' => array(
            'editbook' => HOST_URL . 'Book/editbook/id/',
            'savebook' => HOST_URL . 'Book/editbook/id/',
            'addquestion' => HOST_URL . 'Book/test/id/',
        ),
    )
);