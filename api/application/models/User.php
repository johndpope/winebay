<?php
class Application_Model_User extends Zend_Db_Table_Abstract {

    protected $_name = "user";
    protected $_id = "id";

    public function getByLoginAndPassword($login, $password) {
        try {
            $data = $this->fetchRow("login='$login' AND password='$password'");
            if ($data) return $data->toArray();
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
