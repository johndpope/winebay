<?php
class EstoqueController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function getentriesAction()
    {
        if ($this->_hasParam("Produto")) {
            $estoqueModel = new Application_Model_Estoque();
            $busca = array(
                'date_start' => implode('-', array_reverse(explode('/', $this->getRequest()->getPost("Inicio")))),
                'date_end' => implode('-', array_reverse(explode('/', $this->getRequest()->getPost("Fim")))),
                'id_winehouse_product' => $this->getRequest()->getPost("Produto")
            );
            $list = $estoqueModel->getEntries($busca);
            foreach ($list as $i => $entry) {
                $list[$i]->product_name = utf8_encode(ucwords(strtolower(stripslashes($entry->product_name))));
                $list[$i]->description = utf8_encode(ucwords(strtolower(stripslashes($entry->description))));
                $list[$i]->is_manual = boolval($entry->is_manual);
            }
            echo json_encode($list);
        }
    }

    public function addentryAction() {
        if ($this->_hasParam("id_winehouse_product")) {
            $estoqueModel = new Application_Model_Estoque();
            $entryData = array(
                'id_winehouse_product' => $this->getRequest()->getPost("id_winehouse_product"),
                'quantity' => $this->getRequest()->getPost("quantity"),
                'description' => utf8_decode(strip_tags(addslashes($this->getRequest()->getPost("description")))),
                'is_manual' => $this->getRequest()->getPost("is_manual")
            );
            echo $estoqueModel->addEntry($entryData);
        }
    }
}
