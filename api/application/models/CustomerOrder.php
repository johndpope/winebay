<?php
class Application_Model_CustomerOrder extends Zend_Db_Table_Abstract
{
    //STATUS POSSÍVEIS:
    // CRIADO (open)
    // APROVADO (approved)  -> Após comunicação com Gateway de Pagamento
    // EM COLETA (approved, com a flag awayting_pickup = TRUE) -> Aguardando coleta da DHL
    // EM TRANSPORTE (shipment)   -> Após coleta da DHL
    // FINALIZADO (finished)  -> Após finalização pela DHL
    protected $_name = "customer_order";
    protected $_id = "id";

    public function getList()
    {
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
            $connection = Zend_Db_Table::getDefaultAdapter();
            $data = $connection->fetchRow("SELECT co.*, co.total_amount+co.total_shipping-co.shipment_discount as final_value, co.total_amount-co.total_fee as total_to_receive, cu.name as customer_name, DATE_FORMAT(co.date, '%d/%m/%y %H:%i') as date_format FROM customer_order co, customer cu WHERE co.id_customer=cu.id AND co.id=$id");
            if ($data) {
                return $data;
            } else {
                return array();
            }
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getByCustomer($id)
    {
        try {
            $data = $this->fetchAll("id_customer='$id'");
            if ($data) {
                return $data->toArray();
            }

        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function add($data)
    {
        try {
            $data["order"]["number"] = date("Ym") . $data["order"]['id_customer'] . $data["order"]['id_winehouse'] . str_pad(floor(rand() % 1000), 4, "0", STR_PAD_LEFT);
            if ($newOrderId = $this->insert($data["order"])) {
                $data["order"]["id"] = $newOrderId;
                $data["order"]["address"] = (array) json_decode(utf8_encode($data["order"]["address"]));
                $data["order"]["coupon"] = (array) json_decode(utf8_encode($data["order"]["coupon"]));
                $connection = Zend_Db_Table::getDefaultAdapter();
                foreach ($data["items"] as $i => $item) {
                    $item["id_customer_order"] = $newOrderId;
                    $connection->insert("customer_order_item", $item);
                    $data["items"][$i] = $item;
                }
                return $data;
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

    public function getItems($id)
    {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            $data = $connection->fetchAll("SELECT pr.name, coi.quantity, coi.price as unit_price, coi.quantity * coi.price as total_price FROM product pr, winehouse_product wp, customer_order_item coi WHERE pr.id = wp.id_product AND wp.id = coi.id_winehouse_product AND coi.id_customer_order=$id ORDER BY pr.name");
            if ($data) {
                return $data;
            } else {
                return array();
            }

        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getByWinehouse($id)
    {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            $data = $connection->fetchAll("SELECT co.*, co.total_amount+co.total_shipping-co.shipment_discount as final_value, cu.name as customer_name, DATE_FORMAT(co.date, '%d/%m/%y %H:%i') as date_format FROM customer_order co, customer cu, winehouse wi WHERE co.id_customer=cu.id AND co.id_winehouse=wi.id AND wi.id=$id ORDER BY co.date DESC");
            if ($data) {
                return $data;
            } else {
                return array();
            }

        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }
}
