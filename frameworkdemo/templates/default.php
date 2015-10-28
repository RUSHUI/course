<!DOCTYPE html>
<html lang="zh-CN">
  <head>
      <meta charset="UTF-8">
      <title><?php echo $title;?></title>
      <?php echo $js;?>
      <?php echo $css;?>
      <?php if($raw_css):?>
      <style>
        <?php echo $raw_css;?>
      </style>
      <?php endif;?>
      <?php if($raw_js):?>
      <script type="text/javascript">
        <?php echo $raw_js;?>
      </script>
      <?php endif;?>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="col-lg-2 sidebar">
        <?php if(!empty($menu)):?>
        <?php echo $menu?>
        <?php endif;?>
        <?php echo $left_side;?>
        </div>
        <div class="col-lg-10"><p></p><?php echo $content?></div> 
      </div>
      <div class="row"><?php echo $footer;?></div>
    </div>
  </body>
</html>