<?php
use ORC\MVC\Action;
use ORC\API\Interior\Client\Connection;
use ORC\Core\Config;
/**
 * for test only!
 * @author pal
 *
 */
class Interior_Test_Action extends Action {
    public function execute()
    {
        $url = Config::getInstance()->get('interior.server_url');
        $connection = new Connection($url);
        $connection->usePost(false);
        pre($connection->get('knowledgepoint.get', array(), true));exit();
    }
}