<?php
class PackageController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function listAction()
    {
        $packageModel = new Application_Model_Package();
        $list = $packageModel->getList();
        foreach ($list as $i => $package) {
            $list[$i]['name'] = utf8_encode(ucwords(strtolower(stripslashes($package['name']))));
        }
        echo json_encode($list);
    }

    public function deleteAction()
    {
        $packageModel = new Application_Model_Package();
        $id = $this->getRequest()->getParam("id");
        echo $packageModel->exclude($id);
    }

    public function createAction()
    {
        $packageModel = new Application_Model_Package();
        $name = trim($this->getRequest()->getPost("name"));
        $size = intval($this->getRequest()->getPost("size"));
        $width = intval($this->getRequest()->getPost("width"));
        $height = intval($this->getRequest()->getPost("height"));
        $depth = intval($this->getRequest()->getPost("depth"));
        $weight = intval($this->getRequest()->getPost("weight"));
        if ($name != "") {
            $data = array(
                'name' => utf8_decode(strip_tags(addslashes($name))),
                'size' => utf8_decode(strip_tags(addslashes($size))),
                'width' => $width,
                'height' => $height,
                'depth' => $depth,
                'weight' => $weight,
            );
            $newPackage = $packageModel->create($data);
            echo json_encode(array('id' => $newPackage, 'name' => $name, 'size' => $size, 'weight' => $weight));
        } else {
            echo true;
        }
    }

    public function saveAction()
    {
        $packageModel = new Application_Model_Package();
        $id = $this->getRequest()->getParam("id");
        $name = trim($this->getRequest()->getPost("name"));
        $size = intval($this->getRequest()->getPost("size"));
        $width = intval($this->getRequest()->getPost("width"));
        $height = intval($this->getRequest()->getPost("height"));
        $depth = intval($this->getRequest()->getPost("depth"));
        $weight = intval($this->getRequest()->getPost("weight"));
        if (($id != "") && ($name != "")) {
            $data = array(
                'name' => utf8_decode(strip_tags(addslashes($name))),
                'size' => utf8_decode(strip_tags(addslashes($size))),
                'width' => $width,
                'height' => $height,
                'depth' => $depth,
                'weight' => $weight,
            );
            echo $packageModel->save($data, $id);
        } else {
            echo true;
        }
    }
}
