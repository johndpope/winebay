<?php
class Application_Model_Customer extends Zend_Db_Table_Abstract
{

    protected $_name = "customer";
    protected $_id = "id";

    public function getByLoginAndPassword($login, $password)
    {
        try {
            $data = $this->fetchRow("email='$login' AND password='$password'");
            if ($data) {
                return $data->toArray();
            }

        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getByEmail($login)
    {
        try {
            $data = $this->fetchRow("email='$login'");
            if ($data) {
                return $data->toArray();
            }

        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    function getList() {
        try {
            $data = $this->fetchAll();
            if ($data) {
                return $data->toArray();
            }

        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getById($id)
    {
        try {
            $data = $this->fetchRow("id='$id'");
            if ($data) {
                return $data->toArray();
            }

        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function save($data, $id)
    {
        try {
            return $this->update($data, "id=$id");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    protected function filterColumns(array $data)
    {
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
