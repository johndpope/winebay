<?php
class Application_Model_Image extends Zend_Db_Table_Abstract {

    protected $_name = "image";
    protected $_id = "id";


    public static function create($path) {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            $connection->insert('image', array('path'=>$path, 'hide'=>0));
            return $connection->lastInsertId();
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function exclude($id) {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->delete('image', "id=$id");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }
    

    public static function getPath($id) {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->fetchOne("SELECT path FROM image WHERE id=$id");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }
}
?>
