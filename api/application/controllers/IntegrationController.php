<?php
class IntegrationController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function getAction()
    {
        if ($this->_hasParam("name")) {
            $integrationsModel = new Application_Model_Integrations();
            $name = $this->getRequest()->getParam("name");
            $dados = $integrationsModel->get($name);
            $dados['test_data'] = json_decode($dados['test_data']);
            $dados['production_data'] = json_decode($dados['production_data']);
            echo json_encode($dados);
        }
    }

    public function saveAction()
    {
        $integrationsModel = new Application_Model_Integrations();
        $dados = $this->getRequest()->getPost("Dados");
        $dados['test_data'] = json_encode($dados['test_data']);
        $dados['production_data'] = json_encode($dados['production_data']);
        $dados['status'] = intval($dados['status']);
        echo $integrationsModel->save($dados, $dados['name']);
    }
}
