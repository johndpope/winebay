<?php
class Application_Model_DHLAPI
{
    public function getQuotes($config = false)
    {
        if ($config) {
            $quoteXML = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>
            <p:DCTRequest xmlns:p=\"http://www.dhl.com\" xmlns:p1=\"http://www.dhl.com/datatypes\" xmlns:p2=\"http://www.dhl.com/DCTRequestdatatypes\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.dhl.com DCT-req.xsd\">
            </p:DCTRequest>");

            $quoteData = array(
                "GetQuote" => array(
                    "Request" => array(
                        "ServiceHeader" => array(
                            "SiteID" => $config['site_id'],
                            "Password" => $config['password'],
                        ),
                    ),
                    "From" => array(
                        "CountryCode" => "BR",
                        "Postalcode" => $config['winehouse']['cep'],
                    ),
                    "BkgDetails" => array(
                        "PaymentCountryCode" => "BR",
                        "Date" => date('Y-m-d'),
                        "ReadyTime" => date('\P\TH\Hi\M'),
                        "ReadyTimeGMTOffset" => "-03:00",
                        "DimensionUnit" => "CM",
                        "WeightUnit" => "KG",
                        "Pieces" => array(),
                        "PaymentAccountNumber" => $config['account_number'],
                        "IsDutiable" => "N",
                        "NetworkTypeCode" => "AL",
                        "QtdShp" => array(
                            "GlobalProductCode" => "N",
                            "LocalProductCode" => "N",
                        ),
                    ),
                    "To" => array(
                        "CountryCode" => "BR",
                        "Postalcode" => $config['destination'],
                    ),
                    "Dutiable" => array(
                        "DeclaredCurrency" => "BRL",
                        "DeclaredValue" => $config['shipment_value'],
                    ),
                ),
            );
            foreach ($config['packages'] as $i => $package) {
                $quoteData['GetQuote']['BkgDetails']['Pieces'][] = array(
                    "NodeName" => "Piece",
                    "Value" => array(
                        "PieceID" => $i + 1,
                        "Height" => $package['height'],
                        "Depth" => $package['depth'],
                        "Width" => $package['width'],
                        "Weight" => $package['weight'] / 1000,
                    ),
                );
            }
            $xmlRequest = $this->arrayToXml($quoteData, $quoteXML)->asXML();

            return $this->sendRequest($config['url'], $xmlRequest);
        }
    }

