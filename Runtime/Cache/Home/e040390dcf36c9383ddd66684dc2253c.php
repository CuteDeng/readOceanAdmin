<?php if (!defined('THINK_PATH')) exit();?><div class="row">
    <div class="col-sm-12">
        <div class="wrapper wrapper-content animated fadeInDown">

            <div class="ibox-content m-b-sm border-bottom">
                <div class="p-xs">
                    <div class="pull-left m-r-md">
                        <i class="fa fa-question text-navy mid-icon"></i>
                    </div>
                    <h2>分答管理</h2>
                    <span>统计问题回答情况</span>
                </div>
            </div>
            <div class="ibox-content forum-container">
                <?php if(is_array($list)): foreach($list as $key=>$vo): ?><a href="">
                        <div class="forum-item  <?php echo empty($vo['best'])?'':'active';?>">
                            <div class="row">
                                <div class="col-sm-9">
                                    <div class="forum-icon">
                                        <i class="fa fa-bookmark"></i>
                                    </div>
                                    <a href="/Post/postdetail/id/<?php echo ($vo["questionid"]); ?>/page/<?php echo ($_GET['p']); ?>"
                                       class="forum-item-title"><?php echo ($vo["topicname"]); ?></a>
                                    <div class="forum-sub-title">
                                        最佳答案: <?php echo empty($vo['best'])?'暂无':$vo['best'];?> </div>
                                </div>
                                <div class="col-sm-1 forum-info">
                                        <span class="views-number">
                                            <?php echo ($vo["posts"]); ?>
                                        </span>
                                    <div>
                                        <small>评论数</small>
                                    </div>
                                </div>
                                <div class="col-sm-1 forum-info">
                                        <span class="views-number">
                                            <?php echo $vo['thumbupnumbers']?$vo['thumbupnumbers']:0;?>
                                        </span>
                                    <div>
                                        <small>点赞数</small>
                                    </div>
                                </div>
                                <a href="/Post/deleltepost/id/<?php echo ($vo["questionid"]); ?>/page/<?php echo ($_GET['p']); ?>">
                                    <div class="col-sm-1 forum-info">
                                        <span class="views-number">
                                            <i class="fa fa-trash"></i>
                                        </span>
                                        <div>
                                            <small>删除</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div><?php endforeach; endif; ?>
                <div class="page-container">
                    <div class="page"><?php echo ($page); ?></div>
                    <div class="clear"></div>
                </div>
            </div>

        </div>
    </div>
</div>