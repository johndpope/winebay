<?php
class Application_Model_Maintenance extends Zend_Db_Table_Abstract {

    protected $_name = "table.name";
    protected $_id = "ID";

    public function fetchOrCreate($table, $value, $col='name') {
        try {
             $connection = Zend_Db_Table::getDefaultAdapter();
             $value = utf8_decode(ucwords(mb_strtolower(trim(addslashes($value)), 'UTF-8')));
             $data = $connection->fetchRow("SELECT * FROM $table WHERE $col='$value'");
             if (!$data) {
                 $connection->insert($table, array($col=>$value));
                 return $connection->lastInsertId();
             }
             return $data->id;
         } catch (Zend_Db_Table_Exception $e) {
             echo $e->getMessage();
         }
    }

    public function addProduct($list) {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            foreach ($list as $prod) {
                $prod['name'] = utf8_decode(ucwords(mb_strtolower(trim(addslashes($prod['name'])), 'UTF-8')));
                $data = $connection->fetchRow("SELECT * FROM product WHERE name='" . $prod['name'] . "'");
                if (!$data) {
                    $connection->insert("product", $prod);
                }
            }
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }
}
?>
