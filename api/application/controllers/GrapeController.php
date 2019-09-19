<?php
class GrapeController extends Zend_Controller_Action {
    public function init() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function listAction() {
        $grapeModel = new Application_Model_Grape();
        $list = $grapeModel->getList();
        foreach ($list as $i=>$grape) {
            $list[$i]->name = utf8_encode(ucwords(strtolower(stripslashes($grape->name))));
            $list[$i]->description = utf8_encode(ucwords(strtolower(stripslashes($grape->description))));
        }
        echo json_encode($list);
    }

    public function deleteAction() {
        $grapeModel = new Application_Model_Grape();
        $id = $this->getRequest()->getParam("id");
        echo $grapeModel->exclude($id);
    }

    public function createAction() {
        $grapeModel = new Application_Model_Grape();
        $name = trim($this->getRequest()->getPost("name"));
        $description = trim($this->getRequest()->getPost("description"));
        if (($name!="")) {
            $image = trim($this->getRequest()->getPost("id_image"));
            if (!is_numeric($image)) $image = null;
            $data = array(
                'name' => utf8_decode(strip_tags(addslashes($name))),
                'description' => utf8_decode(strip_tags(addslashes($description))),
                'id_image' => $image
            );
            $newGrape = $grapeModel->create($data);
            echo json_encode(array('id'=>$newGrape, 'name'=>$name, 'description'=>$description, 'id_image' => $image));
        } else {
            echo true;
        }
    }

    public function saveAction() {
        $grapeModel = new Application_Model_Grape();
        $id = $this->getRequest()->getParam("id");
        $name = trim($this->getRequest()->getPost("name"));
        $description = trim($this->getRequest()->getPost("description"));
        $image = trim($this->getRequest()->getPost("id_image"));
        if (!is_numeric($image)) $image = null;
        if (($id!="")&&($name!="")) {
            $data = array(
                'name' => utf8_decode(strip_tags(addslashes($name))),
                'description' => utf8_decode(strip_tags(addslashes($description))),
                'id_image' => $image
            );
            echo $grapeModel->save($data, $id);
        } else {
            echo true;
        }
    }
}
