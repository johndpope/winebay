<?php
class ImporterController extends Zend_Controller_Action {
    public function init() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function listAction() {
        $importerModel = new Application_Model_Importer();
        $list = $importerModel->getList();
        foreach ($list as $i=>$importer) {
            $list[$i]->name = utf8_encode(ucwords(strtolower(stripslashes($importer->name))));
            $list[$i]->description = utf8_encode(ucwords(strtolower(stripslashes($importer->description))));
            $list[$i]->address = utf8_encode(ucwords(strtolower(stripslashes($importer->address))));
            $list[$i]->phone = utf8_encode(ucwords(strtolower(stripslashes($importer->phone))));
            $list[$i]->email = utf8_encode(ucwords(strtolower(stripslashes($importer->email))));
        }
        echo json_encode($list);
    }

    public function deleteAction() {
        $importerModel = new Application_Model_Importer();
        $id = $this->getRequest()->getParam("id");
        echo $importerModel->exclude($id);
    }

    public function createAction() {
        $importerModel = new Application_Model_Importer();
        $name = trim($this->getRequest()->getPost("name"));
        $description = trim($this->getRequest()->getPost("description"));
        $address = trim($this->getRequest()->getPost("address"));
        $phone = trim($this->getRequest()->getPost("phone"));
        $email = trim($this->getRequest()->getPost("email"));
        if ($name!="") {
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
            $newImporter = $importerModel->create($data);
            echo json_encode(array('id'=>$newImporter, 'name'=>$name));
        } else {
            echo true;
        }
    }

    public function saveAction() {
        $importerModel = new Application_Model_Importer();
        $id = $this->getRequest()->getParam("id");
        $name = trim($this->getRequest()->getPost("name"));
        $description = trim($this->getRequest()->getPost("description"));
        $address = trim($this->getRequest()->getPost("address"));
        $phone = trim($this->getRequest()->getPost("phone"));
        $email = trim($this->getRequest()->getPost("email"));
        $image = trim($this->getRequest()->getPost("id_image"));
        if (!is_numeric($image)) $image = null;
        if ($name!="") {
            $data = array(
                'name' => utf8_decode(strip_tags(addslashes($name))),
                'description' => utf8_decode(strip_tags(addslashes($description))),
                'address' => utf8_decode(strip_tags(addslashes($address))),
                'phone' => utf8_decode(strip_tags(addslashes($phone))),
                'email' => utf8_decode(strip_tags(addslashes($email))),
                'id_image' => $image
            );
            echo $importerModel->save($data, $id);
        } else {
            echo true;
        }
    }
}