    public function validateShipment($config = false)
    {
        if ($config) {
            $validationXML = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>
            <req:ShipmentRequest xmlns:req=\"http://www.dhl.com\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.dhl.com ship-val-global-req.xsd\" schemaVersion=\"5.0\">
            </req:ShipmentRequest>");

            $shipmentData = array(
                'Request' => array(
                    'ServiceHeader' => array(
                        "MessageTime" => date("Y-m-d\Th:i:s") . "-03:00",
                        "MessageReference" => 'Shipping Validation WH_' . $config['order']['winehouse_data']['id'] . ' CT_' . $config['order']['customer_data']['id'],
                        'SiteID' => $config['site_id'],
                        'Password' => $config['password'],
                    ),
                ),
                'RegionCode' => 'AM',
                'RequestedPickupTime' => 'N',
                'NewShipper' => $config['order']['winehouse_shipper'] ? 'N' : 'Y',
                'LanguageCode' => 'pt',
                'PiecesEnabled' => 'Y',
                'Billing' => array(
                    'ShipperAccountNumber' => $config['account_number'],
                    'ShippingPaymentType' => 'S',
                    'BillingAccountNumber' => $config['account_number'],
                    'DutyPaymentType' => 'S',
                    'DutyAccountNumber' => $config['account_number'],
                ),
                'Consignee' => array(
                    'CompanyName' => $config['order']['customer_data']['name'],
                    array(
                        "NodeName" => "AddressLine",
                        "Value" => $config['order']['order']['address']['address'],
                    ),
                    array(
                        "NodeName" => "AddressLine",
                        "Value" => $config['order']['order']['address']['region'],
                    ),
                    'City' => $config['order']['order']['address']['city'],
                    'PostalCode' => $config['order']['order']['address']['cep'],
                    'CountryCode' => 'BR',
                    'CountryName' => 'Brasil',
                    'FederalTaxId' => $config['order']['customer_data']['cpf'],
                    'StateTaxId' => $config['order']['order']['address']['state'],
                    'Contact' => array(
                        'PersonName' => $config['order']['customer_data']['name'],
                        'PhoneNumber' => '+55 ' . $config['order']['customer_data']['phone'],
                    ),
                ),
                'Commodity' => array(
                    'CommodityCode' => $config['order']['order']['number'],
                ),
                'ShipmentDetails' => array(
                    'NumberOfPieces' => count($config['order']['embalagens']),
                    'Pieces' => array(),
                    'Weight' => 0,
                    'WeightUnit' => 'K',
                    'GlobalProductCode' => 'N',
                    'LocalProductCode' => 'N',
                    'Date' => date('Y-m-d'),
                    'Contents' => 'Wine Bottles',
                    'DoorTo' => 'DD',
                    'DimensionUnit' => 'C',
                    'PackageType' => 'PA',
                    'IsDutiable' => 'N',
                    'CurrencyCode' => 'BRL',
                ),
                'Shipper' => array(
                    'ShipperID' => "winehouse_" . $config['order']['winehouse_data']['id'],
                    'CompanyName' => $config['order']['winehouse_data']['name'],
                    'RegisteredAccount' => $config['order']['winehouse_data']['dhl_account'],
                    'AddressLine' => $config['order']['winehouse_data']['address'],
                    'City' => $config['order']['winehouse_data']['city'],
                    'Division' => $config['order']['winehouse_data']['state'],
                    'DivisionCode' => $config['order']['winehouse_data']['state'],
                    'PostalCode' => $config['order']['winehouse_data']['cep'],
                    'CountryCode' => 'BR',
                    'CountryName' => 'Brasil',
                    'FederalTaxId' => str_replace(array('.', '/', '-'), '', $config['order']['winehouse_data']['cnpj']),
                    'StateTaxId' => $config['order']['winehouse_data']['state'],
                    'Contact' => array(
                        'PersonName' => $config['order']['winehouse_data']['name'],
                        'PhoneNumber' => '+55 ' . $config['order']['winehouse_data']['phone'],
                        'Email' => $config['order']['winehouse_data']['email'],
                    ),
                ),
                'EProcShip' => 'N',
                'LabelImageFormat' => 'PDF',
            );
            foreach ($config['order']['embalagens'] as $i => $package) {
                for ($i = 0; $i < $package['count']; $i++) {
                    $shipmentData['ShipmentDetails']['Pieces'][] = array(
                        "NodeName" => "Piece",
                        "Value" => array(
                            "PieceID" => $i + 1,
                            'PackageType' => 'PA',
                            "Weight" => $package['weight'] / 1000,
                            "DimWeight" => number_format(($package['weight'] / 1000) / $package['size'], 3, '.', ''),
                            "Width" => $package['width'],
                            "Height" => $package['height'],
                            "Depth" => $package['depth'],
                            "PieceContents" => 'Wine Bottles',

                        ),
                    );
                    $shipmentData['ShipmentDetails']['Weight'] += ($package['weight'] / 1000);
                }
            }

            $xmlRequest = $this->arrayToXml($shipmentData, $validationXML)->asXML();

            return $this->sendRequest($config['url'], $xmlRequest);
        }
    }

