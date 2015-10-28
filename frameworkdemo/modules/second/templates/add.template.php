<?php
//$list = $modelData['Second.User']['list'];
$pagination = $modelData['Second.User']['patgination'];
$list = $pagination->getDataList();
?>
<table class="table table-hover">
    <tr>
        <th>id</th>
        <th>用户名</th>
        <th>查看</th>
    </tr>
<?php foreach ($list as $row):?>
    <tr>
        <td><?php echo $row['id'];?></td>
        <td><?php echo $row['username'];?></td>
        <td><a href="<?php echo $this->generateURL('Second.Demo',array("action"=>"details","uid"=>$row['id']))?>">查看</td>
    </tr>
<?php endforeach?>
</table>
<?php echo $pagination->toHTML('Second.Demo',array("action"=>"list"))?>
