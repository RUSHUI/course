<?php
switch($redirect->getType()) {
    case \ORC\APP\Response\Redirect::TYPE_ERROR:
        $css_class = 'alert alert-danger';
        break;
    case \ORC\APP\Response\Redirect::TYPE_FAILURE:
        $css_class = 'alert alert-info';
        break;
    case \ORC\APP\Response\Redirect::TYPE_WARNING:
        $css_class = 'alert alert-warning';
        break;
    default:
    case \ORC\APP\Response\Redirect::TYPE_SUCCESS:
        $css_class = 'alert alert-success';
        break;
} 
?>
<div class="<?php echo $css_class;?>">
  <div id="redirect_title"><?php echo $redirect->getTitle();?></div>
  <div id="redirect_message"><?php echo $redirect->getMessage();?></div>
  <div>页面将在3秒后自动跳转，您也可以点击<a href="<?php echo $redirect->getURL();?>" class="alert-link">这里</a>立即跳转</div>
</div>
<script type="text/javascript">
setTimeout("javascript:location.href='<?php echo $redirect->getURL();?>'", 3000); 
</script>
