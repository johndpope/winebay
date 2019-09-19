<?php
class Application_Model_Winehouse extends Zend_Db_Table_Abstract {

    protected $_name = "winehouse";
    protected $_id = "id";

    public function getByLoginAndPassword($login, $password) {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->fetchRow("SELECT wi.*, im.path as logo FROM winehouse wi LEFT JOIN image im ON wi.id_image=im.id WHERE wi.email='$login' AND wi.password='$password'");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getList() {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->fetchAll("SELECT wi.*, im.path as image FROM winehouse wi LEFT JOIN image im ON wi.id_image=im.id ORDER BY wi.name");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getById($id) {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->fetchRow("SELECT wi.*, im.path as logo FROM winehouse wi LEFT JOIN image im ON wi.id_image=im.id WHERE wi.id=$id");
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

    public function checkIfShipper($id)
    {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            $data = $connection->fetchRow("SELECT id FROM customer_order WHERE id_winehouse=$id AND customer_pickup=0 AND status='finished'");
            if ($data) return (array)$data;
            else return false;
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
