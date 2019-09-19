<?php
class Application_Model_Example extends Zend_Db_Table_Abstract {

    protected $_name = "table.name";
    protected $_id = "ID";

    public function create(array $data) {
        try {
            $data = $this->filterColumns($data);
            if ($data) {
                $lastId = $this->insert($data);
                return $lastId;
            }
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getByLoginAndPassword($login, $password) {
        try {
            return $this->fetchRow();
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getByCardID($cardID) {
       try {
           $connection = Zend_Db_Table::getDefaultAdapter();
           $data = $connection->fetchRow("SELECT user.*, player.ID as PLAYER_ID FROM  user user, player player, consumer_card card WHERE user.ID = player.ID_USER AND player.ID = card.ID_PLAYER AND card.ID = $cardID");
           $connection->closeConnection();
           return $data;
       } catch (Zend_Db_Table_Exception $e) {
           echo $e->getMessage();
       }
   }

    protected function filterColumns(array $data) {
        $cols = $this->info('cols');
        $filterData = array();
        if ($data) {
            foreach ($data as $col => $val) {
                if (in_array($col, $cols)) {
                    $filterData[$col] = $val;
                }
            }
        }
        return $filterData;
    }
}
?>
