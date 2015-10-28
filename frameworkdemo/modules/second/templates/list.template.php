<!doctype html>
<html lang='en'>
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
	<form action="<?php echo $this->generateURL('Second.Demo',array('action'=>"insert"))?>" method="POST">
		用户名：<input type="text" value="" name="username" /><br/>
		密码：<input type="password" value="" name="password" /><br/>
		<input type="submit" name="sub" value="提交" >
	</form>
</body>
</html>
