<?php
/**
 * Created by PhpStorm.
 * User: Chan
 * Date: 17/3/18
 * Time: 下午10:27
 * Note:需要连接的日志配置
 */
return array(
    //需要记录日志的配置
    'WriteLog' => array(
//        控制器=>方法 lowercase
        'user' => array('login', 'logout', 'saveuser'),
        'school' => array(
            'saveclassname',
            'reset',
            'editinfo',
            'editteacher',
            'closegraduate',
            'opengraduate'),
        'role' => array(
            'roleEdit',
        ),
        'system' => array(
            'comment',
            'reset',
            'closeuser',
            'openuser',
            'closestu',
            'openstu',
        ),
        'post' => array('deleltepost',
            'delelteanswer',
            'deletecomment',)
    ),

    //日志记录组件的中文翻译
    'user' => array('checklogin' => '登录',//改版之后的登录方法
        'login' => '登录',//之前的登录方法
        'logout' => '退出登录',
        'saveuser' => '用户修改自己的用户名或密码'),
    'school' => array(
        'schoolinfo' => '进入学校管理页面',
        'school' => '进入学校管理页面',
        'saveclassname' => '修改年级中的班级信息,如班级名字修改、新增班级等',
        'addSchool' => '添加学校,新建学校基础配置',
        'addclass' => '添加班级',
        'addstu' => '添加学生',
        'upgrade' => '升级',
        'reset' => '重置学校学生、教师前台密码',
        'teacherdetail' => '老师详情',
        'editinfo' => '编辑学生信息',
        'editteacher' => '编辑老师信息',
        'closegraduate' => '禁用毕业生登录',
        'opengraduate' => '允许毕业生登录',
        'addteachers' => '添加老师',
        'addschool' => '添加学校',
    ),
    'role' => array(
        'roleAdd' => '添加后台角色',
        'roleEdit' => '后台角色权限设置',
    ),
    'system' => array('addusers' => '添加后台用户',
        'comment' => '评论意见反馈',
        'reset' => '重置后台用户密码',
        'closeuser' => '禁用后台用户',
        'openuser' => '启用后台用户',
        'closestu' => '禁用学生登录',
        'openstu' => '激活用户',
        'editinfo' => '修改后台用户信息',),
    'upload' => array(
        'classmates' => '批量上传班级学生',
        'teachers' => '批量上传学校教师',
        'questions' => '批量导入书籍题库',
    ),
    'book' => array(
        'editbook' => '编辑图书信息',
        'savebook' => '上传图书',
        'addquestion' => '上传题库',
        'tasks' => '书籍任务',
    ),
    'post' => array('deleltepost' => '删除帖子',
        'delelteanswer' => '删除讨论',
        'deletecomment' => '删除评论',)
);