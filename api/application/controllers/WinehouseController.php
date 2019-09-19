<?php
class WinehouseController extends Zend_Controller_Action
{
    private static $_salt;
    private static $_secaoConfig = "geral";

    public function init()
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/config.ini', self::$_secaoConfig); //Lê o arquivo de configuração
        try {
            self::$_salt = $config->salt;
        } catch (Zend_Exception $e) {
            echo $e->getMessage();
        }
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function listAction()
    {
        $winehouseModel = new Application_Model_Winehouse();
        $list = $winehouseModel->getList();
        foreach ($list as $i => $winehouse) {
            $list[$i]->name = utf8_encode(ucwords(strtolower(stripslashes($winehouse->name))));
            $list[$i]->business_name = utf8_encode(ucwords(strtolower(stripslashes($winehouse->business_name))));
            $list[$i]->contact = utf8_encode(ucwords(strtolower(stripslashes($winehouse->contact))));
            $list[$i]->address = utf8_encode(ucwords(strtolower(stripslashes($winehouse->address))));
            $list[$i]->city = utf8_encode(ucwords(strtolower(stripslashes($winehouse->city))));
            $list[$i]->state = utf8_encode(ucwords(strtolower(stripslashes($winehouse->state))));
            $list[$i]->cep = utf8_encode(ucwords(strtolower(stripslashes($winehouse->cep))));
            $list[$i]->phone = utf8_encode(ucwords(strtolower(stripslashes($winehouse->phone))));
            $list[$i]->email = utf8_encode(ucwords(strtolower(stripslashes($winehouse->email))));
            $list[$i]->can_add_products = boolval($winehouse->can_add_products);
        }
        // var_dump($list);
        echo json_encode($list);
    }

    public function getAction()
    {
        if ($this->_hasParam("id")) {
            $winehouseModel = new Application_Model_Winehouse();
            $winehouse = $winehouseModel->getById($this->getRequest()->getParam('id'));
            $winehouse->name = utf8_encode(ucwords(strtolower(stripslashes($winehouse->name))));
            $winehouse->business_name = utf8_encode(ucwords(strtolower(stripslashes($winehouse->business_name))));
            $winehouse->address = utf8_encode(ucwords(strtolower(stripslashes($winehouse->address))));
            $winehouse->city = utf8_encode(ucwords(strtolower(stripslashes($winehouse->city))));
            $winehouse->state = utf8_encode(ucwords(strtolower(stripslashes($winehouse->state))));
            $winehouse->contact = utf8_encode(ucwords(strtolower(stripslashes($winehouse->contact))));
            $winehouse->cep = utf8_encode(ucwords(strtolower(stripslashes($winehouse->cep))));
            $winehouse->phone = utf8_encode(ucwords(strtolower(stripslashes($winehouse->phone))));
            $winehouse->email = utf8_encode(ucwords(strtolower(stripslashes($winehouse->email))));
            echo json_encode($winehouse);
        }
    }

    public function deleteAction()
    {
        $winehouseModel = new Application_Model_Winehouse();
        $id = $this->getRequest()->getParam("id");
        echo $winehouseModel->exclude($id);
    }

