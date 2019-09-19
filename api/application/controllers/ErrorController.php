<?php

class ErrorController extends Zend_Controller_Action
{
    public function init(){
        
    }

    public function errorAction() {
        $errors = $this->_getParam('error_handler');
        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'You have reached the error page';
            return;
        }
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
            $this->getResponse()->setHttpResponseCode(404);
            $this->_forward("error404", null, null);
            break;
            default:
            $this->getResponse()->setHttpResponseCode(500);
            $this->_forward("error500", null, null, array('error'=>$errors->exception->getMessage()));
            break;
        }
    }

    public function error404Action() {

    }

    public function error500Action() {
        $this->view->message = $this->getRequest()->getParam("error");
    }
}
