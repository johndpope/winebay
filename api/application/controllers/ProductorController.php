<?php
class ProductorController extends Zend_Controller_Action {
    public function init() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function listAction() {
        $productorModel = new Application_Model_Productor();
        $list = $productorModel->getList();
        foreach ($list as $i=>$productor) {
            $list[$i]->name = utf8_encode(ucwords(strtolower(stripslashes($productor->name))));
            $list[$i]->description = utf8_encode(ucwords(strtolower(stripslashes($productor->description))));
            $list[$i]->address = utf8_encode(ucwords(strtolower(stripslashes($productor->address))));
            $list[$i]->phone = utf8_encode(ucwords(strtolower(stripslashes($productor->phone))));
            $list[$i]->email = utf8_encode(ucwords(strtolower(stripslashes($productor->email))));
        }
        echo json_encode($list);
    }

    public function deleteAction() {
        $productorModel = new Application_Model_Productor();
        $id = $this->getRequest()->getParam("id");
        echo $productorModel->exclude($id);
    }

    public function createAction() {
        $productorModel = new Application_Model_Productor();
        $name = trim($this->getRequest()->getPost("name"));
        $description = trim($this->getRequest()->getPost("description"));
        $address = trim($this->getRequest()->getPost("address"));
        $phone = trim($this->getRequest()->getPost("phone"));
        $email = trim($this->getRequest()->getPost("email"));
        if (($name!="")&&($address!="")&&($phone!="")) {
            $image = trim($this->getRequest()->getPost("id_image"));
            if (!is_numeric($image)) $image = null;
            $data = array(
                'name' => utf8_decode(strip_tags(addslashes($name))),
                'description' => utf8_decode(strip_tags(addslashes($description))),
                'address' => utf8_decode(strip_tags(addslashes($address))),
                'phone' => utf8_decode(strip_tags(addslashes($phone))),
                'email' => utf8_decode(strip_tags(addslashes($email))),
                'id_image' => $image
            );
            $newProductor = $productorModel->create($data);
            echo json_encode(array('id'=>$newProductor, 'name'=>$name));
        } else {
            echo true;
        }
    }

    public function saveAction() {
        $productorModel = new Application_Model_Productor();
        $id = $this->getRequest()->getParam("id");
        $name = trim($this->getRequest()->getPost("name"));
        $description = trim($this->getRequest()->getPost("description"));
        $address = trim($this->getRequest()->getPost("address"));
        $phone = trim($this->getRequest()->getPost("phone"));
        $email = trim($this->getRequest()->getPost("email"));
        $image = trim($this->getRequest()->getPost("id_image"));
        if (!is_numeric($image)) $image = null;
        if (($name!="")&&($address!="")&&($phone!="")) {
            $data = array(
                'name' => utf8_decode(strip_tags(addslashes($name))),
                'description' => utf8_decode(strip_tags(addslashes($description))),
                'address' => utf8_decode(strip_tags(addslashes($address))),
                'phone' => utf8_decode(strip_tags(addslashes($phone))),
                'email' => utf8_decode(strip_tags(addslashes($email))),
                'id_image' => $image
            );
            echo $productorModel->save($data, $id);
        } else {
            echo true;
        }
    }
}
