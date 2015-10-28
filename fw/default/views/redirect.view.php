<?php
class Default_Redirect_View extends \ORC\MVC\View {
	/* (non-PHPdoc)
     * @see \ORC\MVC\View::execute()
     */
    public function execute()
    {
        $response = $this->getController()->getResponse();
        $redirect = $response->HTMLRedirect();
        $this->includeTemplate('Default.Redirect', array('redirect' => $redirect));
    }
}