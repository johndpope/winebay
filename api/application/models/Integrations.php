<?php
class Application_Model_Integrations extends Zend_Db_Table_Abstract
{

    protected $_name = "integration";
    protected $_id = "id";

    public function save($data, $name)
    {
        try {
            return $this->update($data, "name='$name'");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function get($name)
    {
        try {
            return $this->fetchRow("name='$name'")->toArray();
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }
}
