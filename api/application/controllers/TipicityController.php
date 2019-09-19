<?php
class TipicityController extends Zend_Controller_Action {
    public function init() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function listAction() {
        $tipicityModel = new Application_Model_Tipicity();
        $list = $tipicityModel->getList();
        foreach ($list as $i=>$tipicity) {
            $list[$i]->name = utf8_encode(ucwords(strtolower(stripslashes($tipicity->name))));
            $list[$i]->description = utf8_encode(ucwords(strtolower(stripslashes($tipicity->description))));
        }
        echo json_encode($list);
    }

    public function deleteAction() {
        $tipicityModel = new Application_Model_Tipicity();
        $id = $this->getRequest()->getParam("id");
        echo $tipicityModel->exclude($id);
    }

    public function createAction() {
        $tipicityModel = new Application_Model_Tipicity();
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
            $newTipicity = $tipicityModel->create($data);
            echo json_encode(array('id'=>$newTipicity, 'name'=>$name, 'description'=>$description, 'id_image' => $image));
        } else {
            echo true;
        }
    }

    public function saveAction() {
        $tipicityModel = new Application_Model_Tipicity();
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
            echo $tipicityModel->save($data, $id);
        } else {
            echo true;
        }
    }
}
