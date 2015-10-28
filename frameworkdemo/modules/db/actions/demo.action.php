<?php
use ORC\MVC\Action;
use ORC\DBAL\DBAL;
use ORC\DAO\DaoFactory;
use ORC\Util\Pagination;
class DB_Demo_Action extends Action {
    public function execute() {
        $request = $this->getRequest();
        //注意！为了演示dao和dbal的用法，把所有代码放在这里，但实际上要利用model
        switch ($request->get('action')) {
            case 'add':
                return $this->HTMLView('DB.Add');
                break;
            case 'save':
                $key1 = $request->get('key1');
                $key2 = $request->get('key2');
                $dbal = DBAL::insert('test');
                $dbal->set('key1', $key1);
                $dbal->set('key_2', $key2);
                //$dbal->setKey2($key2);//两种写法都可以，推荐用上面的
                $id = $dbal->execute();
                return $this->HTMLRedirect($this->generateURL('DB.Demo'), '保存成功', '页面保存成功');
                break;
            case 'view2':
                //利用key2作为主键
                $key2 = $request->get(0, 'safe', '');//注意request的get的用法
                $dao = DaoFactory::get('default');//得到一个default的dao，也可以使用dbal的getDao方法
                $dao->beginTransaction();//开始一个事务, 注意这里select没有加锁（select for update)
                $dbal = DBAL::select('default.test');//default是配置文件里的server name，不是database name
                $dbal->setDataRowClass('\\APP\\Module\\Test\\DataRow\\Test');//指定datarow class，否则使用ORC\DAO\Table\DataRow
                $dbal->byKey2($key2);//注意虽然在数据库中字段是key_2，这里不要有_
                $row = $dbal->getOne();
                //给计数器加1
                $dbal = DBAL::update('test');
                $dbal->increase('count', 1);
                $dbal->execute();
                $dao->commit();//提交
                $model = $this->getModel('DB.Test');
                $model->set('record', $row);
                return $this->HTMLView('DB.ViewDemo');
                break;
            case 'view':
                $id = $request->get('id', 'posint', 0);
                $dbal = DBAL::select('default.test');//default是配置文件里的server name，不是database name
                $dbal->setDataRowClass('\\APP\\Module\\Test\\DataRow\\Test');//指定datarow class，否则使用ORC\DAO\Table\DataRow
                $dbal->byId($id);
                $result = $dbal->getOne();//直接得到一个DataRow，这里直接得到Test对象
                $model = $this->getModel('DB.Test');
                $model->set('record', $result);
                return $this->HTMLView('DB.ViewDemo');
                break;
            case 'list':
            default:
                $dbal = DBAL::select('test');//获得一个dbal select对象
                $dbal->setDataRowClass('\\APP\\Module\\Test\\DataRow\\Test');//指定datarow class，否则使用ORC\DAO\Table\DataRow
                $list = $dbal->execute();
                $model = $this->getModel('DB.Test');
                $model->set('list', $list);
                //如果要分页的话
                $dbal = DBAL::select('test');//获得一个dbal select对象
                $dbal->setDataRowClass('\\APP\\Module\\Test\\DataRow\\Test');//指定datarow class，否则使用ORC\DAO\Table\DataRow
                $dbal->setPage(1, 20);//第一页，每页20条
                $list = $dbal->execute();
                $pagination = new Pagination($list, $dbal->getTotalCount(), 1, 20);
                return $this->HTMLView('DB.List');
                break;
        }
    }
}