    public function createAction()
    {
        $winehouseModel = new Application_Model_Winehouse();
        $name = ucwords(strtolower(trim($this->getRequest()->getPost("name"))));
        $business_name = ucwords(strtolower(trim($this->getRequest()->getPost("business_name"))));
        $address = trim($this->getRequest()->getPost("address"));
        $contact = trim($this->getRequest()->getPost("contact"));
        $city = trim($this->getRequest()->getPost("city"));
        $region = trim($this->getRequest()->getPost("region"));
        $state = trim($this->getRequest()->getPost("state"));
        $cep = trim($this->getRequest()->getPost("cep"));
        $phone = trim($this->getRequest()->getPost("phone"));
        $email = strtolower(trim($this->getRequest()->getPost("email")));
        $password = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@#!', ceil(7 / strlen($x)))), 1, 7);
        $cnpj = trim($this->getRequest()->getPost("cnpj"));
        $tax_type = trim($this->getRequest()->getPost("tax_type"));
        $fee_percentage = intval($this->getRequest()->getPost("fee_percentage"));
        $can_add_products = intval($this->getRequest()->getPost("can_add_products"));
        $self_register = intval($this->getRequest()->getPost("self_register"));
        if (($name != "") && ($address != "") && ($phone != "")) {
            $image = trim($this->getRequest()->getPost("id_image"));
            if (!is_numeric($image)) {
                $image = null;
            }

            $data = array(
                'name' => utf8_decode(strip_tags(addslashes($name))),
                'business_name' => utf8_decode(strip_tags(addslashes($business_name))),
                'address' => utf8_decode(strip_tags(addslashes($address))),
                'contact' => utf8_decode(strip_tags(addslashes($contact))),
                'city' => utf8_decode(strip_tags(addslashes($city))),
                'region' => utf8_decode(strip_tags(addslashes($region))),
                'state' => utf8_decode(strip_tags(addslashes($state))),
                'cep' => utf8_decode(strip_tags(addslashes($cep))),
                'phone' => utf8_decode(strip_tags(addslashes($phone))),
                'email' => utf8_decode(strip_tags(addslashes($email))),
                'password' => md5($password . self::$_salt),
                'cnpj' => utf8_decode(strip_tags(addslashes($cnpj))),
                'tax_type' => utf8_decode(strip_tags(addslashes($tax_type))),
                'fee_percentage' => $fee_percentage,
                'id_image' => $image,
                'can_add_products' => $can_add_products,
                'self_register' => $self_register
            );
            $newWinehouse = $winehouseModel->create($data);

            $smtp = "mail.abdtech.com.br";
            $conta = "noreply@abdtech.com.br";
            $senha = "C-[&1!mP7dAJ";
            $de = "noreply@abdtech.com.br";
            $assunto = "[Winebay] Winehouse cadastrada!";

            $mensagem = utf8_decode("
            <h3>Agora a Winehouse $name está no Winebay!</h3>
            <h4>Seu novo acesso é:</h4>
            <p><strong>Login:</strong> $email</p>
            <p><strong>Senha:</strong> $password</p>
            <br/><br/>
            <p>Para acessar o Painel da Winehouse, <a href='http://winehouse.winebay.com.br' target='_blank'>clique aqui</a></p>");
            try {
                $config = array('auth' => 'login', 'username' => $conta, 'password' => $senha, 'ssl' => 'ssl', 'port' => '465');
                $mailTransport = new Zend_Mail_Transport_Smtp($smtp, $config);
                $mail = new Zend_Mail();
                $mail->setFrom($de);
                $mail->setBodyHTML($mensagem);
                $mail->setSubject($assunto);
                $mail->addTo($email);
                $mail->send($mailTransport);
                // echo 1;
            } catch (Exception $e) {
                echo ($e->getMessage());
            }

            echo json_encode(array('id' => $newWinehouse, 'name' => $name));
        } else {
            echo true;
        }
    }

    public function setpassAction()
    {
        if ($this->_hasParam("id") && $this->_hasParam("password")) {
            $id = strip_tags(addslashes($this->getRequest()->getPost("id")));
            $password = $this->getRequest()->getPost("password");
            $winehouseModel = new Application_Model_Winehouse();
            echo $winehouseModel->save(array(
                "password" => md5($password . self::$_salt),
            ), $id);
        }
    }

    public function saveAction()
    {
        $winehouseModel = new Application_Model_Winehouse();
        $id = $this->getRequest()->getParam("id");
        $name = ucwords(strtolower(trim($this->getRequest()->getPost("name"))));
        $business_name = ucwords(strtolower(trim($this->getRequest()->getPost("business_name"))));
        $address = trim($this->getRequest()->getPost("address"));
        $contact = trim($this->getRequest()->getPost("contact"));
        $city = trim($this->getRequest()->getPost("city"));
        $region = trim($this->getRequest()->getPost("region"));
        $state = trim($this->getRequest()->getPost("state"));
        $cep = trim($this->getRequest()->getPost("cep"));
        $phone = trim($this->getRequest()->getPost("phone"));
        $email = strtolower(trim($this->getRequest()->getPost("email")));
        $cnpj = trim($this->getRequest()->getPost("cnpj"));
        $tax_type = trim($this->getRequest()->getPost("tax_type"));
        $fee_percentage = intval($this->getRequest()->getPost("fee_percentage"));
        $image = trim($this->getRequest()->getPost("id_image"));
        $shipment_discount = trim($this->getRequest()->getPost("shipment_discount"));
        $shipment_discount_type = trim($this->getRequest()->getPost("shipment_discount_type"));
        $can_add_products = intval($this->getRequest()->getPost("can_add_products"));
        if (!is_numeric($image)) {
            $image = null;
        }

        if (($name != "") && ($address != "") && ($phone != "")) {
            $data = array(
                'name' => utf8_decode(strip_tags(addslashes($name))),
                'business_name' => utf8_decode(strip_tags(addslashes($business_name))),
                'address' => utf8_decode(strip_tags(addslashes($address))),
                'contact' => utf8_decode(strip_tags(addslashes($contact))),
                'city' => utf8_decode(strip_tags(addslashes($city))),
                'region' => utf8_decode(strip_tags(addslashes($region))),
                'state' => utf8_decode(strip_tags(addslashes($state))),
                'cep' => utf8_decode(strip_tags(addslashes($cep))),
                'phone' => utf8_decode(strip_tags(addslashes($phone))),
                'email' => utf8_decode(strip_tags(addslashes($email))),
                'cnpj' => utf8_decode(strip_tags(addslashes($cnpj))),
                'tax_type' => utf8_decode(strip_tags(addslashes($tax_type))),
                'fee_percentage' => $fee_percentage,
                'id_image' => $image,
                'shipment_discount' => $shipment_discount,
                'shipment_discount_type' => $shipment_discount_type,
                'can_add_products' => $can_add_products
            );
            echo $winehouseModel->save($data, $id);
        } else {
            echo true;
        }
    }

    public function addproductAction()
    {
        $productModel = new Application_Model_Product();
        if ($this->_hasParam("id_product") && $this->_hasParam("id_winehouse") &&
            $this->_hasParam("price") && $this->_hasParam("quantity")) {
            echo $productModel->addToWinehouse(array(
                'id_product' => $this->getRequest()->getParam("id_product"),
                'id_winehouse' => $this->getRequest()->getParam("id_winehouse"),
                'price' => $this->getRequest()->getParam("price"),
                'quantity' => $this->getRequest()->getParam("quantity"),
                'crop' => utf8_decode(strip_tags(addslashes(trim($this->getRequest()->getParam("crop"))))),
            ));
        }
    }

    public function addproductlistAction()
    {
        $productModel = new Application_Model_Product();
        if ($this->_hasParam("list")) {
            $list = $this->getRequest()->getPost("list");
            foreach ($list as $prod) {
                $productModel->addToWinehouse(array(
                    'id_product' => $prod["id_product"],
                    'id_winehouse' => $prod["id_winehouse"],
                    'price' => $prod["price"],
                    'quantity' => $prod["quantity"],
                    'crop' => utf8_decode(strip_tags(addslashes(trim($prod["crop"])))),
                ));
            }
        }
    }

    public function saveproductAction()
    {
        $productModel = new Application_Model_Product();
        if ($this->_hasParam("id") && $this->_hasParam("id_product") && $this->_hasParam("id_winehouse") &&
            $this->_hasParam("price") && $this->_hasParam("quantity")) {
            echo $productModel->saveOnWinehouse(array(
                'id_product' => $this->getRequest()->getParam("id_product"),
                'id_winehouse' => $this->getRequest()->getParam("id_winehouse"),
                'price' => $this->getRequest()->getParam("price"),
                'quantity' => $this->getRequest()->getParam("quantity"),
                'crop' => utf8_decode(strip_tags(addslashes(trim($this->getRequest()->getParam("crop"))))),
            ), $this->getRequest()->getParam("id"));
        }
    }

    public function getproductAction()
    {
        if ($this->_hasParam("id")) {
            $productModel = new Application_Model_Product();
            $id = $this->getRequest()->getParam("id");
            $whProduct = $productModel->getFromWinehouse($id);
            $whProduct->crop = utf8_encode(stripslashes($whProduct->crop));
            $whProduct->name = utf8_encode(stripslashes($whProduct->name));

            $product = $productModel->getById($whProduct->id_product);
            $product->name = utf8_encode(ucwords(strtolower(stripslashes($product->name))));
            $product->country = utf8_encode(ucwords(strtolower(stripslashes($product->country))));
            $product->region = utf8_encode(ucwords(strtolower(stripslashes($product->region))));
            $product->grape = utf8_encode(ucwords(strtolower(stripslashes($product->grape))));
            $product->productor = utf8_encode(ucwords(strtolower(stripslashes($product->productor))));
            $product->importer = utf8_encode(ucwords(strtolower(stripslashes($product->importer))));
            $product->tipicity = utf8_encode(ucwords(strtolower(stripslashes($product->tipicity))));

            echo json_encode(array('winehouseproduct' => $whProduct, 'product' => $product));
        }
    }

    public function removeproductAction()
    {
        if ($this->_hasParam("id")) {
            $productModel = new Application_Model_Product();
            $id = $this->getRequest()->getParam("id");
            echo $productModel->excludeFromWinehouse($id);
        }
    }

    public function getordersAction()
    {
        if ($this->_hasParam("id")) {
            $customerOrderModel = new Application_Model_CustomerOrder();
            $id = $this->getRequest()->getParam("id");
            $list = $customerOrderModel->getByWinehouse($id);
            foreach ($list as $i => $order) {
                $list[$i]->customer_name = utf8_encode(stripslashes($order->customer_name));
            }
            echo json_encode($list);
        }
    }

    public function getorderAction()
    {
        if ($this->_hasParam("id")) {
            $customerOrderModel = new Application_Model_CustomerOrder();
            $packageModel = new Application_Model_Package();
            $id = $this->getRequest()->getParam("id");
            $data = $customerOrderModel->getById($id);
            $data->customer_name = utf8_encode(stripslashes($data->customer_name));
            $data->address = json_decode($data->address);
            $data->coupon = json_decode($data->coupon);
            $data->pickup_info = json_decode($data->pickup_info);
            $list = $customerOrderModel->getItems($data->id);
            $data->total_quantity = 0;
            foreach ($list as $j => $item) {
                $list[$j]->name = utf8_encode($item->name);
                $data->total_quantity += $item->quantity;
            }
            $data->list = $list;
            $data->packages = $packageModel->getList();
            foreach ($data->packages as $i => $package) {
                $data->packages[$i]['name'] = utf8_encode(ucwords(strtolower(stripslashes($package['name']))));
            }

            echo json_encode($data);
        }
    }

    public function requestpickupAction()
    {
        if ($this->_hasParam("pickupData")) {
            $pickupData = $this->getRequest()->getPost("pickupData");
            $customerOrderModel = new Application_Model_CustomerOrder();
            $integrationsModel = new Application_Model_Integrations();
            $dhlConfig = $integrationsModel->get("DHL");
            $dhlConfig = json_decode($dhlConfig[$dhlConfig['current_env']], true);
            $dhlConfig['order'] = array(
                "id" => $pickupData['order']['id'],
                "packages" => array(),
                "tracking_code" => $pickupData['order']['tracking_code'],
            );
            $dhlConfig['order']['total_weight'] = 0;
            foreach ($pickupData['order']['Embalagens'] as $embalagem) {
                for ($i = 0; $i < $embalagem['count']; $i++) {
                    $dhlConfig['order']['packages'][] = $embalagem;
                    $dhlConfig['order']['total_weight'] += $embalagem['weight'];
                }
            }
            $dhlConfig['winehouse'] = array(
                "contact" => $pickupData['winehouse']['contact'],
                "phone" => $pickupData['winehouse']['phone'],
                "name" => $pickupData['winehouse']['name'],
                "address" => $pickupData['winehouse']['address'],
                "city" => $pickupData['winehouse']['city'],
                "state" => $pickupData['winehouse']['state'],
                "cep" => $pickupData['winehouse']['cep'],
            );
            $dhlConfig['pickup'] = array(
                "location" => $pickupData['pickup']['location'],
                "date" => $pickupData['pickup']['date'],
                "min_hour" => $pickupData['pickup']['minHour'],
                "max_hour" => $pickupData['pickup']['maxHour'],
                "contact" => $pickupData['pickup']['contact'],
                "pickup_type" => ($pickupData['pickup']['date'] == $pickupData['pickup']['nowDate']) ? 'S' : 'A',
            );

            $dhlAPI = new Application_Model_DHLAPI();

            if ($pickupRequest = $dhlAPI->requestPickup($dhlConfig)) {
                if (isset($pickupRequest['Response']['Status']['ActionStatus']) && ($pickupRequest['Response']['Status']['ActionStatus'] == "Error")) {
                    header("HTTP/1.1 500 Internal Server Error");
                } else {
                    echo $customerOrderModel->save(array(
                        "pickup_contact" => $pickupData['pickup']['contact'],
                        "awaiting_pickup" => 1,
                        "pickup_info" => json_encode(array(
                            "confirmation" => $pickupRequest['ConfirmationNumber'],
                            "pickup" => $dhlConfig['pickup'],
                            "origin" => $pickupRequest['OriginSvcArea'],
                        )),
                    ), $pickupData['order']['id']);
                }
            }
        }
    }

    public function cancelpickupAction()
    {
        if ($this->_hasParam("pickupData")) {
            $pickupData = $this->getRequest()->getPost("pickupData");
            $reason = $this->getRequest()->getPost("reason");
            $customerOrderModel = new Application_Model_CustomerOrder();
            $integrationsModel = new Application_Model_Integrations();
            $dhlConfig = $integrationsModel->get("DHL");
            $dhlConfig = json_decode($dhlConfig[$dhlConfig['current_env']], true);
            $dhlConfig['reason'] = $reason;
            $dhlConfig['pickup'] = $pickupData['pickup'];
            $dhlConfig['confirmation'] = $pickupData['confirmation'];
            $dhlConfig['origin'] = $pickupData['origin'];
            $dhlAPI = new Application_Model_DHLAPI();
            $cancelData = $dhlAPI->cancelPickup($dhlConfig);

            if ($cancelData = $dhlAPI->cancelPickup($dhlConfig)) {
                if (isset($cancelData['Response']['Status']['ActionStatus']) && ($cancelData['Response']['Status']['ActionStatus'] == "Error")) {
                    header("HTTP/1.1 500 Internal Server Error");
                } else {
                    echo $customerOrderModel->save(array(
                        "awaiting_pickup" => 0,
                        "pickup_info" => null,
                    ), $this->getRequest()->getPost("order"));
                }
            }
            // echo $customerOrderModel->save(array(
            //     "awaiting_pickup" => 0,
            //     "pickup_info" => null,
            // ), $this->getRequest()->getPost("order"));
        }
    }
}
