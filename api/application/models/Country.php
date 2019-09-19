<?php
class Application_Model_Country extends Zend_Db_Table_Abstract {

    protected $_name = "country";
    protected $_id = "id";

    public function getList() {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->fetchAll("SELECT co.*, im.path as image FROM country co LEFT JOIN image im ON co.id_image=im.id ORDER BY co.name");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getPostById($id) {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->fetchRow("SELECT *, DATE_FORMAT(posted, '%d/%m/%Y %h:%i') AS posted FROM posts WHERE id=$id");
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

    public static function fetchOrCreate($name) {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            $data = $connection->fetchOne("SELECT id FROM country WHERE name='$name'");
            if (!$data) {
                if ($connection->insert("country", array('name' => $name, 'shortname' => substr($name, 0, 3)))) {
                    $data = $connection->lastInsertId();
                }
            }
            return $data;
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
