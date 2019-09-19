<?php

class LoginController extends Zend_Controller_Action {

    private $_login;
    private $_chave;
    private static $_salt;
    private static $_secaoConfig = "geral";

    public function init() {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/config.ini', self::$_secaoConfig); //Lê o arquivo de configuração
        try {
            self::$_salt = $config->salt;
        } catch (Zend_Exception $e) {
            echo $e->getMessage();
        }
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function indexAction() {
    }

    public function dologinAction() {
        $userModel = new Application_Model_User();
        $loginData = $this->getRequest()->getPost("loginData");
        $userData = $userModel->getByLoginAndPassword(strtolower($loginData['login']), $loginData['password']); // md5($loginData['password'].self::$_salt));
        if($userData) {
            $userData['name'] = utf8_encode($userData['name']);
            echo json_encode($userData);
        }
    }

    public function whloginAction() {
        $winehouseModel = new Application_Model_Winehouse();
        $loginData = $this->getRequest()->getPost("loginData");
        $winehouseData = $winehouseModel->getByLoginAndPassword(strtolower($loginData['login']), $loginData['password']); //md5($loginData['password'].self::$_salt));
        if($winehouseData) {
            $winehouseData->name = utf8_encode($winehouseData->name);
            $winehouseData->business_name = utf8_encode($winehouseData->business_name);
            $winehouseData->address = utf8_encode($winehouseData->address);
            $winehouseData->city = utf8_encode($winehouseData->city);
            $winehouseData->region = utf8_encode($winehouseData->region);
            $winehouseData->contact = utf8_encode($winehouseData->contact);
            echo json_encode($winehouseData);
        }
    }
}
	