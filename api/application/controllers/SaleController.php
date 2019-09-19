<?php
class SaleController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function winehouselistAction()
    {
        if ($this->_hasParam("id")) {
            $saleModel = new Application_Model_Sale();
            $id = $this->getRequest()->getParam("id");
            $list = $saleModel->getWinehouseList($id);
            foreach ($list as $i => $sale) {
                $list[$i]->product = utf8_encode(ucwords(strtolower(stripslashes($sale->product))));
                $list[$i]->package_name = utf8_encode(ucwords(strtolower(stripslashes($sale->package_name))));
            }
            echo json_encode($list);
        }
    }

    public function deleteAction()
    {
        if ($this->_hasParam("id")) {
            $saleModel = new Application_Model_Sale();
            $id = $this->getRequest()->getParam("id");
            echo $saleModel->exclude($id);
        }
    }

    public function createAction()
    {
        if ($this->_hasParam("id_winehouse_product")) {
            $saleModel = new Application_Model_Sale();
            echo $saleModel->create(array(
                'id_winehouse_product' => $this->getRequest()->getParam("id_winehouse_product"),
                'id_package' => $this->getRequest()->getParam("package_id"),
                'price' => $this->getRequest()->getParam("price"),
                'active' => intval($this->getRequest()->getParam("active")),
            ));
        }
    }

    public function saveAction()
    {
        if ($this->_hasParam("id")) {
            $saleModel = new Application_Model_Sale();
            echo $saleModel->update(array(
                'id_package' => $this->getRequest()->getParam("package_id"),
                'price' => $this->getRequest()->getParam("price"),
                'active' => intval($this->getRequest()->getParam("active")),
            ), "id=" . $this->getRequest()->getParam("id"));
        }
    }

    public function stopAction()
    {
        if ($this->_hasParam("id")) {
            $saleModel = new Application_Model_Sale();
            $id = $this->getRequest()->getParam("id");
            echo $saleModel->update(array('active' => 0), "id=$id");
        }
    }

    public function startAction()
    {
        if ($this->_hasParam("id")) {
            $saleModel = new Application_Model_Sale();
            $id = $this->getRequest()->getParam("id");
            echo $saleModel->update(array('active' => 1), "id=$id");
        }
    }
}
