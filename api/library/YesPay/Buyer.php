<?php
namespace libary\YesPay;

use Helpers\Util;
use Models\State;

class Buyer
{
	private $success = false;
	private $id;
	private $error;
	private $error_message;
	private $error_details;
	private $authorization_token;
	/** @var  \Models\Address */
	private $address;
	private $name;
	private $first_name;
	private $last_name;
	private $email;
	private $phone_number;
	private $description;        
        private $birthdate;
        private $taxpayer_id;
        private $api_url_base;
        
        public function setApiUrlBase($value){
            $this->api_url_base=$value;
            return $this;
        } 
        private function setSuccess($value){
            $this->success=$value;
            return $this;
        }
	private function setId($value){
            $this->id=$value;
            return $this;
        }
	private function setError($value){
            $this->error=$value;
            return $this;
        }
	private function setErrorMessage($value){
            $this->error_message=$value;
            return $this;
        }
	private function setErrorDetails($value){
            $this->error_details=$value;
            return $this;
        }
        public function setAuthorizationToken($value){
           $this->authorization_token=$value;
            return $this;
        }
        public function setBirthdate($value){
            $this->birthdate=Util::validateDate($value, 'Y-m-d') ? Util::parseDateFormat($value) : (Util::validateDate($value, 'd-m-Y')? $value: '');
            return $this;
        }
        public function setTaxpayerId($value){
            $this->taxpayer_id=Util::isCPF($value) ? Util::addMaskCPF($value) : (Util::isCNPJ($value) ? Util::addMaskCNPJ($value): '');
            return $this;
        }
	/** @var  \Models\Address */
	public function setAddress($value){
            $this->address=$value;
            return $this;
        }
        /*
	public function setName($value){
            $this->name=$value;
            return $this;
        }
         * */
	public function setFirstName($value){
            $this->first_name=$value;
            return $this;
        }
	public function setLastName($value){
            $this->last_name=$value;
            return $this;
        }
	public function setEmail($value){
            $this->email=$value;
            return $this;
        }
	public function setPhoneNumber($value){
            $this->phone_number=$value;
            return $this;
        }
        public function setDescription($value){
            $this->description=$value;
            return $this;
        }
        
        public function getSuccess(){ return $this->success;}
	public function getId(){ return $this->id;}
	public function getError(){ return $this->error;}
	public function getErrorMessage(){ return $this->error_message;}
	public function getErrorDetails(){ return $this->error_details; }
        public function getAuthorizationToken(){ return $this->authorization_token;}
        /** @var  \Models\Address */
	public function getAddress(){ return $this->address;}
	public function getName(){return empty($this->getFirstName())? '' : trim($this->getFirstName().' '.$this->getLastName());}
	public function getFirstName(){ return $this->first_name;}
	public function getLastName(){ return $this->last_name;}
	public function getEmail(){return $this->email;}
	public function getPhoneNumber(){ return $this->phone_number;}
        public function getDescription(){ return $this->description;}
        public function getBirthdate(){ return $this->birthdate;}
        public function getTaxpayerId(){ return $this->taxpayer_id;}
        public function getApiUrlBase(){ return $this->api_url_base;}
        
	public function __construct($dados=null) {
            return !$dados? $this : $this->prepare($dados);
        }
        
