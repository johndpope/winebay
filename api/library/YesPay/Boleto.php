<?php
namespace YesPay;
use YesPay\Buyer;
use Helpers\Util;

class Boleto
{	
     /** @var  \Controllers\YesPay\Buyer */
	private $buyer;
        private $error;
        private $api_url_base;
        
        public function setApiUrlBase($value){
            $this->api_url_base=$value;
            return $this;
        }
        private function setError($value){
            $this->error=$value;
            return $this;
        }
        public function setBuyer($value){
            $this->buyer=new Buyer($value);
            return $this;
        } 
        
        public function getBuyer(){ return $this->buyer; }
        public function getError(){ return $this->error;}
        public function getApiUrlBase(){ return $this->api_url_base;}

	public function __construct($dados) {
           return !$dados? $this : $this->fillFields($dados);            
        }
        
         public function getAttr(){
             return (object)['buyer'=>$this->getBuyer(),
                            'api_url_base'=>$this->getApiUrlBase(),
                            'error'=>$this->getError()];
         }
         public function fillFields($payment_info){
            return $this->setApiUrlBase($payment_info->url_api_base)
                        ->setBuyer($payment_info->buyer);
        }
        public function isValid(){
             if(empty($this->getBuyer())){
                 $this->setError('Dados do comprado incorretos.');
                 return false;
             }
            if($this->getBuyer()->setApiUrlBase($this->getApiUrlBase())->save()===false){
                $this->setError($this->getBuyer()->getError());
                return false;
            }
            return true;
        }
        public function preparedToSend($transaction){
             $to_day=new \DateTime();
            $to_day->modify('+2 day');
            $limit_date=$to_day->format('Y-m-d');
            
            /*
             $teste='simples'; //'complex' //'dinamic';
            if($teste=='simples'){
                return '{
                        "ambient": "production",
                        "input": {
                            "amount": 300,
                            "currency": "BRL",
                            "description": "venda",
                            "customer": "'.$this->getBuyer()->getId().'"
                        }
                    }';
           }
            */
            
            return '{"ambient": "production",
                     "input": {
                        "amount": '.$transaction->getAmountFormatted().',
                        "currency": "BRL",
                        "description":  "'.$transaction->getDescription().'",
                         "reference_id": "'.$transaction->getReferenceId().'",   
                        "customer": "'.$this->getBuyer()->getId().'",
                        "logo": "https://turne.app/beta/assets/img/top_logo.png",
                        "payment_method": {
                                "expiration_date": "'.$transaction->getExpirationDate().'",
                                "payment_limit_date":"'.$limit_date.'",    
                                "body_instructions": ["Pedido '.$transaction->getReferenceId().'", "'.$transaction->getDescription().'"],    
                            }
                        }
                    } ' ;
            
        }
        
        public function loadByUrl($url){
            
            $conteudo= \Helpers\Curl::doRequest('GET', $url);
            return isset($conteudo['content'])? $conteudo['content'] : '';
        }
        //'.$transaction->getExpirationDate().'
//, "'.$transaction->getDescription().'"

}
/*
 * 
     //'.$this->getBuyer()->getId().'"
            $teste='dinamic'; //'complex' //'dinamic';
            if($teste=='simples'){
                return '{
                        "ambient": "production",
                        "input": {
                            "amount": 300,
                            "currency": "BRL",
                            "description": "venda",
                            "customer": "c7fe18bf20a34c41808d933e01aef2d2"
                        }
                    }';
           }
            if($teste=='complex'){
                return '{
                    "ambient": "production",
                    "input": {
                        "amount": 300,
                        "currency": "BRL",
                        "description": "Venda de ingressos para o show do dia 30",
                        "reference_id": "reference_5132",
                        "customer": "'.$this->getBuyer()->getId().'",
                        "payment_method": {
                            "expiration_date": "2019-03-20",
                            "payment_limit_date": "2019-03-20",
                            "body_instructions": ["Pedido 5132"],
                            "billing_instructions": {
                                "late_fee": {
                                    "mode": "FIXED",
                                    "amount": 500
                                },
                                "interest": {
                                    "mode": "DAILY_AMOUNT",
                                    "amount": 200
                                },
                                "discount": [
                                    {
                                        "mode": "FIXED",
                                        "limit_date": "2019-03-09",
                                        "amount": 150
                                    },
                                    {
                                        "mode": "FIXED",
                                        "limit_date": "2019-03-10",
                                        "amount": 100
                                    }
                                ]
                            }
                        }
                    }
                }';
            }    
 *   //   https://api.zoop.ws/v1/marketplaces/:marketplace_id/buyers
             /*  "payment_method": {
                                "expiration_date": "20",
                                "body_instructions": ["Pedido de teste 363636"],    
                            }*/
            //"{{payment-gateway-buyer-id}}"
             //$transaction->getReferenceId()
            //"payment_type": "boleto",
/*
 * if (empty($this->card->expiration_year)) {
				$this->error = Error::getErrorArray(Error::HTTP_BAD_REQUEST, "Vencimento do cartão é obrigatório para cadastro do pagamento.");

				return false;
			}
 */
