<?php
class KitController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function winehouselistAction()
    {
        if ($this->_hasParam("id")) {
            $kitModel = new Application_Model_Kit();
            $id = $this->getRequest()->getParam("id");
            $list = $kitModel->getWinehouseList($id);
            foreach ($list as $i => $kit) {
                $list[$i]->name = utf8_encode(ucwords(strtolower(stripslashes($kit->name))));
                $list[$i]->description = utf8_encode(ucwords(strtolower(stripslashes($kit->description))));
                $list[$i]->package_name = utf8_encode(ucwords(strtolower(stripslashes($kit->package_name))));
            }
            echo json_encode($list);
        }
    }

    public function getAction()
    {
        if ($this->_hasParam("id")) {
            $kitModel = new Application_Model_Kit();
            $id = $this->getRequest()->getParam("id");
            $kit = $kitModel->getById($id);
            $kit->name = utf8_encode(ucwords(strtolower(stripslashes($kit->name))));
            $kit->description = utf8_encode(ucwords(strtolower(stripslashes($kit->description))));
            $kit->package_name = utf8_encode(ucwords(strtolower(stripslashes($kit->package_name))));
            $kit->items = $kitModel->getItems($id);
            echo json_encode($kit);
        }
    }

    public function deleteAction()
    {
        if ($this->_hasParam("id")) {
            $kitModel = new Application_Model_Kit();
            $id = $this->getRequest()->getParam("id");
            echo $kitModel->exclude($id);
        }
    }

    public function createAction()
    {
        if ($this->_hasParam("name")) {
            $kitModel = new Application_Model_Kit();
            $newKit = $kitModel->create(array(
                'id_winehouse' => $this->getRequest()->getParam("id_winehouse"),
                'name' => utf8_decode(strip_tags(addslashes($this->getRequest()->getParam("name")))),
                'description' => utf8_decode(strip_tags(addslashes($this->getRequest()->getParam("description")))),
                'price' => $this->getRequest()->getParam("price"),
                'id_package' => $this->getRequest()->getParam("package_id")
            ));
            foreach ($this->getRequest()->getParam("items") as $item) {
                $item['id_product_kit'] = $newKit;
                $kitModel->addItem($item);
            }
            echo $newKit;
        }
    }

    public function updateAction()
    {
        if ($this->_hasParam("id")) {
            $kitModel = new Application_Model_Kit();
            $kitId = $this->getRequest()->getParam("id");
            $Kit = $kitModel->save(array(
                'id_winehouse' => $this->getRequest()->getParam("id_winehouse"),
                'name' => utf8_decode(strip_tags(addslashes($this->getRequest()->getParam("name")))),
                'description' => utf8_decode(strip_tags(addslashes($this->getRequest()->getParam("description")))),
                'price' => $this->getRequest()->getParam("price"),
                'id_package' => $this->getRequest()->getParam("package_id")
            ), $kitId);
            foreach ($this->getRequest()->getParam("items") as $item) {
                $item['id_product_kit'] = $kitId;
                $kitModel->addItem($item);
            }
            echo $Kit;
        }
    }
}
