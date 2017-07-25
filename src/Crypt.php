<?php
    namespace Stone;
    
    class Crypt
    {
		private $passphrase;
		private $ttl;
		protected $defaultConfiguration = [
    		'VERIFY_CRYPT_PASSPHRASE'=>null,
    		'VERIFY_CRYPT_TTL'=> -1
		];
		
		function __construct($userCfg=[])
		{
            $configure = new Configuration($this->defaultConfiguration, $userCfg);
    		$this->ttl = $configure->VERIFY_CRYPT_TTL;
			$this->passphrase = $configure->VERIFY_CRYPT_PASSPHRASE;
		}
		
		function expire($ttl=null)
		{
			if(is_null($ttl)) {
				return $this->ttl;
			} else {
				$this->ttl = $ttl;
			}
		}
		
		function expired($ttl) 
		{
    		$ttl = (int) $ttl;
    		if($ttl===-1) {
        		return false;
    		}
    		$time = time();
    		if($ttl < $time) {
        		return true;
    		} else {
        		return false;
    		}
		}
		
		function encrypt($data, $ttl=null)
		{
			if(is_null($ttl)) {
				$ttl = $this->ttl;
			}
			if($ttl > 0) {
			    $time = time();
			    $ttl += $time;
			}
			$data = $ttl.':'.$data;
			$iv = $this->randomIv();
			$encryptData = openssl_encrypt($data, "BF-CBC", $this->passphrase, null, $iv);
            return $encryptData.':'.base64_encode($iv);
		}
		
		function decrypt($data)
		{
			$encryptData = explode(":", $data, 2);
			if(count($encryptData)!==2) {
    			return false;
			}
			$iv = base64_decode($encryptData[1]);
		    $data = openssl_decrypt($encryptData[0], "BF-CBC", $this->passphrase, 0,  $iv);
			$data = explode(":", $data, 2);
			
			if(count($data) !== 2){
				return false;
			}
			list($ttl, $data) = $data;
            if($this->expired($ttl)) {
                return false;
            } else {
                return rtrim($data);
            }
		}
		
		function randomIv()
		{
    		return openssl_random_pseudo_bytes(8);
		}
    }
    