<?php
class RegionController extends Zend_Controller_Action {
    public function init() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function listAction() {
        $regionModel = new Application_Model_Region();
        $list = $regionModel->getList();
        foreach ($list as $i=>$region) {
            $list[$i]->name = utf8_encode(ucwords(strtolower(stripslashes($region->name))));
            $list[$i]->description = utf8_encode(ucwords(strtolower(stripslashes($region->description))));
            $list[$i]->country_name = utf8_encode(ucwords(strtolower(stripslashes($region->country_name))));
        }
        echo json_encode($list);
    }

    public function deleteAction() {
        $regionModel = new Application_Model_Region();
        $id = $this->getRequest()->getParam("id");
        echo $regionModel->exclude($id);
    }

    public function createAction() {
        $regionModel = new Application_Model_Region();
        $name = trim($this->getRequest()->getPost("name"));
        $description = trim($this->getRequest()->getPost("description"));
        $id_country = trim($this->getRequest()->getPost("id_country"));
        if (($name!="")&&($id_country!="")) {
            $image = trim($this->getRequest()->getPost("id_image"));
            if (!is_numeric($image)) $image = null;
            $data = array(
                'name' => utf8_decode(strip_tags(addslashes($name))),
                'description' => utf8_decode(strip_tags(addslashes($description))),
                'id_country' => intval($id_country),
                'id_image' => $image
            );
            $newRegion = $regionModel->create($data);
            echo json_encode(array('id'=>$newRegion, 'name'=>$name, 'description'=>$description, 'id_country'=>$id_country, 'id_image'=>$image));
        } else {
            echo true;
        }
    }

    public function saveAction() {
        $regionModel = new Application_Model_Region();
        $id = $this->getRequest()->getParam("id");
        $name = trim($this->getRequest()->getPost("name"));
        $description = trim($this->getRequest()->getPost("description"));
        $id_country = trim($this->getRequest()->getPost("id_country"));
        $image = trim($this->getRequest()->getPost("id_image"));
        if (!is_numeric($image)) $image = null;
        if (($id!="")&&($name!="")&&($id_country!="")) {
            $data = array(
                'name' => utf8_decode(strip_tags(addslashes($name))),
                'description' => utf8_decode(strip_tags(addslashes($description))),
                'id_country' => intval($id_country),
                'id_image' => $image
            );
            echo $regionModel->save($data, $id);
        } else {
            echo true;
        }
    }
}
