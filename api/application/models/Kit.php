<?php
class Application_Model_Kit extends Zend_Db_Table_Abstract
{

    protected $_name = "product_kit";
    protected $_id = "id";

    public function getWinehouseList($id)
    {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->fetchAll("SELECT pk.*, COUNT(pki.id) AS products, SUM(pki.quantity) AS product_count, pa.id as package_id, pa.name as package_name, pa.size as package_size
            FROM product_kit pk LEFT JOIN product_kit_item pki ON pki.id_product_kit=pk.id, package pa
            WHERE pk.id_winehouse=$id AND pk.id_package = pa.id GROUP BY pk.id");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getById($id)
    {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->fetchRow("SELECT pk.*, COUNT(pki.id) AS products, SUM(pki.quantity) AS product_count, pa.id as package_id, pa.name as package_name, pa.size as package_size
            FROM product_kit pk LEFT JOIN product_kit_item pki ON pki.id_product_kit=pk.id, package pa
            WHERE pk.id=$id AND pk.id_package = pa.id GROUP BY pk.id");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getItems($id)
    {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->fetchAll("SELECT pki.* FROM product_kit_item pki WHERE pki.id_product_kit = $id");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function addItem($data)
    {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->insert("product_kit_item", $data);
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function create($data)
    {
        try {
            return $this->insert($data);
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function save($data, $id)
    {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            $connection->delete("product_kit_item", "id_product_kit=$id");
            return $this->update($data, "id=$id");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function exclude($id)
    {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            $connection->delete("product_kit_item", "id_product_kit=$id");
            return $this->delete("id=$id");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }
}
