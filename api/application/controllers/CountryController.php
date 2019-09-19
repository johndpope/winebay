<?php
class CountryController extends Zend_Controller_Action {
    public function init() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function listAction() {
        $countryModel = new Application_Model_Country();
        $list = $countryModel->getList();
        foreach ($list as $i=>$country) {
            $list[$i]->name = utf8_encode(ucwords(strtolower(stripslashes($country->name))));
        }
        echo json_encode($list);
    }

    public function deleteAction() {
        $countryModel = new Application_Model_Country();
        $id = $this->getRequest()->getParam("id");
        echo $countryModel->exclude($id);
    }

    public function createAction() {
        $countryModel = new Application_Model_Country();
        $name = trim($this->getRequest()->getPost("name"));
        $shortname = trim($this->getRequest()->getPost("shortname"));
        if ($name!="") {
            $image = trim($this->getRequest()->getPost("image"));
            if (!is_numeric($image)) $image = null;
            $data = array('name' => utf8_decode(strip_tags(addslashes($name))), 'shortname' => utf8_decode(strip_tags(addslashes($shortname))), 'id_image'=>$image);
            $newCountry = $countryModel->create($data);
            echo json_encode(array('id'=>$newCountry, 'name'=>$name, 'id_image'=>$image));
        } else {
            echo true;
        }
    }

    public function saveAction() {
        $countryModel = new Application_Model_Country();
        $id = $this->getRequest()->getParam("id");
        $name = trim($this->getRequest()->getPost("name"));
        $shortname = trim($this->getRequest()->getPost("shortname"));
        $image = trim($this->getRequest()->getPost("id_image"));
        if (!is_numeric($image)) $image = null;
        if (($id!="")&&($name!="")) {
            $data = array('name' => utf8_decode(strip_tags(addslashes($name))),'shortname' => utf8_decode(strip_tags(addslashes($shortname))), 'id_image'=>$image);
            echo $countryModel->save($data, $id);
        } else {
            echo true;
        }
    }
}
