<?php
use ORC\MVC\Model;
use ORC\DBAL\DBAL;
use ORC\DAO\DaoFactory;
use ORC\DAO\Table\DataList;
//use ORC\Util\Pagination;
class Example_Demo_Model extends Model{
    public function UserDemo(){
//        //现在每个方法都互相影响结果 可注释后在看
//
//        $dbal = DBAL::select("user");
//        $dbal->setDataRowClass('\\APP\\Module\\Test\\DataRow\\Test');
//        //将数组排序
//        $dbal->orderBy('username desc');
//        //查询个别字段
//        $dbal->setSelect('username,password');
//        $list = $dbal->execute();
//        $this->set('list',$list);
//        //将$list的结果中以username作为数组键 password作为value 组成一个数组
//        $toArray = $list->toArray("username","password");
//        $this->set("toArray",$toArray);
//        //将$list的结果中usernanme字段的内容组成一个数组
//        $getByName = $list->getByName("username");
//        $this->set('getByName',$getByName);
//        //将$list的结果以password分组显示
//        $groupBy = $list->groupBy("password");
//        $this->set('groupBy',$groupBy);
//        //将$list的结果升序排序
//        $sort = $list->sort("id");
//        $this->set('sort',$sort);
//        //将$list的结果按指定数组排序
//        $array = array('zcs',"qwe");
//        $sortByfield = $list->sortByField("username",$array);
//        $this->set('sortByfield',$sortByfield);
//        //查找数组中的某个值所对定的数据
//        $find = $list->find("username","zcs");
//        $this->set('find',$find);
        //DAO　PDO
        $sql = "SELECT * FROM user WHERE id = ?";
        $dao = DaoFactory::get();//得到一个default的dao，也可以使用dbal的getDao方法
//        $dao->beginTransaction();
        $dao->prepare($sql);
        $id=1;
        $dao->bindParam(1,$id);
        $pdo = $dao->execute();
        $a = $dao->fetchAll($pdo);
        $this->set("pdo",$a);

    }
}
?>