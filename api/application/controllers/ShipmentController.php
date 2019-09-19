<?php
class ShipmentController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function calculateAction()
    {
        if ($this->_hasParam("cep") && $this->_hasParam("id")) {
            $integrationsModel = new Application_Model_Integrations();
            $dhlConfig = $integrationsModel->get("DHL");
            $dhlConfig = json_decode($dhlConfig[$dhlConfig['current_env']], true);
            $dhlAPI = new Application_Model_DHLAPI();
            $packageModel = new Application_Model_Package();
            $productModel = new Application_Model_Product();
            $winehouseController = new Application_Model_Winehouse();
            $websiteModel = new Application_Model_Website();

            $destination = $this->getRequest()->getParam("cep");
            $productData = (array) $productModel->getFromWinehouse(intval($this->getRequest()->getParam("id")));
            $winehouseData = (array) $winehouseController->getById($productData["id_winehouse"]);
            $fee_value = $productData['price'] * ($winehouseData['fee_percentage'] / 100);

            $generalShipmentDiscount = $websiteModel->getList(array("shipment_discount", "shipment_discount_type"));
            $shipmentDiscount = array(
                "type" => $generalShipmentDiscount['shipment_discount_type']['value'],
                "value" => $generalShipmentDiscount['shipment_discount']['value'],
            );
            $winehouseShipmentDiscount = array(
                "type" => $winehouseData['shipment_discount_type'],
                "value" => $winehouseData['shipment_discount'],
            );
            if ($winehouseShipmentDiscount['value'] > 0) {
                $shipmentDiscount = $winehouseShipmentDiscount;
            }
            $shipmentDiscountValue = 0;
            if (($shipmentDiscount['type'] == 'percentage') && ($shipmentDiscount['value'] >= 100)) {
                $shipmentDiscountValue = $fee_value;
            } else if (($shipmentDiscount['type'] == 'percentage') && ($shipmentDiscount['value'] > 0)) {
                $shipmentDiscountValue = $fee_value * ($shipmentDiscount['value'] / 100);
            } else if (($shipmentDiscount['type'] == 'value') && ($shipmentDiscount['value'] > 0)) {
                $shipmentDiscountValue = $shipmentDiscount['value'];
                if ($shipmentDiscountValue > $fee_value) {
                    $shipmentDiscountValue = $fee_value;
                }
            }
            $shipmentDiscountValue = number_format($shipmentDiscountValue, 2, '.', '');
            $package = (array) $packageModel->getBySize(1);
            $dhlConfig['destination'] = strip_tags(addslashes($this->getRequest()->getParam("cep")));
            $dhlConfig['shipment_value'] = floatval($productData['price']);
            $dhlConfig['packages'] = [$package];
            $dhlConfig['winehouse'] = $winehouseData;

            $dhlQuotes = $dhlAPI->getQuotes($dhlConfig);
            if (isset($dhlQuotes['GetQuoteResponse']['BkgDetails'])) {
                $dhlQuotes['GetQuoteResponse']['BkgDetails']['QtdShp']['ShippingCharge'] -= $shipmentDiscountValue;
                echo json_encode($dhlQuotes['GetQuoteResponse']['BkgDetails']);
            } else {
                header("HTTP/1.1 500 Internal Server Error");
            }
        }
    }

    public function calculateboxAction()
    {
        if ($this->_hasParam("address") && $this->_hasParam("box")) {
            $integrationsModel = new Application_Model_Integrations();
            $dhlConfig = $integrationsModel->get("DHL");
            $dhlConfig = json_decode($dhlConfig[$dhlConfig['current_env']], true);
            $dhlAPI = new Application_Model_DHLAPI();
            $packageModel = new Application_Model_Package();
            $productModel = new Application_Model_Product();
            $winehouseController = new Application_Model_Winehouse();
            $websiteModel = new Application_Model_Website();

            $addressData = $this->getRequest()->getPost("address");
            $boxData = $this->getRequest()->getPost("box");

            $destination = $addressData['cep'];
            $winehouseData = (array) $winehouseController->getById($boxData['winehouse']);
            $fee_value = $boxData['total'] * ($winehouseData['fee_percentage'] / 100);

            $generalShipmentDiscount = $websiteModel->getList(array("shipment_discount", "shipment_discount_type"));
            $shipmentDiscount = array(
                "type" => $generalShipmentDiscount['shipment_discount_type']['value'],
                "value" => $generalShipmentDiscount['shipment_discount']['value'],
            );
            $winehouseShipmentDiscount = array(
                "type" => $winehouseData['shipment_discount_type'],
                "value" => $winehouseData['shipment_discount'],
            );
            if ($winehouseShipmentDiscount['value'] > 0) {
                $shipmentDiscount = $winehouseShipmentDiscount;
            }
            $shipmentDiscountValue = 0;
            if (($shipmentDiscount['type'] == 'percentage') && ($shipmentDiscount['value'] >= 100)) {
                $shipmentDiscountValue = $fee_value;
            } else if (($shipmentDiscount['type'] == 'percentage') && ($shipmentDiscount['value'] > 0)) {
                $shipmentDiscountValue = $fee_value * ($shipmentDiscount['value'] / 100);
            } else if (($shipmentDiscount['type'] == 'value') && ($shipmentDiscount['value'] > 0)) {
                $shipmentDiscountValue = $shipmentDiscount['value'];
                if ($shipmentDiscountValue > $fee_value) {
                    $shipmentDiscountValue = $fee_value;
                }
            }
            $shipmentDiscountValue = number_format($shipmentDiscountValue, 2, '.', '');

            $package = (array) $packageModel->getBySize(1);
            $dhlConfig['destination'] = $destination;
            $dhlConfig['shipment_value'] = $boxData['total'];

            $dhlConfig['packages'] = [];
            foreach ($boxData['embalagens'] as $embalagem) {
                for ($i = 0; $i < $embalagem['count']; $i++) {
                    $dhlConfig['packages'][] = $embalagem;
                }
            }
            $dhlConfig['winehouse'] = $winehouseData;

            $dhlQuotes = $dhlAPI->getQuotes($dhlConfig);
            if (isset($dhlQuotes['GetQuoteResponse']['BkgDetails'])) {
                $dhlQuotes['GetQuoteResponse']['BkgDetails']['DescontoFrete'] = $shipmentDiscountValue;
                $dhlQuotes['GetQuoteResponse']['BkgDetails']['PorcentagemPlataforma'] = $fee_value - $shipmentDiscountValue;
                $dhlQuotes['GetQuoteResponse']['BkgDetails']['QtdShp']['ShippingCharge'] -= $shipmentDiscountValue;
                echo json_encode($dhlQuotes['GetQuoteResponse']['BkgDetails']);
            } else {
                header("HTTP/1.1 500 Internal Server Error");
            }
        }
    }

    public function trackpackageAction()
    {
        //TODO Conectar API DHL - Rastrear o pacote pelo ID
    }
}
