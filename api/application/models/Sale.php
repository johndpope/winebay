<?php
class Application_Model_Sale extends Zend_Db_Table_Abstract {

    protected $_name = "sale";
    protected $_id = "id";

    public function getWinehouseList($id) {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->fetchAll("SELECT sa.*, wp.quantity as estoque, pr.name as product, pa.id as package_id, pa.name as package_name, pa.size as package_size
            FROM sale sa, product pr, winehouse_product wp, package pa
            WHERE sa.id_winehouse_product=wp.id AND wp.id_winehouse=$id AND wp.id_product=pr.id AND sa.id_package=pa.id GROUP BY wp.id");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function create($data) {
        try {
            return $this->insert($data);
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function save($data, $id) {
        try {
            return $this->update($data, "id=$id");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function exclude($id) {
        try {
            return $this->delete("id=$id");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }
}
?>
