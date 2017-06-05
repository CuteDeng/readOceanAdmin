<?php if (!defined('THINK_PATH')) exit();?><style>
    .tag-selected {
        background: #1ab394 !important;
        color: white !important;
    }

    .bookimg {
        height: 250px;
        width: 175px;
    }
</style>
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-sm-3 animated fadeInLeft">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="file-manager">
                        <h2>书籍管理列表</h2>
                         <form action="/Book/booklist">
                                <div class="input-group">
                                    <?php if(empty($sname) == true): ?><input type="text" placeholder="按照书籍名称搜索书籍" name="name" class="input-sm form-control">
                                    <?php else: ?>
                                    <input type="text" value="<?php echo ($sname); ?>" name="name" class="input-sm form-control"><?php endif; ?>
                                      <span class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-primary"><i
                                                class="fa fa-search"></i></button>
                                                <a href="<?php echo U('book/booklist');?>"><button type="button" class="btn btn-sm btn-default"><i class="fa fa-times"></i></button></a> 
                                    </span>
                                </div>
                        </form>
                        <h5 class="tag-title">分类</h5>
                        <ul class="tag-list" style="padding: 0">
                            <li><a <?php if(empty($_GET['type'])){echo 'class="tag-selected"';}; ?>
                                href="<?php echo valueChange('type',0)?>">全部</a>
                            </li>
                            <?php if(is_array($type)): foreach($type as $key=>$vo): ?><li><a <?php if($vo['id']==$_GET['type']){echo 'class="tag-selected"';} ?>
                                    href="<?php echo valueChange('type',$vo['id'])?>"><?php echo ($vo["name"]); ?></a>
                                </li><?php endforeach; endif; ?>
                            <!---->
                        </ul>
                        <div class="clearfix"></div>
                        <h5 class="tag-title">适用年级</h5>
                        <ul class="tag-list" style="padding: 0;overflow: hidden">
                            <li><a <?php if(empty($_GET['suit'])){echo 'class="tag-selected"';}; ?>
                                href="<?php echo valueChange('suit',0)?>">全部</a>
                            </li>
                            <?php if(is_array($suits)): foreach($suits as $key=>$vo): ?><li><a <?php if($vo['id']==$_GET['suit']){echo 'class="tag-selected"';} ?>
                                    href="<?php echo valueChange('suit',$vo['id'])?>"><?php echo ($vo["name"]); ?></a>
                                </li><?php endforeach; endif; ?>
                            <!---->
                        </ul>
                        <div class="clearfix" style="margin-top: 20px"></div>
                        <div class="hr-line-dashed"></div>
                        <a href="<?php echo U('book/addbook');?>">
                            <button class="btn btn-primary btn-block">上传书籍</button>
                        </a>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-9 animated fadeInRight">
            <div class="row">
                <div class="col-sm-12">
                    <?php if(is_array($list)): foreach($list as $key=>$co): ?><div class="file-box">
                            <div class="file">
                                <a href="/Book/editbook/id/<?php echo ($co["id"]); ?>">
                                    <span class="corner"></span>
                                    <!--图片-->
                                    <div class="image">
                                        <img alt="<?php echo ($co["name"]); ?>" class="img-responsive bookimg"
                                             src="<?php echo ($co["picurl"]); ?>">
                                    </div>
                                    <!--书名-->
                                    <div class="file-name">
                                        <?php echo subtext($co['name'],10);?>
                                        <br/>
                                    </div>
                                </a>
                            </div>
                        </div><?php endforeach; endif; ?>
                </div>
            </div>
            <div class="page-container">
                <div class="page"><?php echo ($page); ?></div>
                <div class="clear"></div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('.file-box').each(function () {
            animationHover(this, 'pulse');
        });
    });
</script>