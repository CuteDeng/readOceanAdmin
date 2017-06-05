<?php if (!defined('THINK_PATH')) exit();?><div class="wrapper wrapper-content animated fadeInDown">
    <div class="ibox-content m-b-sm border-bottom">
        <div class="row">
            <div class="col-sm-11 p-xs">
                <div class="pull-left m-r-md">
                    <i class="fa  fa-address-card-o text-navy mid-icon"></i>
                </div>
                <h2>教师管理</h2>
                <span>管理学校教师用户信息</span>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-1">
                            <a href="/School/addteacher">
                                <button type="button" class="btn btn-sm btn-primary">导入单个老师</button>
                            </a>
                        </div>
                        <div class="col-sm-7"></div>
                        <form action="/School/teacher" method="get">
                            <div class="col-sm-2 pull-left">
                                <div class="input-group">
                                    <input type="text" placeholder="请输入学校全名" class="input-sm form-control" name="school"
                                           value="<?php echo ($_GET['school']); ?>">
                                </div>
                            </div>
                            <div class="col-sm-2 pull-left">
                                <div class="input-group">
                                    <input type="text" placeholder="请输入老师名" class="input-sm form-control" name="name"
                                           value="<?php echo ($_GET['name']); ?>">
                                      <span class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-primary"><i
                                                class="fa fa-search"></i></button> </span>
                                </div>
                            </div>
                        </form>

                    </div>
                    <div class="margin-top"></div>

                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>序号</th>
                            <th>教师用户</th>
                            <th>所属学校</th>
                            <th>执教班级</th>
                            <th>状态</th>
                            <th>管理/编辑</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr>
                                <td><?php echo ($key+1+$vo["p"]); ?></td>
                                <td><?php echo ($vo["name"]); ?></td>
                                <td><?php echo ($vo["schoolname"]); ?></td>
                                <td style="max-width: 300px"><?php echo ($vo["classname"]); ?></td>
                                <td>
                                    <?php if($vo["status"] == '1'): ?><span class="label label-success">正常</span>
                                        <?php else: ?>
                                        <span class="label label-danger">停用</span><?php endif; ?>
                                </td>
                                <td>
                                     <?php if($vo["status"] == '0'): ?><a class="text-navy" href="/School/openuser/id/<?php echo ($vo['userid']); ?>/usertype/<?php echo ($vo[type]); echo getRightUrl();?>">激活 </a>
                                        <?php else: ?>
                                        <a href="/School/closeuser/id/<?php echo ($vo['userid']); ?>/usertype/<?php echo ($vo[type]); echo getRightUrl();?>"
                                           class="text-navy">禁用 </a><?php endif; ?> /
                                    <a class="text-navy" href="/School/teacherdetail/id/<?php echo ($vo["userid"]); ?>">编辑</a>
                                </td>
                            </tr><?php endforeach; endif; ?>
                        </tbody>
                    </table>
                    <div class="page-container">
                        <div class="page"><?php echo ($page); ?></div>
                        <div class="clear"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>