<?php
if (!function_exists('getallheaders'))
{
    function getallheaders()
    {
           $headers = [];
       foreach ($_SERVER as $name => $value)
       {
           if (substr($name, 0, 5) == 'HTTP_')
           {
               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
           }
       }
       return $headers;
    }
} 
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
    protected function _initDb() {
        $this->bootstrap('multidb');
        $multidb = $this->getPluginResource('multidb');
        $db1 = $multidb->getDb('db1');
        $db1->setFetchMode(Zend_Db::FETCH_OBJ);
        Zend_Registry::set('db', $db1);

        // $db2 = $multidb->getDb('db2');
        // $db2->setFetchMode(Zend_Db::FETCH_OBJ);
        // Zend_Registry::set('db2', $db2);
    }

    public function _initPostPayload() {
        $headers = getallheaders();
        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            $data = file_get_contents('php://input');
            try {
                $data = json_decode($data, true);
            } catch(Exception $e) {}
                if (is_array($data) && count($data) > 0) {
                    $_POST = array_merge_recursive((array) $_POST, $data);
                }
            }
        }
    }
