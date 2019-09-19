<?php
namespace YesPay;

class Authentication
{
	private $username    = "api.user";
	private $password    = "71806413"; 
	private $tenancyName = "almaciente";
	private $content;
        private $url_scheme;
        private $url_yes_pay='https://api-yespay.azurewebsites.net/api/';
        private $url_zoop='https://api.zoop.ws/v1/api/';//'https://api.zoop.ws/v1/services/app/Zoop/api/';
        // https://api.zoop.ws/v1/marketplaces/
	public $success = false;
	public $access_token;
	public $error;
	public $error_message;
	public $error_details;
        
        public function setUrlScheme($value){
            $this->url_scheme='yes_pay';//$value=='zoop'? 'zoop' : 'yes_pay';
            return $this;
        }
        public function getUrlZoop(){ return $this->url_zoop; }
        function getUrlYesPay(){ return $this->url_yes_pay; }
        public function getUrlScheme(){ return  $this->url_scheme; }
        
	public function __construct($url_scheme='yes_pay')
	{
		$this->setUrlScheme($url_scheme)->authenticate();
	}
        public function getUrlApiBase(){
            return $this->getUrlYesPay();//$this->getUrlScheme() == 'zoop' ? $this->getUrlZoop() : $this->getUrlYesPay(); 
        }

	public function authenticate(){
		$data = '{
                            "userNameOrEmailAddress": "'.$this->username.'",
                            "password": "'.$this->password.'",
                            "tenancyName": "'.$this->tenancyName.'"
			}';
                //'https://api-yespay.azurewebsites.net/api/TokenAuth/AuthenticateWithTenant'
                $url=$this->getUrlApiBase().'TokenAuth/AuthenticateWithTenant';
                //Authorization: `Basic ${btoa(`${this.apiKey}:${this.apiSecret}`)}`,
		$auth = \Helpers\Curl::doRequest('POST', $url, $data, ['Content-Type: application/json']);
               
                if (isset($auth['content']) && $auth['content']) {
			$this->content       = json_decode($auth['content']);
			$this->error_message = $this->content->error->message ? $this->content->error->message : '';
			$this->error_details = $this->content->error->details ? $this->content->error->details : '';
			$this->error         = $this->error_message.($this->error_details ? ' '.$this->error_details : '');
			$this->success       = $this->content->success;
			$this->access_token  = $this->content->result->accessToken ? $this->content->result->accessToken :  null;
		}
	}
}