<?php if (!defined('THINK_PATH')) exit();?><div class="wrapper wrapper-content animated fadeInDown">
    <div class="ibox-content m-b-sm border-bottom">
        <div class="row">
            <div class="col-sm-11 p-xs">
                <div class="pull-left m-r-md">
                    <i class="fa fa-sitemap text-navy mid-icon"></i>
                </div>
                <h2>用户意见反馈</h2>
                <span>管理并回复阅读海洋系统前台用户意见反馈信息</span>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-8"></div>
                        <form action="/System/opinion" type="get">
                        <div class="col-sm-3 pull-left">
                            <div class="input-group">
                                <input type="text" placeholder="按照用户名搜索前台用户反馈" class="input-sm form-control" name="name" value="<?php echo ($_GET['name']); ?>">
                                      <span class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-primary"><i
                                                class="fa fa-search"></i></button> </span>
                            </div>
                        </div>
                        </form>
                        <div class="col-sm-1">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle"
                                        data-toggle="dropdown">
                                    筛选 <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="/System/opinion/type/3">只显示待办</a></li>
                                    <li><a href="/System/opinion/type/2">显示已忽略</a></li>
                                    <li><a href="/System/opinion/type/1">显示已处理</a></li>
                                    <li class="divider"></li>
                                    <li><a href="/System/opinion/type/4">显示全部</a></li>
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
                            <th>反馈时间</th>
                            <th>意见内容</th>
                            <th>标记</th>
                            <th>查看详情</th>
                        </tr>
                        </thead>
                        <?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr>
                                <td><?php echo ($key+1+$vo["p"]); ?></td>
                                <td><?php echo ($vo["name"]); ?></td>
                                <td><?php echo ($vo["time"]); ?></td>
                                <td style="max-width: 300px"><?php echo(subtext($vo['content'],50));?></td>
                                <td>
                                    <?php if($vo["tag"] == '1'): ?><span class="label label-default">已处理</span>
                                        <?php elseif($vo["tag"] == '0'): ?>
                                        <span class="label  label-primary">待办</span>
                                        <?php elseif($vo["tag"] == '2'): ?>
                                        <span class="label  label-danger">已忽略</span><?php endif; ?>
                                </td>
                                <td><a class="text-navy"
                                       href="/System/opiniondetail/id/<?php echo ($vo["id"]); ?>/type/<?php echo ($_GET['type']); ?>">查看详情</a>
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