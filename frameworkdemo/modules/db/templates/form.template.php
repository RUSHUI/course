<?php
?>
<form method="post" action="<?php echo $this->generateURL('DB.Demo', array('action' => 'save'));?>">
<div class="form-group">
  <label for="key1">第一个值</label>
  <input type="text" name="key1" id="key1" class="form-control" placeholder="输入第一个值" />
</div>
<div class="form-group">
  <label for="key2">第二个值</label>
  <input type="text" name="key2" id="key2" class="form-control" placeholder="输入第二个值" />
</div>
<div class="form-group">
  <input type="submit" value="保存" class="btn btn-primary" />
</div>
</form>