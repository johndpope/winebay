<?php
use YesPay\Card;
use YesPay\Payment;
use YesPay\PaymentMethod;
use YesPay\Transaction;

class CustomerController extends Zend_Controller_Action
{
    private static $_salt;
    private static $_secaoConfig = "geral";
    private $order = null;
    private $payment_info = null;
    private $payment_method = null;
    private $payment = null;
    private $price = 0.00;

    private function paymentMethoByLabel($label)
    {
        $labels = ['credit_card' => PaymentMethod::PAYMENT_METHOD_CREDIT_CARD,
            'boleto' => PaymentMethod::PAYMENT_METHOD_BOLETO];
        return isset($labels[$label = strtolower($label)]) ? $labels[$label] : 0;
    }
    private function setPaymentMethod($value)
    {
        $this->payment_method = in_array($value, [PaymentMethod::PAYMENT_METHOD_BOLETO, PaymentMethod::PAYMENT_METHOD_CREDIT_CARD]) ? $value : null;
        return $this;
    }
    private function setPrice($value)
    {
        $this->price = number_format($value, 2);
        return $this;
    }
    /*
     * @param type $card_number
     * @param type $name_on_card
     * @param type $securiy_code
     * @param type $valid_until
     * @param type $installment
     * @param type $description
     * @return \CustomerController
     */
    private function setPaymentInfo($card_number, $name_on_card, $security_code, $valid_until, $installment, $description, $order_id)
    {
        //$card_number = substr($card_number, -4, 4);
        //$card_number = $card_number;

        $this->payment_info = (object) ['order_id' => $order_id,
            'payment_method_id' => $this->getPaymentMethod(),
            'status' => 'pending',
            'card_number' => $card_number,
            'card_name' => $name_on_card,
            //  'card_flag' => $card_flag,
            'security_code' => $security_code,
            'valid_until' => $valid_until,
            'installment' => $installment,
            'description' => $description];
        return $this;
    }
    private function setPayment($value)
    {
        $this->payment = $value;
        return $this;
    }
    public function getOrder()
    {return $this->order;}
    public function getPaymentInfo()
    {return $this->payment_info;}
    public function getPaymentMethod()
    {return $this->payment_method;}
    public function isPaymentMethodCard()
    {return ($this->getPaymentMethod() == PaymentMethod::PAYMENT_METHOD_CREDIT_CARD);}
    public function isPaymentMethodBoleto()
    {return ($this->getPaymentMethod() == PaymentMethod::PAYMENT_METHOD_BOLETO);}
    public function getPayment()
    {return $this->payment;}
    public function getPrice()
    {return $this->price;}

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
        $customerModel = new Application_Model_Customer();
        $list = $customerModel->getList();
        foreach ($list as $i => $customer) {
            $list[$i]['name'] = utf8_encode(ucwords(strtolower(stripslashes($customer['name']))));
            $list[$i]['current_box'] = utf8_encode(stripslashes($customer['current_box']));
            $list[$i]['addresses'] = utf8_encode(stripslashes($customer['addresses']));
        }
        echo json_encode($list);
    }

    public function loginAction()
    {
        if ($this->_hasParam("email") && $this->_hasParam("password")) {
            $customerModel = new Application_Model_Customer();
            $login = strip_tags(addslashes($this->getRequest()->getPost("email")));
            $password = md5($this->getRequest()->getPost("password") . self::$_salt);
            $userData = $customerModel->getByLoginAndPassword($login, $password);
            if ($userData) {
                $userData['name'] = utf8_encode(stripslashes($userData['name']));
                $userData['addresses'] = utf8_encode(stripslashes($userData['addresses']));
                echo json_encode($userData);
            } else {
                echo "not_found";
            }
        } else {
            echo "not_found";
        }
    }

    public function registerAction()
    {
        if ($this->_hasParam("Dados")) {
            $dados = $this->getRequest()->getPost("Dados");
            $customerModel = new Application_Model_Customer();
            if ($customerModel->getByEmail($dados['EMail'])) {
                echo "user_exists";
            } else {
                $newCustomer = $customerModel->insert(array(
                    "name" => utf8_decode(strip_tags(addslashes($dados['NomeCompleto']))),
                    "cpf" => utf8_decode(strip_tags(addslashes($dados['CPF']))),
                    "email" => utf8_decode(strtolower(strip_tags(addslashes($dados['EMail'])))),
                    "password" => md5($dados['Senha'] . self::$_salt),
                    "gender" => utf8_decode(strip_tags(addslashes($dados['Sexo']))),
                    "birth_date" => utf8_decode(strip_tags(addslashes($dados['DataNascimento']))),
                    "phone" => utf8_decode(strip_tags(addslashes($dados['Telefone']))),
                    "newsletter" => utf8_decode(strip_tags(addslashes($dados['Newsletter']))),
                    "avatar" => "http://i.pravatar.cc/300",
                ));
                if ($newCustomer) {
                    echo json_encode(array(
                        "id" => $newCustomer,
                        "name" => $dados['NomeCompleto'],
                        "cpf" => $dados['CPF'],
                        "email" => $dados['EMail'],
                        "password" => md5($dados['Senha'] . self::$_salt),
                        "gender" => $dados['Sexo'],
                        "birth_date" => $dados['DataNascimento'],
                        "phone" => $dados['Telefone'],
                        "newsletter" => $dados['Newsletter'],
                    ));
                }
            }
        }
    }

    public function updateAction()
    {
        if ($this->_hasParam("Dados")) {
            $dados = $this->getRequest()->getPost("Dados");
            $customerModel = new Application_Model_Customer();
            $customer = array(
                "name" => utf8_decode(strip_tags(addslashes($dados['name']))),
                "cpf" => utf8_decode(strip_tags(addslashes($dados['cpf']))),
                "email" => utf8_decode(strtolower(strip_tags(addslashes($dados['email'])))),
                "gender" => utf8_decode(strip_tags(addslashes($dados['gender']))),
                "birth_date" => utf8_decode(strip_tags(addslashes($dados['birth_date']))),
                "phone" => utf8_decode(strip_tags(addslashes($dados['phone']))),
                "newsletter" => utf8_decode(strip_tags(addslashes($dados['newsletter']))),
                "addresses" => utf8_decode(strip_tags(addslashes($dados['addresses']))),
            );
            if ($dados['new_password'] != "") {
                $customer['password'] = md5($dados['new_password'] . self::$_salt);
            }
            echo $customerModel->save($customer, $dados['id']);
        }
    }

    public function getordersAction()
    {
        if ($this->_hasParam("id")) {
            $customer = $this->getRequest()->getParam("id");
            $customerOrderModel = new Application_Model_CustomerOrder();
            $orders = $customerOrderModel->getByCustomer($customer);
            foreach ($orders as $i => $order) {
                $orders[$i]['address'] = json_decode(utf8_encode($order['address']));
                $orders[$i]['coupon'] = json_decode(utf8_encode($order['coupon']));
                $orders[$i]['items'] = array();
                $orders[$i]['final_value'] = $order['total_amount'] + $order['total_shipping'] - $order['shipment_discount'];
                $list = $customerOrderModel->getItems($order['id']);
                foreach ($list as $j => $item) {
                    $list[$j]->name = utf8_encode($item->name);
                }
                $orders[$i]['list'] = $list;
            }
            echo json_encode($orders);
        }
    }
    private function boxTowinehouse($box, $dadosCompra, $winehouseModel, $customerModel)
    {
        $retorno = array(
            "order" => array(
                "date" => date("Y-m-d H:i:s"),
                "number" => "",
                "address" => utf8_decode(json_encode($dadosCompra["address"])),
                "payment_mode" => $dadosCompra["payment_mode"],
                "coupon" => isset($dadosCompra['coupon']) ? utf8_decode(json_encode($dadosCompra["coupon"])) : null,
                "customer_pickup" => intval(($box["shipment"] == "retirar")),
                "shipment" => null,
                "id_winehouse" => $box['winehouse'],
                "id_customer" => $dadosCompra["id_customer"],
                "status" => ($dadosCompra["payment_mode"] == "credit_card") ? "approved" : "open",
                //Para compra em cartão, o status vai pra aprovado após o pagamento
                "invoice_number" => null, //Caso o pagamento seja boleto, gravar o número aqui
                "card_digits" => null,
                "total_amount" => floatval($box['total_frete']),
                "total_shipping" => floatval($box['total_frete'] + $box['desconto_frete']),
                "total_fee" => floatval($box['porcentagem_plataforma']),
                "shipment_discount" => floatval($box['desconto_frete']),
            ),
            "formatted_date" => date("d/m/y"),
            "items" => [],
            "winehouse_data" => (array) $winehouseModel->getById($box['winehouse']),
            "winehouse_shipper" => boolval($winehouseModel->checkIfShipper($box['winehouse'])),
            "customer_data" => $customerModel->getById($dadosCompra['id_customer']),
            "embalagens" => $box['embalagens']
        );
        if ($dadosCompra["payment_mode"] == "credit_card") {
            $retorno["card_payment"] = $dadosCompra["card_payment"];
            $retorno["order"]["card_digits"] = substr($dadosCompra["card_payment"]["number"], -4);
        }
        if ($dadosCompra["payment_mode"] == "boleto") {
            $retorno["order"]["invoice_number"] = "23790.50400 41990.305124 12008.109204 1 78220000019900";
        }
        return $retorno;
    }
    private function isSoldOut($product_id, $productModel, $product)
    {
        $whProduct = $productModel->getFromWinehouse($product_id);

        if ($whProduct->quantity > $product["quant"]) {
            return false;
        }

        return ["error" => "out_of_stock",
            "product_info" => [
                "name" => utf8_encode($whProduct->name),
                "max_count" => $whProduct->quantity,
            ],
        ];
    }
    private function processProduct($products, $productModel)
    {
        $items = [];
        $total_amount = 0;
        foreach ($products as $product) {
            if (($erro = $this->isSoldOut($product["id"], $productModel, $product)) !== false) {
                return (object) $erro;
            }
            $items[] = ["id_winehouse_product" => $product["id"],
                "quantity" => $product["quant"],
                "price" => $product["price"]];
            $total_amount += $product["price"] * $product["quant"];
        }
        return (object) ['items' => $items, 'total_amount' => $total_amount];
    }
    private function prepareCard()
    {
        return (Object) ['card_name' => $this->getPaymentInfo()->card_name,
            'card_number' => $this->getPaymentInfo()->card_number,
            'expiration_month' => substr($this->getPaymentInfo()->valid_until, 0, 2),
            'expiration_year' => empty($y = trim(substr($this->getPaymentInfo()->valid_until, 3, 4))) ? '' : ('20' . $y),
            'security_code' => $this->getPaymentInfo()->security_code];
    }
    private function prepareBoleto($address, $person, $payment)
    {
        return $this;
    }

    /**
     * 30/03/2019 - ALUISIO FERREIRA DE SUSA
     * @TODO no momento esta sendo considerado apenas a forma de pagamento por cartao de credito
     * quando for ativar o boleto a variavel person, adress deverao ser devidamente setadas
     */
    private function pay($description, $order_id, $person = null, $address = null)
    {
        // cria nova instancia de pagamento e verifica autenticação
        $payment = new Payment($this->getPaymentMethod());
        if ($payment->getAuthentication()->error) {
            $payment->setError("Ocorreu um erro ao autenticar no gateway de pagamento. " . $payment->getAuthentication()->error);
            return $this->setPayment($payment);
        }
        if (!$payment->getAuthentication()->access_token) {
            $payment->setError("Ocorreu um erro ao autenticar no gateway de pagamento. Token não disponível.");
            return $this->setPayment($payment);
        }
        // cria uma nova instancia de transação
        $payment->setTransaction(new Transaction((object) ['payment_method_id' => $this->getPaymentMethod(),
            'access_token' => $payment->getAuthentication()->access_token,
            'final_value' => $this->getPrice(),
            'description' => $description,
            'order_id' => $order_id,
            'api_url_base' => $payment->getAuthentication()->getUrlApiBase(),
            'payment_method_object' => $this->isPaymentMethodCard() ? new Card($this->prepareCard()) : new Boleto($this->prepareBoleto($address, $person, $payment)),
        ]));
        $payment->pay();
        return $this->setPayment($payment);

    }
    private function atualizaSaldo($estoqueModel, $order_item, $order)
    {
        foreach ($order_item as $item) {
            $estoqueModel->addEntry(['id_winehouse_product' => $item["id_winehouse_product"],
                'quantity' => -$item["quantity"],
                'description' => "Venda #" . $order["order"]["number"],
                'is_manual' => 0]);
        }
        return $this;
    }
    private function creatOrder($box, $customerOrderModel, $dhlConfig, $dhlAPI, $estoqueModel, $card_payment)
    {
        $order = $customerOrderModel->add($box);
        $this->setPaymentInfo($card_payment['number'],
            $card_payment['holder_name'],
            $card_payment['security_code'],
            $card_payment['expiration'],
            $card_payment['installments'],
            'WineBay', $order['order']['id']);

        $this->atualizaSaldo($estoqueModel, $order['items'], $order);

        if (isset($order['card_payment']['holder_name'])) {
            $order['card_payment']['holder_name'] = utf8_encode($order['card_payment']['holder_name']);
        }
        $order['customer_data']['name'] = utf8_encode($order['customer_data']['name']);
        unset($order['customer_data']['addresses']);
        $order['winehouse_data']['name'] = utf8_encode($order['winehouse_data']['name']);
        $order['winehouse_data']['description'] = utf8_encode($order['winehouse_data']['description']);
        $order['winehouse_data']['address'] = utf8_encode($order['winehouse_data']['address']);
        $order['winehouse_data']['city'] = utf8_encode($order['winehouse_data']['city']);

        // $order['order']['address']['name'] = utf8_encode($order['order']['address']['name']);
        // $order['order']['address']['address'] = utf8_encode($order['order']['address']['address']);
        // $order['order']['address']['region'] = utf8_encode($order['order']['address']['region']);
        // $order['order']['address']['city'] = utf8_encode($order['order']['address']['city']);
        // $order['order']['address']['information'] = utf8_encode($order['order']['address']['information']);
        // $order['order']['address']['dest'] = utf8_encode($order['order']['address']['dest']);

        if ($order['order']['customer_pickup']) {
            return $order;
        }

        $dhlConfig['order'] = $order;
        $shipment = $dhlAPI->validateShipment($dhlConfig);
        $customerOrderModel->save([
            'tracking_code' => $shipment['AirwayBillNumber'],
            'shipping_barcodes' => json_encode($shipment['Barcodes']),
            'shipping_label' => $shipment['LabelImage']['OutputImage'],
        ],
            $order['order']['id']);

        return $order;
    }
    public function addorderAction()
    {
        if (!$this->_hasParam("NovaCompra")) {
            return;
        }
        $finishedOrders = [];
        $customerOrderModel = new Application_Model_CustomerOrder();
        $customerModel = new Application_Model_Customer();
        $winehouseModel = new Application_Model_Winehouse();
        $integrationsModel = new Application_Model_Integrations();
        $productModel = new Application_Model_Product();
        $estoqueModel = new Application_Model_Estoque();
        $dhlConfig = $integrationsModel->get("DHL");
        $dhlConfig = json_decode($dhlConfig[$dhlConfig['current_env']], true);
        $dhlAPI = new Application_Model_DHLAPI();
        $dadosCompra = $this->getRequest()->getPost("NovaCompra");
        // var_dump($dadosCompra); exit;
        $description = 'WineBay';

        $this->setPaymentMethod($this->paymentMethoByLabel($dadosCompra['payment_mode']));

        $boxes = [];
        $valor_total = 0;
        foreach ($dadosCompra["boxes"] as $box) {
            if (!isset($boxes[$box['winehouse']])) {
                $boxes[$box['winehouse']] = $this->boxTowinehouse($box, $dadosCompra, $winehouseModel, $customerModel);
            }

            $produtos_processeds = $this->processProduct($box['products'], $productModel);

            if (isset($produtos_processeds->error)) {
                print json_encode($produtos_processeds);
                exit();
            }

            $boxes[$box['winehouse']]["items"] = $produtos_processeds->items;

            $valor_total += $boxes[$box['winehouse']]["order"]["total_amount"] + $produtos_processeds->total_amount;
            $boxes[$box['winehouse']]["order"]["total_amount"] = number_format($produtos_processeds->total_amount, 2, '.', '');
            $finishedOrders[] = $this->creatOrder($boxes[$box['winehouse']], $customerOrderModel, $dhlConfig, $dhlAPI, $estoqueModel, $dadosCompra['card_payment']);
        }
        /**
         * @TODO
         * Aluisio Ferreira de sousa. 03/04/2019
         * Deveria ser totalizado apenas os pedidos que foram efetivados
         *  Mas como não existe nenhuma checagem se o pedido foi ou nao criado e nem
         * tratativas de erros e rolbacks
         */
        $this->setPrice($valor_total);
        $first_order = reset($finishedOrders);
        $order_id = $first_order['order']['id'];
        //$this->setPayment($this->pay('DESCRIAO de teste ',

        if (empty($error = $this->pay($description, $order_id)->getPayment()->getError()) === false) {
            echo json_encode($error);
        } else {
            echo json_encode($finishedOrders);
        }

    }
}
