<!DOCTYPE html>
<html lang="zh-CN">
  <head>
      <meta charset="UTF-8">
      <base href="http://localhost/offcn/courses/code/public_html/">
      <title><?php echo $title;?></title>
      
      <!-- 引入外部样式表 -->
      <?php echo $css;?>
      <!-- 引入css代码块 -->
      <?php if($raw_css):?>
        <style type="text/css">
          <?php echo $raw_css;?>
        </style>
      <?php endif;?>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
  </head>
  <body>
  <div class="wrapper-header">
      <header class="header e-offcn-header">
          <div class="head-product">
              <div class="user-info">
                  <ul>
                      <li class="btn-register hbox"><a href="register.php">注册</a></li>
                      <li class="btn-login hbox"><a onclick="login.php">登录</a></li>
                  </ul>
              </div>
          </div>
          <div class="head-logo">
              <a href="#">
                  <img src="./assets/imgs/icons/offcn_logo.jpg" alt="中公网校">
              </a>
          </div>
          <nav class="nav-list">
              <ul>
                  <li class="current" data-cmd="page_index"><a class="nav_name">首页</a></li>
                  <li data-cmd="page_course_apply"><a class="nav_name ">课程申请</a></li>
                  <li data-cmd="page_course_create"><a class="nav_name ">课程制作</a></li>
                  <li data-cmd="page_course_shop"><a class="nav_name">课程商品</a></li>
                  <li data-cmd="page_person_center" class="nav_list_last">
                      <a class="nav_name" href="javascript:alert('跳转个人中心页');">个人中心</a>
                  </li>

              </ul>
          </nav>
      </header>
  </div>


  <div class="offcn-lyt-ctn container" id="offcn-proj-wrap">
      <div class="row">
          <div class="col-lg-2">
              <?php if(!empty($menu)):?>
                  <?php echo $menu?>
              <?php endif;?>
          </div>
          <div class="col-lg-12"><p></p><?php echo $content?></div>
      </div>
  </div>

  <aside class="footer-mini-tools">
      <ul>
          <li class="feedback "><a href="javascript:void(0)">意见<br/>反馈</a></li>
          <li class="back-to-top"><a href="javascript:void(0)">返回<br/>顶部</a></li>
      </ul>
  </aside>
  <footer class="footer e-offcn-cpt">
      <div class="related-wbs">
          <a href="http://www.eoffcn.com" target="_blank" title="中公网校">中公网校</a> | <a href="http://www.eoffcn.com/help/cjwt/28628.html" target="_blank" rel="nofollow" title="听课流程">听课流程</a> | <a href="http://www.eoffcn.com/help/gmzf/28624.html" target="_blank" rel="nofollow" title="支付指南">支付指南</a> | <a href="http://www.eoffcn.com/help/cjwt/28630.html" target="_blank" rel="nofollow" title="播放问题">播放问题</a> | <a href="http://www.eoffcn.com/help/xsbd/28614.html" target="_blank" title="网校优势">网校优势</a> | <a href="http://www.eoffcn.com/kszx/wxdt/117856.html" target="_blank" title="诚聘英才">诚聘英才</a> |  <a href="http://union.offcn.com/" target="_blank" rel="nofollow" title="网络推广联盟">网络推广联盟</a> | <a href="http://www.eoffcn.com/help/fwbz/28632.html" target="_blank" rel="nofollow" title="服务帮助">服务帮助</a> | <a href="http://www.eoffcn.com/wzdt/" target="_blank" rel="nofollow" title="网站导航">网站导航</a>
      </div>
      <div class="copyright">Copyright©2000-2014 中公网校 .All Rights Reserved</div>
      <div class="hotline">全国统一咨询热线：4006999366 课程咨询请按1，学员服务请按2（工作时间：9:00-22:00节假日不休）</div>
      <div class="company">主办单位：北京中公未来教育咨询有限公司 合作QQ：2819065496 资源提供：<a href="./css/rs-font/demo.html">IcoMoon</a></div>
      <div class="icp">京ICP证100808号   京ICP备10218183号    京公网安备11010802009855号</div>
  </footer>

    <!-- 引入模板js和生成内联js代码 -->
    <?php echo $js;?>
    <?php if($raw_js):?>
      <script type="text/javascript">
        <?php echo $raw_js;?>
      </script>
    <?php endif;?>
    <script  type="text/javascript" src="./js/baseLib/require.js" data-main="./js/index"></script>
  </body>
</html>
