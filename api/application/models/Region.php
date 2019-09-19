<?php
class Application_Model_Region extends Zend_Db_Table_Abstract {

    protected $_name = "region";
    protected $_id = "id";

    public function getList() {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->fetchAll("SELECT re.*, co.name as country_name, im.path as image FROM region re LEFT JOIN image im ON re.id_image=im.id LEFT JOIN country co ON re.id_country=co.id ORDER BY re.name");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getById($id) {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->fetchRow("SELECT re.*, co.name as country_name FROM region re LEFT JOIN country co ON re.id_country=co.id WHERE re.id=$id");
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

    public static function fetchOrCreate($name, $id_country = null) {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            $data = $connection->fetchOne("SELECT id FROM region WHERE name='$name'");
            if (!$data) {
                if ($connection->insert("region", array('name' => $name, 'id_country' => $id_country))) {
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
