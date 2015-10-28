<?php
$action = $action_info['obj'];
$action_file = $action_info['filename'];
$view = $view_info['obj'];
$view_file = $view_info['filename'];
?>
<script type="text/javascript">
<!--
$(document).ready(function(){
	$('#showSource').click(function(){
		$('#sourceDiv').toggle();
	});
	//$("pre.sourceCode").snippet("html");
});
//-->
</script>
<a href="#" id="showSource">显示源代码</a>
<div id="sourceDiv" class="row" style="display: none">
  <div class="col-md-6">
    <h4>Action：<b><?php echo $action->getName();?></b></h4>
    <pre class="sourceCode"><?php echo str_replace(array('<', '>'), array('&lt', '&gt'), file_get_contents($action_file));?></pre>
  </div>
  <div class="col-md-6">
    <h4>View：<b><?php echo $view->getName();?></b></h4>
    <pre class="sourceCode"><?php echo str_replace(array('<', '>'), array('&lt', '&gt'), file_get_contents($view_file));?></pre>
  </div>
</div>