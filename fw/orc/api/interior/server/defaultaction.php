<?php
namespace ORC\API\Interior\Server;
use ORC\MVC\Action;
abstract class DefaultAction extends Action implements IServer {
    protected $content_403 = array('result' => 'ERR', 'code' => '403');
    protected $content_404 = array('result' => 'ERR', 'code' => '404');

    /* (non-PHPdoc)
     * @see \ORC\API\Interior\Server\IAction::sendContent()
     */
    public function sendContent(array $data)
    {
        $request = $this->getRequest();
        if ($request->get('gzip')) {
            $data = gzcompress(json_encode($data));
            return $this->RAWReturn($data);
        }
        return $this->JSONReturn($data);
    }
    
    public function pre_execute() {
        if (!$this->auth()) {
            return $this->sendContent($this->content_403);
        }
    }
}