<?php if (!defined('THINK_PATH')) exit();?><div class="wrapper wrapper-content animated fadeInDown">
    <div class="ibox-content m-b-sm border-bottom">
        <div class="row">
            <div class="col-sm-11 p-xs">
                <div class="pull-left m-r-md">
                    <i class="fa fa-sitemap text-navy mid-icon"></i>
                </div>
                <h2>后台用户管理</h2>
                <span>管理后台用户</span>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-lg-12 col-md-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-1 col-lg-1 col-md-1">
                            <a href="<?php echo U('System/addusr');?>">
                                <button type="button" class="btn btn-sm btn-primary">添加后台用户</button>
                            </a>
                        </div>
                        <div class="col-sm-6 col-lg-6 col-md-6">
                            <?php if($_GET['role'] == '1'): ?><span class="label label-primary">超级管理员</span>
                                <?php elseif($_GET['role'] == '2'): ?>
                                <span class="label label-success">管理员</span>
                                <?php elseif($_GET['role'] == '3'): ?>
                                <span class="label label-warning">学校管理员</span>
                                <?php elseif($_GET['role'] == '5'): ?>
                                <span class="label label-info">学校年级管理员</span>
                                <?php else: ?>
                                <span class="label label-info"><?php echo ($vo["rolename"]); ?></span><?php endif; ?>
                            <?php if($_GET['type'] == '1'): ?><span class="label label-success">正常</span>
                                <?php elseif($_GET['type'] == '2'): ?>
                                <span class="label label-danger">停用</span><?php endif; ?>
                        </div>
                        <div class="col-sm-3 col-lg-3 col-md-3">
                            <form action="/System/users" method="get">
                                <div class="input-group">
                                    <input type="text" placeholder="搜索管理员用户" class="input-sm form-control"
                                           name="username" value="<?php echo ($_GET['username']); ?>">
                                      <span class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-primary"><i
                                                class="fa fa-search"></i></button> </span>
                                </div>
                            </form>
                        </div>
                        <div class="col-sm-1 col-lg-1 col-md-1 pull-left">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle"
                                        data-toggle="dropdown">
                                    角色筛选 <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <?php if(is_array($tag)): foreach($tag as $key=>$vo): ?><li>
                                            <a href="<?php echo valueChange('role',$vo['roleid'])?>">只显示<?php echo ($vo["rolename"]); ?></a>
                                        </li><?php endforeach; endif; ?>

                                    <li class="divider"></li>
                                    <li><a href="<?php echo valueChange('role',0)?>">显示全部</a></li>
                                </ul>
                            </div>

                        </div>
                        <div class="col-sm-1 col-lg-1 col-md-1 pull-left">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle"
                                        data-toggle="dropdown">
                                    状态筛选 <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="<?php echo valueChange('type',2)?>">只显示禁用用户</a></li>
                                    <li><a href="<?php echo valueChange('type',1)?>">只显示激活用户</a></li>
                                    <li class="divider"></li>
                                    <li><a href="<?php echo valueChange('type',0)?>">显示全部</a></li>
                                </ul>
                            </div>

                        </div>

                    </div>
                    <div class="margin-top"></div>

                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>序号</th>
                            <th>用户名</th>
                            <th>登录账号</th>
                            <th>上次登录时间</th>
                            <th>角色</th>
                            <th>状态</th>
                            <th>管理 / 修改信息</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr>
                                <td><?php echo ($key+1); ?></td>
                                <!--<td><?php if($vo["role"] == '1'): ?>超级管理员<?php else: ?>普通管理员<?php endif; ?></td>-->
                                <td><?php echo ($vo["name"]); ?></td>
                                <td><?php echo ($vo["usr"]); ?></td>
                                <td><?php echo date('Y-m-d H:i:s',$vo['lasttime'])?></td>
                                <td>
                                    <?php if($vo["role"] == '1'): ?><span
                                            class="label label-primary"><?php echo ($vo["rolename"]); ?></span>
                                        <?php elseif($vo["role"] == '2'): ?>
                                        <span class="label label-success"><?php echo ($vo["rolename"]); ?></span>
                                        <?php elseif(($vo["role"] == '3') or ($vo["role"] == '5')): ?>
                                        <span class="label label-info"><?php echo ($vo["rolename"]); ?></span>
                                        <?php else: ?>
                                        <span class="label label-warning"><?php echo ($vo["rolename"]); ?></span><?php endif; ?>
                                </td>
                                <td>
                                    <?php if($vo["status"] == '0'): ?><span class="label label-success">正常</span>
                                        <?php else: ?>
                                        <span class="label label-danger">停用</span><?php endif; ?>
                                </td>
                                <td>
                                    <?php if($vo["status"] == '0'): ?><a class="text-navy"
                                                                         href="/System/closeuser/id/<?php echo ($vo['eid']); echo getRightUrl();?>">禁用 </a>
                                        <?php else: ?>
                                        <a href="/System/openuser/id/<?php echo ($vo['eid']); echo getRightUrl();?>"
                                           class="text-navy">激活 </a><?php endif; ?>
                                    / <a class="text-navy" href="/System/userinfo/id/<?php echo ($vo['eid']); ?>"> 修改信息</a></td>
                                <!--/ <a class="text-navy" href="/System/reset/id/<?php echo ($vo['eid']); ?>"> 重置密码</a>-->
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