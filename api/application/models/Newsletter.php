<?php
class Application_Model_Newsletter extends Zend_Db_Table_Abstract
{

    protected $_name = "newsletter";
    protected $_id = "id";

    public function getList()
    {
        try {
            return $this->fetchAll()->toArray();
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function save($name, $email)
    {
        try {
            return $this->insert(array("name" => $name, "email" => $email));
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function check($email)
    {
        try {
            return $this->fetchRow("email='$email'");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }
}
