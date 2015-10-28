<?php
namespace ORC\API\Interior\Client;
use ORC\MVC\Action;
/**
 * 由于几乎不可能在client的action中调用，所以这个方法废弃
 * @author pal
 * @deprecated
 */
abstract class DefaultAction extends Action implements IAction {
    /* (non-PHPdoc)
     * @see \ORC\API\Interior\Client\IAction::getContent()
     */
    public function getContent($url, array $params = array(), $gzip = false)
    {
        $connection = new Connection($url);
        return $connection->get('', $params, $gzip);
    }

    
}