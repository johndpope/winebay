<?php
namespace YesPay;

use Helpers\Error;
use YesPay\PaymentMethod;
/**
 * Class Payment
 * Classe responsável por realizar a intermediação entre o server e as endpoints da YesPay para realização de
 * pagamentos via cartão de crédito ou boleto.
 *
 * @package Controllers\YesPay
 */
class Payment
{
	/** @var  \Controllers\YesPay\Authentication */
	private $authentication;
	/** @var  \Controllers\YesPay\Buyer */
	private $buyer;
	/** @var  \Helpers\Error */
	private $error;
	/** @var  \Controllers\YesPay\Transaction */
	private $transaction;

        private function setError($value){
            $this->error=$value;
            return $this;
        }
        public function setTransaction($value){
            $this->transaction=$value;
            return $this;
        }
        public function setBuyer($value){
            $this->buyer=$value;
            return $this;
        }
        private function setAuthentication($value){
            $this->authentication=$value;
            return $this;
        }        
        public function getError(){ return $this->error;}
        public function getTransaction(){ return $this->transaction; }
        public function getBuyer(){ return $this->buyer; }
        public function getAuthentication(){ return $this->authentication;}
	/**
	 * Payment constructor.
	 *
	 * @param string $payment_type Tipos disponíveis: 2="credit" e 1="boleto"
	 */
        //int $payment_type = 2
	public function __construct()
	{   //PaymentMethod::PAYMENT_METHOD_BOLETO==$payment_type? 'zoop': 'yes_pay'
		$this->setAuthentication(new Authentication('yes_pay'));
	}


	public function pay(){
		if (!$this->validate()) return false;
		if ($this->getTransaction()->save() == false) {
			if (is_array($this->getTransaction()->getError())) {
				$this->setError(json_decode(json_encode(array('code' => $this->getTransaction()->error['code'], 'message' => $this->getTransaction()->error['error']))));
			} else {
				$this->setError($this->getTransaction()->getError());
			}
                        return false;
		}
                return true;
	}


	public function validate(){
		if (empty($this->getTransaction())) {
                       $this->setError("Dados do comprador é obrigatório para cadastro do pagamento.");
                       return false;
		}
		return true;
	}


	public function validateAll(){
		if (empty($this->getTransaction())) {
			$this->setError("Os Dados do comprador são obrigatórios para cadastro do pagamento.");
			return false;
		} 
                if ($this->getTransaction()->validate() == false) {
			$this->setError($this->getTransaction()->getError());
			return false;
		}
		return true;
	}

}