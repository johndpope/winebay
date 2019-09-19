<?php
class Application_Model_Website extends Zend_Db_Table_Abstract
{

    protected $_name = "site_options";
    protected $_id = "id";

    public function save($name, $value)
    {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->query("INSERT INTO site_options (name, value) VALUES ('$name', '$value') ON DUPLICATE KEY UPDATE value='$value';");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getList($list)
    {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->fetchAssoc("SELECT name, value FROM site_options WHERE name IN ('" . implode("','", $list) . "')");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }
}
