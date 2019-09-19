<?php
class NewsletterController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function getlistAction()
    {
        $newsletterModel = new Application_Model_Newsletter();
        $dados = $newsletterModel->getList();
        echo json_encode($dados);
    }

    public function addAction()
    {
        if ($this->_hasParam("name") && $this->_hasParam("email")) {
            $newsletterModel = new Application_Model_Newsletter();
            $name = utf8_encode(strip_tags(addslashes(trim($this->getRequest()->getPost("name")))));
            $email = utf8_encode(strip_tags(addslashes(trim($this->getRequest()->getPost("email")))));

            if (!$newsletterModel->check($email)) {
                echo $newsletterModel->save($name, $email);
            } else {
                echo 'exists';
            }
        }
    }
}
