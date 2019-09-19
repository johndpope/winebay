<?php

class Zend_View_Helper_Userslist extends Zend_View_Helper_Abstract {

    function userslist() {
        $userModel = new Application_Model_User();
        $usersList = array();
        foreach ($userModel->getAll() as $i=>$user){
            $usersList[] = array(
                'id' => $user['ID'],
                'name' => utf8_encode($user['NAME']),
                'cpf' => ($user['CPF'])?$user['CPF']:"",
                'mail' => $user['EMAIL']
            );
        }
        return $usersList;
    }
}
?>
