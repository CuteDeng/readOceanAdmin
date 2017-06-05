<?php if (!defined('THINK_PATH')) exit();?><link href="<?php echo (ADMIN_CSS_URL); ?>plugins/zeromodel/zeroModal.css" rel="stylesheet">
<style>
    .result-error {
        color: red !important;
        transition: color 1s;
    }

    .result-tag {
        position: absolute;
        margin-top: -25px;
        margin-left: 70%;
        color: transparent;
    }
</style>
<div class="wrapper wrapper-content animated fadeInDown">
    <div class="ibox-content m-b-sm border-bottom">
        <div class="row">
            <div class="col-sm-10 p-xs">
                <div class="pull-left m-r-md">
                    <i class="fa fa-sitemap text-navy mid-icon"></i>
                </div>
                <h2>后台用户管理</h2>
                <span>添加后台用户,只有超级管理员才有权限添加后台管理员</span>
            </div>
        </div>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-4  form-group">
                <label>用户名</label>
                <input type="text" placeholder="用户名将作为登录账号" class="form-control" id="user" required><span
                    id='result'
                    class='result-tag control-label'>用户名已存在</span>
            </div>
            <div class="form-group col-lg-4">
                <label>昵称</label>
                <input type="text" placeholder="请输入您的昵称" class="form-control" id="name" required>
            </div>
            <div class="form-group col-lg-4">
                <div class="col-lg-6">
                    <label>管理员角色分类</label>
                    <select class="form-control col-sm-6" id="level1" name="role" required>
                        <option value="">请选择</option>
                        <?php if(is_array($role)): foreach($role as $key=>$vo): ?><option value="<?php echo ($vo["roleid"]); ?>"><?php echo ($vo["rolename"]); ?></option><?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="col-lg-6">
                    <label>&nbsp;</label>
                    <div id="zhanwei"></div>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="form-group col-lg-4">
                <label>密码</label>
                <input type="password" placeholder="请输入密码" class="form-control" id="pwd" required>
            </div>
            <div class="form-group col-lg-4">
                <label>确认密码</label>
                <input type="password" placeholder="请确认输入密码" class="form-control" id="pwds" required>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-lg-12">
                <button class="btn btn-sm btn-primary pull-right" id="sub_btn"><strong>添 加</strong></button>
            </div>
        </div>
    </div>
</div>
</div>
<script src="<?php echo (ADMIN_JS_URL); ?>encode/md5.js"></script>
<script src="<?php echo (ADMIN_JS_URL); ?>plugins/zeromodel/zeroModal.min.js"></script>

<script>
    $("#sub_btn").click(function () {
        var flag = 0;// 标识位
        var flag1 = 0;// 标识位
        if ($('#pwd').val() != $('#pwds').val()) {
            alert('两次输入密码不一致。请重新输入。');
        }
        else if ($('#pwd').val() == '' || $('#pwds').val() == '' || $('#user').val() == '' || $('#level1').val() == "") {
            zeroModal.error('所有选项不能为空。');
        }
        else if ($('#user').parent().hasClass('has-error')) {
            zeroModal.error('用户名已存在!给自己起一个独一无二的名字吧。');
        } else {
            flag = 1;
        }
        //二级联动菜单
        if ($('#level1').val() == '3' || $('#level1').val() == '5') {
            var tags;
            switch ($('#level1').val()) {
                case "3":
                    tags = $("#school").val();
                    break;
                case "5":
                    tags = $("#grade").val();
                    break;
            }
            if (tags == '') {
                zeroModal.error('请给学校系管理员选择对应学校或年级吧~');
            } else {
                flag1 = 1;
            }
        } else {
            flag1 = 1;
        }
        if (flag && flag1) {
            $.ajax({
                type: "post",
                url: '/System/addusers',
                data: {
                    usr: $('#user').val(),
                    username: $('#name').val(),
                    pwd: hex_md5($('#pwd').val()),
                    role: $('#level1').val(),
                    school: $('#school').val(),
                    grade: $('#grade').val(),
                },
                dataType: "json",
                success: function (data) {
                    switch (data['tag']) {
                        case '0':
                            zeroModal.success(data.msg);
                            break;
                        case '2':
                            zeroModal.error(data.msg);
                            break;
                        default:
                            zeroModal.error(data.msg);
                            break;

                    }
                },
                error: function (data) {
                    alert('网络错误');
                }
            });
        }
    });

    $(function () {
        $('#level1').change(function () {
            val = $(this).val();
            if (val == 3) {

                $.ajax({
                    type: "GET",
                    url: '/System/findSchool',
//                    data: {fid: '44'},
                    beforeSend: function () {
                        zeroModal.loading(6);
                    },
                    success: function (data) {
                        if (data['msg'] != false) {
                            $('#zhanwei').next().remove();
                            $('#zhanwei').after(data['str']);
                        } else {
                            $('#zhanwei').next().remove();
                        }
                        $('#school').change(function () {
                            if ($('#name').val() == "") {
                                $('#name').val($("#school option:selected").text() + '-学校管理员');
                            }
                        });
                        zeroModal.closeAll();
                    }
                });
            }
            //年级管理员
            else if (val == 5) {
                $.ajax({
                    type: "GET",
                    url: '/System/findGrade',
                    beforeSend: function () {
                        zeroModal.loading(6);
                    },
                    success: function (data) {
                        if (data['msg'] != false) {
                            $('#zhanwei').next().remove();
                            $('#zhanwei').after(data['str']);
                        } else {
                            $('#zhanwei').next().remove();
                        }
                        $('#grade').change(function () {
                            $('#name').val($("#grade option:selected").text() + '-年级管理员');
                        });
                        zeroModal.closeAll();
                    }
                });
            }
            else {
                $('#zhanwei').next().remove();
            }

        });
        $('#user').change(function () {
            $.ajax({
                type: "GET",
                url: '/System/checkName',
                data: {name: $('#user').val()},
                success: function (data) {
                    console.log(data);
                    if (data['tag'] == '1') {
                        if ($('#user').parent().hasClass('has-error')) {
                            $('#user').parent().removeClass('has-error');
                            $('#result').removeClass('result-error');
                        }
                    } else {
                        $('#user').parent().addClass('has-error');
                        $('#result').addClass('result-error');
                    }
                }
            });
        });

    })


</script>