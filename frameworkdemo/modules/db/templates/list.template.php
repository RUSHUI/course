<?php
$list = $modelData['DB.Test']['list'];
// pre($list);
?>
<table class="table table-hover">
  <tr>
    <th>编号</th>
    <th>key1</th>
    <th>key2</th>
    <th>计数器</th>
    <th>&nbsp;</th>
  </tr>
<?php foreach ($list as $row):?>
  <tr>
    <td><?php echo $row['id'];?>或者<?php echo $row->get('id');?></td>
    <td><?php echo $row->get('key1');?></td>
    <td><?php echo $row->get('key_2');?></td>
    <td><?php echo $row->get('count');?></td>
    <td><a href="<?php echo $this->generateURL('Db.Demo', array('action' => 'view', 'key1' => $row->get('key1')));?>">查看</a> &nbsp;&nbsp;
    <a href="<?php echo $this->generateURL('Db.Demo', array('action' => 'view2', $row->get('key_2')));?>">查看</a></td>
  </tr>
<?php endforeach;?>
</table>