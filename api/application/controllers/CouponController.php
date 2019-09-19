<?php
class CouponController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function validateAction()
    {
        if ($this->_hasParam("code")) {
            $couponModel = new Application_Model_Coupon();
            $code = $this->getRequest()->getParam("code");
            $info = $couponModel->getByCode($code);
            if ($info) {
                $info['code'] = utf8_encode(stripslashes($info['code']));
                echo json_encode($info);
            } else {
                echo "invalid";
            }
        }
    }

    public function listAction()
    {
        $couponModel = new Application_Model_Coupon();
        $list = $couponModel->getList();
        if ($list) {
            foreach ($list as $i => $coupon) {
                $list[$i]['code'] = utf8_encode(ucwords(strtolower(stripslashes($coupon['code']))));
            }
            echo json_encode($list);
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

    public function toggleAction()
    {
        if ($this->_hasParam("id")) {
            $couponModel = new Application_Model_Coupon();
            $id = $this->getRequest()->getParam("id");
            $flag = $this->getRequest()->getParam("flag");
            echo $couponModel->save(array("active" => $flag), $id);
        }
    }

    public function removeAction()
    {
        if ($this->_hasParam("id")) {
            $couponModel = new Application_Model_Coupon();
            $id = $this->getRequest()->getParam("id");
            echo $couponModel->exclude($id);
        }
    }

    public function createAction()
    {
        if ($this->_hasParam("code") && $this->_hasParam("value")) {
            $couponModel = new Application_Model_Coupon();
            echo $couponModel->create(array(
                "code" => utf8_decode(strip_tags(addslashes($this->getRequest()->getPost("code")))),
                "value" => floatval($this->getRequest()->getPost("value")),
                "active" => intval($this->getRequest()->getPost("active"))
            ));
        }
    }
}
