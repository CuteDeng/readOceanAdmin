<?php if (!defined('THINK_PATH')) exit();?><div class="wrapper wrapper-content animated fadeInDown">
    <div class="ibox-content m-b-sm border-bottom">
        <div class="row">
            <div class="col-sm-11 p-xs">
                <div class="pull-left m-r-md">
                    <i class="fa fa-cogs text-navy mid-icon"></i>
                </div>
                <h2><?php echo ($title["title"]); ?>管理</h2>
                <span><?php echo ($title["subtitle"]); ?></span>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-8"></div>
                        <form action="/Book/test">
                            <div class="col-sm-3 pull-left">
                                <div class="input-group">
                                    <input type="text" name="bookname" placeholder="搜索书本" class="input-sm form-control"
                                           value="<?php echo ($_GET['bookname']); ?>">
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
                                    <?php if(is_array($type)): foreach($type as $key=>$to): ?><li><a href="/Book/test/type/<?php echo ($to["id"]); ?>"><?php echo ($to["name"]); ?></a></li><?php endforeach; endif; ?>
                                    <li class="divider"></li>
                                    <li><a href="/Book/test/type/0">显示全部</a></li>
                                </ul>
                            </div>

                        </div>
                    </div>
                    <div class="margin-top"></div>
                    <div style="min-height: 450px;">
                        <?php if($title['url'] == addquestion): ?><table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>序号</th>
                                    <th>书本名</th>
                                    <th>判断题数</th>
                                    <th>单选题数</th>
                                    <th>多选题数</th>
                                    <th>导入</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr>
                                        <td><?php echo ($key+1+$vo["p"]); ?></td>
                                        <td><?php echo ($vo["name"]); ?></td>
                                        <td><?php echo ($vo["judge"]); ?></td>
                                        <td><?php echo ($vo["single"]); ?></td>
                                        <td><?php echo ($vo["multiple"]); ?></td>
                                        <td>
                                            <!--<a class="text-navy" href="/Book/<?php echo ($title["url"]); ?>/id/<?php echo ($vo["id"]); ?>">编辑</a> |-->
                                            <a class="text-navy"
                                               href="/Book/<?php echo ($title["url"]); ?>/id/<?php echo ($vo["id"]); ?>">批量导入题库</a>
                                        </td>
                                    </tr><?php endforeach; endif; ?>

                                </tbody>
                            </table>
                            <?php else: ?>
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>序号</th>
                                    <th>书本名</th>
                                    <th>查看任务</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr>
                                        <td><?php echo ($key+1+$vo["p"]); ?></td>
                                        <td><?php echo ($vo["name"]); ?></td>
                                        <td><a class="text-navy" href="/Book/<?php echo ($title["url"]); ?>/id/<?php echo ($vo["id"]); ?>">编辑</a>
                                        </td>
                                    </tr><?php endforeach; endif; ?>

                                </tbody>
                            </table><?php endif; ?>

                        <div class="page-container">
                            <div class="page"><?php echo ($page); ?></div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>