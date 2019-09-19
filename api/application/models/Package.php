<?php
class Application_Model_Package extends Zend_Db_Table_Abstract {

    protected $_name = "package";
    protected $_id = "id";

    public function getList() {
        try {
            return $this->fetchAll()->toArray();
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function getBySize($size) {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->fetchRow("SELECT * FROM package WHERE size>=$size ORDER BY size ASC LIMIT 1");
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
