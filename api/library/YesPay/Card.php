<?php
namespace YesPay;

class Card
{
	private $holder_name;
	private $expiration_month;
	private $expiration_year;
	private $card_number;
	private $security_code;
        private $error=false;
        
        public function setHolderName($value){
            $this->holder_name=$value;
            return $this;
        }
        public function setExpirationMonth($value){
            $this->expiration_month=substr($value, 0, 2);
            return $this;
        }
        public function setExpirationYear($value){
            $this->expiration_year=  strlen($value)==4 ? $value : substr($value, 3, 4);
            return $this;
        }
        public function setCardNumber($value){
            $this->card_number=$value;
            return $this;
        }
        public function setSecurityCode($value){
            $this->security_code=$value;
            return $this;
        } 
        private function setError($value){
            $this->error=$value;
        }
        public function getHolderName(){ return $this->holder_name; }
        public function getExpirationMonth(){ return $this->expiration_month; }
        public function getExpirationYear(){ return $this->expiration_year; }
        public function getCardNumber(){ return  $this->card_number; }
        public function getSecurityCode(){ return $this->security_code;}
        public function getError(){ return $this->error; }
	public function __construct($payment_info=null) {
            return is_object($payment_info)===false ? $this : $this->fillFields($payment_info);
        }
        public function fillFields($payment_info){
            // cria uma nova instancia de cartão de crédito
            return $this->setHolderName($payment_info->card_name)
                        ->setCardNumber($payment_info->card_number)
                        ->setExpirationMonth($payment_info->expiration_month)
                        ->setExpirationYear($payment_info->expiration_year)
                        ->setSecurityCode($payment_info->security_code);
        }
        public function getformatedNumber(){ return str_replace(".", '', str_replace('-', '', str_replace(' ', '', $this->getCardNumber()))); }
        public function isValid(){
                /*
                 if (empty($this->card)) {
				$this->error = Error::getErrorArray(Error::HTTP_BAD_REQUEST, "Dados do cartão são obrigatórios para cadastro do pagamento.");

				return false;
			}
                */        
               if (empty($this->getCardNumber())) {
                    $this->setError("Número do cartão é obrigatório para cadastro do pagamento.");
                    return false;
                } 

		if (empty($this->getHolderName())) {
                    $this->setError("Nome no cartão é obrigatório para cadastro do pagamento.");
                    return false;
		}
    
                if ($this->getExpirationMonth() < 1 || $this->getExpirationMonth() > 12) {
                    $this->setError("Data de vencimento (mês) do cartão inválida.");
                    return false;
		}

		if (!is_numeric($this->getExpirationYear()) || $this->getExpirationYear() < date('Y')) {
                    $this->setError("Data de vencimento (ano) do cartão inválida.");
                    return false;
		}
    
                if (!is_numeric($this->getSecurityCode())) {
                    $this->setError("Código de segurança do cartão inválido. xxx".$this->getSecurityCode());
                    return false;
		}
                return true;
        }
        public function getAttr(){
            return (object) ['holder_name'=>$this->getHolderName(),
                            'expiration_month'=>$this->getExpirationMonth(),
                            'expiration_year'=>$this->getExpirationYear(),
                            'card_number'=>$this->getCardNumber(),
                            'security_code'=>$this->getSecurityCode()];
        }
        public function preparedToSend($transaction){
            return '
                    {
                        "ambient": "production",
                        "input": {
                            "amount": '.$transaction->getAmountFormatted().',
                            "currency": "BRL",
                            "description": "'.$transaction->getDescription().'",
                            "payment_type": "credit",
                            "capture": true,
                            "reference_id": "'.$transaction->getReferenceId().'",
                            "source": {
                                    "usage": "single_use",
                                    "amount": '.$transaction->getAmountFormatted().',
                                    "currency": "BRL",
                                    "type": "card",
                                    "card": {
                                        "holder_name": "'.$this->getHolderName().'",
                                        "expiration_month": "'.$this->getExpirationMonth().'",
                                        "expiration_year": "'.$this->getExpirationYear().'",
                                        "security_code": "'.$this->getSecurityCode().'",
                                        "card_number": "'.$this->getformatedNumber().'"
                                    }
                                }
                            }
			}
            ';
        }
}
