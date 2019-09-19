<?php
class IndexController extends Zend_Controller_Action {
    public function init() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function indexAction() {
        echo "Silence is golden. Erlan Carreira";
    }
}
