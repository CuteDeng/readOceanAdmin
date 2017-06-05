<?php if (!defined('THINK_PATH')) exit();?><div class="wrapper wrapper-content animated fadeInDown">
    <div class="ibox-content m-b-sm border-bottom">
        <div class="row">
            <div class="col-sm-11 p-xs">
                <div class="pull-left m-r-md">
                    <i class="fa fa-sliders text-navy mid-icon"></i>
                </div>
                <h2><?php echo ($title["title"]); ?>管理</h2>
                <span><?php echo ($title["subtitle"]); ?></span>
            </div>

        </div>
    </div>
    <?php if($title['title'] != '积分等级'): ?><div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-sm-1">
                                <?php if($title['title'] != 分类设置): ?><a href="<?php echo U('data/add'.$title['type']);?>">
                                        <button type="button" class="btn btn-sm btn-primary">添加<?php echo ($title["title"]); ?></button>
                                    </a><?php endif; ?>
                            </div>
                            <div class="col-sm-8"></div>
                            <form action="/Data/video">
                            <div class="col-sm-3 pull-left">
                                <div class="input-group">
                                    <input type="text" placeholder="搜索" name="name" class="input-sm form-control">
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
                            <?php if($title['title'] == '海洋生物'): ?><tr>
                                    <th>序号</th>
                                    <th>海洋生物名称</th>
                                    <th>分类</th>
                                    <th>兑换等级</th>
                                    <th>兑换积分</th>
                                    <th>编辑</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr>
                                    <td><?php echo ($key+1+$vo["p"]); ?></td>
                                    <td><?php echo ($vo["name"]); ?></td>
                                    <td><?php echo ($vo["type"]); ?></td>
                                    <td><?php echo ($vo["requiredlevel"]); ?></td>
                                    <td><?php echo ($vo["requiredscore"]); ?></td>
                                    <td><a class="text-navy" href="">编辑</a></td>
                                </tr><?php endforeach; endif; ?>
                            </tbody>
                            <?php elseif($title['title'] == '微课资源'): ?>
                            <tr>
                                <th>序号</th>
                                <th>微课课程</th>
                                <th>课程分类</th>
                                <th>推荐阅读阶段</th>
                                <th>编辑</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr>
                                    <td><?php echo ($key+1+$vo["p"]); ?></td>
                                    <td><?php echo ($vo["name"]); ?></td>
                                    <td><?php echo ($vo["type"]); ?></td>
                                    <td><?php echo ($vo["suit"]); ?>年级</td>
                                    <td><a class="text-navy" href="/Data/editvideo/id/<?php echo ($vo["id"]); ?>">编辑</a></td>
                                </tr><?php endforeach; endif; ?>
                            </tbody>
                            <?php elseif($title['title'] == '积分等级'): ?>
                            <tr>
                                <th>序号</th>
                                <th>等级称号</th>
                                <th>升级所需积分阈值</th>
                                <th>人数</th>
                                <th>编辑</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr>
                                    <td><?php echo ($key+1+$vo["p"]); ?></td>
                                    <td><?php echo ($vo["schoolname"]); ?></td>
                                    <td><?php echo ($vo["schoolid"]); ?></td>
                                    <td><?php echo ($vo["city"]); ?></td>
                                    <td><a class="text-navy" href="">编辑</a></td>
                                </tr><?php endforeach; endif; ?>
                            </tbody>
                            <?php elseif($title['title'] == '分类设置'): ?>
                            <tr>
                                <th>序号</th>
                                <th>分类</th>
                                <th>二级分类</th>
                                <th>编辑</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr>
                                    <td><?php echo ($key+1+$vo["p"]); ?></td>
                                    <td><?php echo ($vo["schoolname"]); ?></td>
                                    <td><?php echo ($vo["schoolid"]); ?></td>
                                    <td><?php echo ($vo["city"]); ?></td>
                                    <td><a class="text-navy" href="">编辑</a></td>
                                </tr><?php endforeach; endif; ?>
                            </tbody><?php endif; ?>

    </table>
    <div class="page-container">
        <div class="page"><?php echo ($page); ?></div>
        <div class="clear"></div>
    </div>
</div>
</div>

</div>
</div>

<?php else: ?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>等级阈值管理</h5>
                <div class="ibox-tools">

                    <!--<a href="<?php echo U('index/index');?>">-->
                    <!--<i class="fa fa-wrench"></i>-->
                    <!--</a>-->
                    <!--<ul class="dropdown-menu dropdown-user">-->
                    <!--<li><a href="table_basic.html#">设置</a>-->
                    <!--</li>-->
                    <!--<li><a href="table_basic.html#">选项2</a>-->
                    <!--</li>-->
                    <!--</ul>-->
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>等级</th>
                        <th>等级称号</th>
                        <th>该等级用户数</th>
                        <th>所需书本阅读数</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr>
                            <td><?php echo ($vo["rank"]); ?></td>
                            <td><input id="text_<?php echo ($vo["rank"]); ?>" type="text" class="form-control" style="max-width:200px"
                                       value="<?php echo ($vo["label"]); ?>"
                                       disabled="disabled"></td>
                            <td><?php echo ($vo["count"]); ?></td>
                            <td><input id="score_<?php echo ($vo["rank"]); ?>" type="text" class="form-control" style="max-width:200px"
                                       value="<?php echo ($vo["required"]); ?>"
                                       disabled="disabled"></td>
                            <!--<td><a class="text-navy" href="/Data/editgrade/id/<?php echo ($vo["rank"]); ?>">编辑</a></td>-->
                            <td>
                                <button type="button" class="btn btn-sm btn-warning pull-right"
                                        style="margin-right:10px " onclick="saveitem(<?php echo ($vo["rank"]); ?>)">保存修改
                                </button>
                                <button type="button" class="btn btn-sm btn-primary pull-right"
                                        style="margin-right:10px" onclick="edititem(<?php echo ($vo["rank"]); ?>)">修改
                                </button>
                            </td>
                        </tr><?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>最高分排行榜</h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>排名</th>
                        <th>用户名</th>
                        <th>所属学校</th>
                        <th>积分</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(is_array($data)): foreach($data as $key=>$so): ?><tr>
                            <td>第 <?php echo ($key+1); ?> 名</td>
                            <td><?php echo ($so["name"]); ?></td>
                            <td><?php echo ($so["schoolname"]); ?></td>
                            <td><?php echo ($so["totalintegral"]); ?></td>
                        </tr><?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div><?php endif; ?>

</div>
<script>

    function edititem(that) {
        var text = $('#text_' + that);//node节点
        var score = $('#score_' + that);//node节点
        text.removeAttr('disabled');
        score.removeAttr('disabled');
    }
    function saveitem(that) {
        var text = $('#text_' + that);//node节点
        var score = $('#score_' + that);//node节点
        if (text[0].hasAttribute('disabled') || score[0].hasAttribute('disabled')) {
            alert('未做任何更改');
        } else {
            $.ajax({
                url: "/Data/editscoregrade",
                type: "post",
                data: {'label': text.val(), 'score': score.val(), 'rank': that},
                success: function (data, status) {
                    console.log(data);
                    if (data.tag == 1) {
                        alert('修改成功');
                        text.attr('disabled', 'disabled');
                        score.attr('disabled', 'disabled');
                    } else {
                        alert('网络错误');
                    }
                },
                error: function () {
                    alert('网络错误');
                }
            })
        }
    }
</script>