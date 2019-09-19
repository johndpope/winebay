<?php
namespace YesPay;

use Helpers\Error;
use Helpers\Util;
use YesPay\PaymentMethod;

class Transaction
{
	private $status = null;
	private $error;
	private $payload;

	/** @var  \Controllers\YesPay\Card */
	private $card;
        private $boleto;
	private $amount;
	private $currency = 'BRL';
	private $description;
	private $payment_type;
	private $capture  = true;
	private $reference_id;
	private $customer_id;
	private $authorization_token;
        private $expiration_date; //:::Data de vencimento para o boleto 
        private $api_url_base;
        private $card_end_point='services/app/Zoop/CreateTransaction';
        private $boleto_end_point='services/app/Zoop/CreateBankSlipTransaction';
        
        public function setApiUrlBase($value){
            $this->api_url_base=$value;
            return $this;
        } 
        
        public function setCard($value){
            $this->card=$value;
            return $this;
        }
        public function setBoleto($value){
            $this->boleto=$value;
            return $this;
        }
        public function setAmount($value){
            $this->amount=$value;
            return $this;
        }       
        public function setDescription($value){
            $this->description=$value;
            return $this;
        }
        public function setPaymentType($value){
            /*
             * @TODO deveria dá um abort caso o metodo de pagamento não for valido.
             * Aluisio Ferreira de Sousa 27/02/2019
             */
            $this->payment_type= $this->paymentMethodIsValid($value) ? $value: 'boleto';
            return $this;
        }
        public function setReferenceId($value){
            $this->reference_id=$value;
            return $this;
        }
        public function setCustomerId($value){
            $this->customer_id=$value;
            return $this;
        }
        public function setAuthorizationToken($value){
            $this->authorization_token=$value;
            return $this;
        }
        private function setError($value){
            $this->error=$value;
            return $this;
        }
        private function setStatus($value){
            $this->status=$value;
            return $this;
        }
        private function setPayload($value){
            $this->payload=$value;
            return $this;
        }
        private function setExpirationDate(){
            /**
             * @TODO Assumindo que o boleto sempre terá como data de vencimento um dia
             * Obs.: Não esta sendo verificado a data do pedido, teoricamente sempre que o pedido for gerado será gerado o boleto.
             * Se for criado algum recurso para reeimpressão ou qualquer funcionalidade necessarios ajustes.
             * Aluisio Ferreira de Sousa. 28/02/2019
             */
            $to_day=new \DateTime();
            $to_day->modify('+1 day');
            $this->expiration_date=$to_day->format('Ymd');
            return $this;
        }
        
        /**
         * Depende do payment_method_id
         */
        public function setPaymentMethodObject($payment_type_instance){
            if($this->isCardType()){ return $this->setCard($payment_type_instance); }
            if($this->isBoletoType()){ return $this->setBoleto($payment_type_instance); }
            return $this;
        }
        public function getCapture(){ return $this->capture;}
        public function getCurrency(){ return $this->currency;}
        public function getCard(){ return $this->card; }
        public function getBoleto(){ return $this->boleto;}
        public function getAmount(){ return $this->amount;} 
        public function getAmountFormatted(){ return str_replace(".", '', str_replace(',', '', $this->getAmount()));}
        public function getDescription(){ return $this->description;}
        public function getPaymentType(){return $this->payment_type;}
        public function getReferenceId(){ return $this->reference_id;}
        public function getCustomerId(){ return $this->customer_id;}
        public function getAuthorizationToken(){return $this->authorization_token;}
        public function getPaymentMethodObject(){
            if($this->isCardType()){ return $this->getCard(); }
            if($this->isBoletoType()){ return $this->getBoleto(); }
            return false;
        }
        public function getError(){ return $this->error;}
        public function getStatus(){ return $this->status;}
        public function getPayload(){ return $this->payload;}
        public function getExpirationDate(){ return $this->expiration_date; }
        public function getApiUrlBase(){ return $this->api_url_base;}
        public function getcardEndPoint(){ return $this->card_end_point; }
        public function getBoletoEndPoint(){ return $this->boleto_end_point; }
        public function getEndPointByPaymentMethod(){ return $this->isCardType() ? $this->getcardEndPoint() : $this->getBoletoEndPoint(); }          
        public function isCardType($value=null){
            $payment_method=!$value ? $this->getPaymentType() : $value;
            return $payment_method == PaymentMethod::PAYMENT_METHOD_CREDIT_CARD;
        }
        public function isBoletoType($value=null){ 
            $payment_method=!$value ? $this->getPaymentType() : $value;
            return $payment_method == PaymentMethod::PAYMENT_METHOD_BOLETO;
        }
        public function paymentMethodIsValid($value=null){ 
             $payment_method=!$value ? $this->getPaymentType() : $value;
            return $this->isCardType($payment_method) || $this->isBoletoType($payment_method); 
            
        }
        public function __construct($dados=null) {
            $this->setExpirationDate();
            return !$dados? $this: $this->prepare($dados);
        }
        public function prepare($dados){
            return $this->setAuthorizationToken($dados->access_token)
                ->setAmount(number_format($dados->final_value, 2))
		->setDescription($dados->description)
		->setReferenceId($dados->order_id)
                ->setPaymentType($dados->payment_method_id)
                ->setPaymentMethodObject($dados->payment_method_object)
                ->setApiUrlBase($dados->api_url_base)    ;
        }        
        
