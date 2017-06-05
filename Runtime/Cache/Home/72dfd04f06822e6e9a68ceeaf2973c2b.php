<?php if (!defined('THINK_PATH')) exit();?><div class="footer">
    <!--<div class="pull-right">-->
    <!--Powered By：<a href="http://itc.bnuz.edu.cn" target="_blank">北京师范大学珠海分校</a>-->
    <!--</div>-->
    <div style="text-align: center">
        <strong>Copyright</strong> 阅读海洋 &copy; 2016-2017
    </div>
</div>
</div>


<script src="<?php echo (ADMIN_JS_URL); ?>bootstrap.min.js?v=3.3.6"></script>
<script src="<?php echo (ADMIN_JS_URL); ?>content.min.js?v=1.0.0"></script>
<script src="<?php echo (ADMIN_JS_URL); ?>plugins/flot/jquery.flot.js"></script>
<script src="<?php echo (ADMIN_JS_URL); ?>plugins/flot/jquery.flot.tooltip.min.js"></script>
<script src="<?php echo (ADMIN_JS_URL); ?>plugins/flot/jquery.flot.resize.js"></script>
<script src="<?php echo (ADMIN_JS_URL); ?>plugins/chartJs/Chart.min.js"></script>
<!--<script src="<?php echo (ADMIN_JS_URL); ?>plugins/peity/jquery.peity.min.js"></script>-->
<!--<script src="<?php echo (ADMIN_JS_URL); ?>demo/peity-demo.min.js"></script>需要用的时候再加-->
<script type="text/javascript">
    $(document).ready(function () {
        if ($('.wrapper').height() < $('body').height()) {
            $(".footer").addClass('navbar-fixed-bottom');
        }
    });
</script>
</body>

</html>