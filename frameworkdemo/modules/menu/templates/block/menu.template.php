<?php
?>
<div class="list-group">
  <a href="<?php echo $this->generateURL();?>" class="list-group-item">首页</a>
  <a href="<?php echo $this->generateURL('DB.Demo');?>" class="list-group-item">数据库操作</a>
  <a href="<?php echo $this->generateURL('Second.Demo');?>" class="list-group-item">注册</a>
  <a href="<?php echo $this->generateURL('Second.Demo',array('action'=>"list"));?>" class="list-group-item">查看用户</a>
</div>
