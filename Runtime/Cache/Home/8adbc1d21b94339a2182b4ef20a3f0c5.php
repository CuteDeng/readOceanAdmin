<?php if (!defined('THINK_PATH')) exit();?><div class="wrapper wrapper-content animated fadeInDown">
    <div class="ibox-content m-b-sm border-bottom">
        <div class="row">
            <div class="col-sm-11 p-xs">
                <div class="pull-left m-r-md">
                    <i class="fa fa-cogs text-navy mid-icon"></i>
                </div>
                <h2>年级管理</h2>
                <span>管理学校年级和升学管理</span>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-1">
                            <!--<a href="/School/addgrade"><button type="button" class="btn btn-sm btn-primary">添加年级</button></a>-->
                        </div>
                        <div class="col-sm-8">
                            <?php if(is_array($go)): foreach($go as $key=>$grade): ?><li><a <?php if($vo['id']==$_GET['type']){echo 'class="tag-selected"';} ?>
                                    href="<?php echo valueChange('grades',$go['gradename'])?>"><?php echo ($go["gradename"]); ?></a>
                                </li><?php endforeach; endif; ?>
                        </div>
                        <form action="/School/grade" method="get">
                            <div class="col-sm-3 pull-left">
                                <div class="input-group">
                                    <input type="text" placeholder="搜索学校全称" class="input-sm form-control"
                                           name="schoolname" value="<?php echo ($_GET['schoolname']); ?>">
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
                            <th>学校名称</th>
                            <th>年级</th>
                            <th>该年级下班级数</th>
                            <th>管理</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr>
                                <td><?php echo ($key+1+$vo["p"]); ?></td>
                                <td><?php echo ($vo["schoolname"]); ?></td>
                                <td><?php echo ($vo["gradename"]); ?></td>
                                <td><?php echo getGradeNum($vo['gradeid'])?></td>
                                <td><a class="text-navy" href="/School/klass/gid/<?php echo ($vo["gradeid"]); ?>">管理对应年级班级</a> / <a
                                        class="text-navy" href="/School/editgrade/id/<?php echo ($vo["gradeid"]); ?>">编辑</a></td>
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