<?php
class WebsiteController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function getAction()
    {
        if ($this->_hasParam("list")) {
            $websiteModel = new Application_Model_Website();
            $options = $this->getRequest()->getPost("list");
            $list = $websiteModel->getList($options);
            $return_list = array();
            foreach ($list as $option) {
                $return_list[$option['name']] = utf8_encode(stripslashes($option['value']));
                if (is_numeric($option['value'])&&(strpos($option['name'], 'shipment')===false)) {
                    $return_list[$option['name']] = Application_Model_Image::getPath($option['value']);
                }
            }
            echo json_encode($return_list);
        }
    }

    public function saveAction()
    {
        if ($this->_hasParam("Dados")) {
            $websiteModel = new Application_Model_Website();
            $dados = $this->getRequest()->getPost("Dados");
            foreach ($dados as $name => $value) {
                $value = utf8_decode(addslashes(nl2br($value)));
                $websiteModel->save($name, $value);
            }
        }
    }
}