        public function prepare($dados){        
            return $this->setFirstName(isset($dados->first_name)? $dados->first_name: '')
                    ->setLastName(isset($dados->last_name)? $dados->last_name: '')
                     ->setDescription(isset($dados->description)? $dados->description: '')
                    ->setEmail(isset($dados->email)?$dados->email:'')
                    ->setPhoneNumber(isset($dados->phone_number)?$dados->phone_number:'')
                    ->setAddress(isset($dados->address)? $dados->address : '')
                    ->setAuthorizationToken(isset($dados->authorization_token)? $dados->authorization_token : '')
                    ->setTaxpayerId(isset($dados->taxpayer_id)? $dados->taxpayer_id : '')
                    ->setBirthdate(isset($dados->birthdate) ? $dados->birthdate : '');
                    
        }
        public function getDataJson(){
            /*
             return '{
                    "ambient": "production",
                    "input": {
                      "first_name": "'.$this->getFirstName().'",
                      "last_name": "",
                      "email": "'.$this->getEmail().'",
                      "phone_number": "'.$this->getPhoneNumber().'",
                      "description": "",
                      "taxpayer_id": "99082209349", 
                      "birthdate": "'.$this->getBirthdate().'",    
                      "address": {
                            "line1": "rua de teste",
                            "line2": "",
                            "line3": "",
                            "neighborhood": "Bairro de teste",
                            "city": "Campinas",
                            "state": "SP",
                            "postal_code": "'.$this->address->zipcode.'",
                            "country_code": "BR"
                      }
                    }  
		}';             * 
             */
            return '{
                    "ambient": "production",
                    "input": {
                      "first_name": "'.$this->getFirstName().'",
                      "last_name": "'.$this->getLastName().'",
                      "email": "'.$this->getEmail().'",
                      "phone_number": "'.$this->getPhoneNumber().'",
                      "description": "'.$this->getDescription().'",
                      "taxpayer_id": "'.$this->getTaxpayerId().'", 
                      "birthdate": "'.$this->getBirthdate().'",    
                      "address": {
                            "line1": "'.$this->address->street.'",
                            "line2": "",
                            "line3": "",
                            "neighborhood": "'.$this->address->neighborhood.'",
                            "city": "'.$this->address->city.'",
                            "state": "'.$this->address->state.'",
                            "postal_code": "'.$this->address->zipcode.'",
                            "country_code": "BR"
                      }
                    }  
		}';
        }
	public function save(){
            $url=$this->getApiUrlBase().'services/app/Zoop/CreateBuyer';
             
            if (!$this->validate()) return false;
            
            $auth = \Helpers\Curl::doRequest('POST', 
                        $url, $data = $this->getDataJson(), 
                        ['Content-Type: application/json', 'Authorization: Bearer '.$this->authorization_token]);
           
           /*
            $a=json_encode(isset($auth['content'])? $auth['content'] :'sem conteudo')."---\nr".PHP_EOL;
            $b=json_encode($auth).'\n\r---'.PHP_EOL;
            $c="@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@\nr".PHP_EOL;
            $content_log="*********************************************************\n\r".PHP_EOL.$data.$a.$b.$c;
                                
            file_put_contents(LOG_PATH.DIRECTORY_SEPARATOR.'culr_log.txt', $content_log, FILE_APPEND );
             */   
                
                
            if (isset($auth['content']) && $auth['content']) {
                	$this->content       = json_decode($auth['content']);
			$this->setErrorMessage($this->content->error->message ?$this->content->error->message: '')
                            ->setErrorDetails($this->content->error->details ? $this->content->error->details :  '')
                            ->setError($this->error_message.($this->error_details ? ' '.$this->error_details : ''))
                            ->setSuccess($this->content->success)
                            ->setId($this->content->result->id ? $this->content->result->id : null);
		}
               
                if ($this->getSuccess() == false || empty($this->getId())) {
                    return false;
		}
              return true;
	}


	public function validate(){
            if (empty($this->getAuthorizationToken())) {
			$this->setError("Token de autorização necessário para cadastrar comprador.");
			return false;
		}

		if (empty($this->getName())) {
			$this->setError("Nome do comprador é obrigatório para cadastro do pagamento.");
			return false;
		}

		if (empty($this->getEmail())) {
			$this->setError("Email do comprador é obrigatório para cadastro do pagamento.");
			return false;
		}

		if (empty($this->getPhoneNumber())) {
			$this->setError("Telefone do comprador é obrigatório para cadastro do pagamento.");
			return false;
		}

		if (empty($this->getAddress())) {
			$this->setError("Endereço é obrigatório para cadastro do pagamento.");
			return false;
		}

		if (empty($this->getAddress()->street)) {
			$this->setError("O nome da rua é obrigatório para cadastro do pagamento.");
			return false;
		}

		if (empty($this->getAddress()->neighborhood)) {
			$this->setError("O bairro é obrigatório para cadastro do pagamento.");
			return false;
		}

		if (empty($this->getAddress()->city)) {
			$this->setError("A cidade é obrigatória para cadastro do pagamento.");
			return false;
		}
                $address=$this->getAddress();
		$address->state = is_numeric($address->state)? (new State($address->state))->short_name :  null;

		if (empty($address->state)) {
			$this->setError("O estado é obrigatório para cadastro do pagamento.");
			return false;
		}
		if (empty($address->zipcode)) {
			$this->setError(Error::HTTP_BAD_REQUEST, "O CEP é obrigatório para cadastro do pagamento.");
			return false;
		}
                $this->setAddress($address);
		return true;
	}
}

/**
 * 
 *  $teste='mokadoxx';
            if($teste=='mokado'){
             return '{
                    "ambient": "production",
                    "input": {
                      "first_name": "Aluisio Ferreira",
                      "last_name": "",
                      "email": "aluisio@turne.app",
                      "phone_number": "19982481299",
                      "description": "",
                      "taxpayer_id": "990.822.093-49", 
                      "birthdate": "07/10/1982",    
                      "address": {
                            "line1": "Rua Paulo Fabiano Sales, 186",
                            "line2": "",
                            "line3": "",
                            "neighborhood": "",
                            "city": "4360",
                            "state": "SP",
                            "postal_code": "12345688",
                            "country_code": "BR"
                      }
                    }  
		}';
            } 
 */