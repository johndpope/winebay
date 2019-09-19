<?php
class Application_Model_Grape extends Zend_Db_Table_Abstract {

    protected $_name = "grape";
    protected $_id = "id";

    public function getList() {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->fetchAll("SELECT gr.*, im.path as image FROM grape gr LEFT JOIN image im ON gr.id_image=im.id ORDER BY gr.name");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getById($id) {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->fetchRow("SELECT * FROM grape WHERE id=$id");
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
            $data = $connection->fetchOne("SELECT id FROM grape WHERE name='$name'");
            if (!$data) {
                if ($connection->insert("grape", array('name' => $name))) {
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