	public function save(){
            
                if (!$this->validate()) return false;
                $url=$this->getApiUrlBase().$this->getEndPointByPaymentMethod();  
                $transaction = \Helpers\Curl::doRequest('POST', $url,
                            $data=$this->getPaymentMethodObject()->preparedToSend($this), 
                            ['Content-Type: application/json', 'Authorization: Bearer '.$this->getAuthorizationToken()]);
                
                
                
                
               // Util::dumpExit($transaction);   
                /*
            $d= '==============================================\n\r'.PHP_EOL;    
            $a=json_encode(isset($transaction['content'])? $transaction['content'] :'sem conteudo').'---\n\r'.PHP_EOL;
            $b=json_encode($transaction).'\n\r---'.PHP_EOL;
            $c='-----------------------------------------------\n\r'.PHP_EOL;
            $content_log=$d.$data.$a.$b.$c;              
            file_put_contents(LOG_PATH.DIRECTORY_SEPARATOR.'culr_log.txt', $content_log, FILE_APPEND );
                */
                
                
		if (isset($transaction['content']) && $transaction['content']) {
			$content=$this->setPayload(json_decode($transaction['content']))
                           ->setStatus($this->payload->status ? $this->payload->status : null)
                           ->getPayload();
                          
                    if(isset($content->error)){
                        if(isset($content->error)){
                            $this->setError($content->error->message);
                           // var_dump($data);
                           // exit('===============300320191918');
                        }       
                    }       
		}

		return  (!empty($this->getError())) ? false : true;
	}
        
	public function validate(){
		if (empty($this->getAmount())) {
			$this->setError("O valor da compra é obrigatório para cadastro do pagamento.");
			return false;
		} 
		if (empty($this->getReferenceId())) {
                     $this->setError("ID do pedido é obrigatório para cadastro do pagamento.");
                     return false;
		}
                if($this->paymentMethodIsValid()===false){
                    $this->setError("O tipo de pagamento é obrigatório.");
                    return false;
                }
		if (empty($this->getPaymentMethodObject())) {
                    $this->setError($this->isCardType()? "Dados do cartão são obrigatórios para cadastro do pagamento." : "Dados para gerar o boleto são obrigatórios");
                    return false;
		}
                if($this->getPaymentMethodObject()->isValid()===false){
                    $this->setError($this->getPaymentMethodObject()->getError());
                    return false;
                }
		return true;
                //if (empty($this->customer_id)) {
		//	$this->error = Error::getErrorArray(Error::HTTP_BAD_REQUEST, "ID do comprador é obrigatório para cadastro do pagamento.");
		//
		//	return false;
		//}
	}
}
