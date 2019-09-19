<?php

class Zend_View_Helper_Clubdata extends Zend_View_Helper_Abstract {

    function clubdata() {
        $clubData = array();
        $instance = Zend_Auth::getInstance();
        if ($instance->hasIdentity()) {
            $identity = $instance->getIdentity();
            $idClub = $identity['CLUB']['ID'];
            $clubModel = new Application_Model_Club();
            $userModel = new Application_Model_User();
            $cardModel = new Application_Model_Consumercard();
            $clubInfo = $clubModel->getInfo($idClub);
            // $players = $clubModel->getPlayersList($idClub);
            // if ($players){
            //     foreach ($players as $i=>$player) {
            //         $players[$i]['USER'] = array(
            //             'ID' => ucwords(utf8_encode($player['USER_ID'])),
            //             'NAME' => ucwords(utf8_encode($player['USER_NAME'])),
            //             'CPF' => $player['USER_CPF']
            //         );
            //         $players[$i]['CARD'] = $cardModel->getOpenCard($player['ID']);
            //     }
            //     $clubData['PLAYERS'] = $players;
            // }
            // $clubData['NEXT_CARD'] = $cardModel->getNextCard($idClub);
            $clubData['LOGO_PATH'] = $clubInfo['LOGO_PATH'];
            $clubData['NAME'] = $clubInfo['NAME'];
        }
        return $clubData;
    }
}
?>