    public function requestPickup($config = false)
    {
        // var_dump($config);exit;
        if ($config) {
            $pickupXML = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?><req:BookPURequest xmlns:req=\"http://www.dhl.com\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" schemaVersion=\"1.0\" xsi:schemaLocation=\"http://www.dhl.com  book-pickup-global-req.xsd\">
            </req:BookPURequest>");

            $pickupData = array(
                'Request' => array(
                    'ServiceHeader' => array(
                        'MessageTime' => date("Y-m-d\Th:i:s") . "-03:00",
                        'MessageReference' => 'Pickup Request for order ID ' . $config['order']['id'],
                        'SiteID' => $config['site_id'],
                        'Password' => $config['password'],
                    ),
                ),
                'RegionCode' => 'AM',
                'Requestor' => array(
                    'AccountType' => 'D',
                    'AccountNumber' => $config['account_number'],
                    'RequestorContact' => array(
                        'PersonName' => $config['winehouse']['contact'],
                        'Phone' => "+55 " . $config['winehouse']['phone'],
                    ),
                ),
                'Place' => array(
                    'LocationType' => 'B',
                    'CompanyName' => $config['winehouse']['name'],
                    'Address1' => $config['winehouse']['address'],
                    // 'Address2' => '',
                    'PackageLocation' => $config['pickup']['location'],
                    'City' => $config['winehouse']['city'],
                    'StateCode' => $config['winehouse']['state'],
                    'CountryCode' => 'BR',
                    'PostalCode' => $config['winehouse']['cep'],
                ),
                'Pickup' => array(
                    'PickupDate' => implode('-', array_reverse(explode('/', $config['pickup']['date']))),
                    // 'PickupType' => $config['pickup']['pickup_type'],
                    'ReadyByTime' => $config['pickup']['min_hour'],
                    'CloseTime' => $config['pickup']['max_hour'],
                    'Pieces' => count($config['order']['packages']),
                    'weight' => array(
                        'Weight' => $config['order']['total_weight'],
                        'WeightUnit' => 'K',
                    ),
                ),
                'PickupContact' => array(
                    'PersonName' => $config['pickup']['contact'],
                    'Phone' => '+55 ' . $config['winehouse']['phone'],
                ),
                'ShipmentDetails' => array(
                    'AccountType' => 'D',
                    'AccountNumber' => $config['account_number'],
                    'AWBNumber' => $config['order']['tracking_code'],
                    'NumberOfPieces' => count($config['order']['packages']),
                    'Weight' => $config['order']['packages'][0]['weight'],
                    'WeightUnit' => 'K',
                    'GlobalProductCode' => 'N',
                    'DoorTo' => 'DD',
                    'DimensionUnit' => 'C',
                ),
            );

            $xmlRequest = $this->arrayToXml($pickupData, $pickupXML)->asXML();

            return $this->sendRequest($config['url'], $xmlRequest);
        }
    }

    public function cancelPickup($config = false)
    {
        // var_dump($config);exit;
        if ($config) {
            $pickupXML = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?>
            <req:CancelPURequest xmlns:req=\"http://www.dhl.com\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
            xsi:schemaLocation=\"http://www.dhl.com cancel-pickup-global-req.xsd\" schemaVersion=\"1.0\">
            </req:CancelPURequest>");

            $pickupData = array(
                'Request' => array(
                    'ServiceHeader' => array(
                        'MessageTime' => date("Y-m-d\Th:i:s") . "-03:00",
                        'MessageReference' => 'Pickup Cancelling of #' . $config['confirmation'],
                        'SiteID' => $config['site_id'],
                        'Password' => $config['password'],
                    ),
                ),
                'RegionCode' => 'AM',
                'ConfirmationNumber' => $config['confirmation'],
                'RequestorName' => $config['pickup']['contact'],
                'CountryCode' => 'BR',
                'OriginSvcArea' => $config['origin'],
                'Reason' => $config['reason'],
                'PickupDate' => implode('-', array_reverse(explode('/', $config['pickup']['date']))),
                'CancelTime' => date("h:i"),
            );

            $xmlRequest = $this->arrayToXml($pickupData, $pickupXML)->asXML();
            return $this->sendRequest($config['url'], $xmlRequest);
        }
    }

    protected function sendRequest($url, $xmlRequest)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest);
        $xmlResponse = curl_exec($ch);
        curl_close($ch);
        $arrayResponse = simplexml_load_string(utf8_encode($xmlResponse), "SimpleXMLElement", LIBXML_NOCDATA);

        return json_decode(json_encode($arrayResponse), true);
    }

    protected function arrayToXml($array, $xmlObject)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (is_numeric($key)) {
                    if (is_array($value['Value'])) {
                        $subnode = $xmlObject->addChild("{$value['NodeName']}", null, "");
                        $this->arrayToXml($value['Value'], $subnode);
                    } else {
                        $subnode = $xmlObject->addChild("{$value['NodeName']}", $value['Value'], "");
                    }
                } else {
                    $subnode = $xmlObject->addChild("$key", null, "");
                    $this->arrayToXml($value, $subnode);
                }
            } else {
                $xmlObject->addChild("$key", htmlspecialchars("$value"), "");
            }
        }
        return $xmlObject;
    }
}
