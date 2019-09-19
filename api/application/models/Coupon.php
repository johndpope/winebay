<?php
class Application_Model_Coupon extends Zend_Db_Table_Abstract
{

    protected $_name = "coupon";
    protected $_id = "id";

    public function save($data, $id)
    {
        try {
            return $this->update($data, "id=$id");
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

    public function getByCode($code)
    {
        try {
            $data = $this->fetchRow("code='$code' AND active IS TRUE");
            if ($data) {
                return $data->toArray();
            }

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

    public function getList()
    {
        try {
            $data = $this->fetchALl();
            if ($data) {
                return $data->toArray();
            }

        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }
}
