<?php
class Application_Model_Estoque extends Zend_Db_Table_Abstract
{

    protected $_name = "winehouse_product_entry";
    protected $_id = "id";

    public function getEntries($data)
    {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->fetchAll("SELECT wpe.*, DATE_FORMAT(wpe.date, '%d/%m/%Y') as `date`, wpe.date as rawDate, pr.name as product_name
             FROM winehouse_product_entry wpe, winehouse_product wp, product pr
        WHERE wpe.id_winehouse_product= wp.id AND wp.id_product=pr.id AND wpe.date >= '" . $data['date_start'] . " 00:00:00' AND wpe.date <= '" . $data['date_end'] . " 23:59:59'");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function addEntry($data)
    {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            $quantity = $connection->fetchOne("SELECT quantity FROM winehouse_product WHERE id={$data['id_winehouse_product']}");
            $connection->update("winehouse_product", array("quantity" => $quantity+$data['quantity']), "id={$data['id_winehouse_product']}");
            return $connection->insert("winehouse_product_entry", $data);
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }
}
