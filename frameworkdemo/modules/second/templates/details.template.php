<?php
    $Details = $modelData['Second.Details']['Details'];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    账号：<?php echo $Details->get('username')?><br/>
    密码：<?php echo $Details->get('password')?><br/>
    编号：<?php echo $Details->get('id')?><br/>
</body>
</html>