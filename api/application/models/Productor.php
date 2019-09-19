<?php
class Application_Model_Productor extends Zend_Db_Table_Abstract {

    protected $_name = "productor";
    protected $_id = "id";

    public function getList() {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->fetchAll("SELECT pr.*, im.path as image FROM productor pr LEFT JOIN image im ON pr.id_image=im.id ORDER BY pr.name");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getById($id) {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->fetchRow("SELECT pr.* FROM productor pr WHERE re.id=$id");
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
            $data = $connection->fetchOne("SELECT id FROM productor WHERE name='$name'");
            if (!$data) {
                if ($connection->insert("productor", array('name' => $name))) {
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
