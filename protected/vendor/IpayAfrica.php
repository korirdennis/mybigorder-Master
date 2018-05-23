<?php
class IpayAfrica
{
	static $message;
	static $api_transact = 'https://apis.ipayafrica.com/payments/v2/transact';
	static $api_mobilemoney = 'https://apis.ipayafrica.com/payments/v2/transact/mobilemoney';
	static $api_stk = 'https://apis.ipayafrica.com/payments/v2/transact/push/mpesa';
	static $api_cc = 'https://apis.ipayafrica.com/payments/v2/transact/cc';
	
	public static function getCredentials($merchant_id='')
	{
		
		$enabled = false;
		$mode = '';		
		$vendor_id='';
		$hashkey='';		
		
		if (FunctionsV3::isMerchantPaymentToUseAdmin($merchant_id)){			
			$mode = getOptionA('admin_ipay_africa_mode');
			$enabled = getOptionA('admin_ipay_africa_enabled');
			
			$vendor_id  = getOptionA('admin_ipay_africa_vendor_id');
			$hashkey  = getOptionA('admin_ipay_africa_hashkey');				
						
			if($mode=="live"){		
				$mode="1";
			} else {
				$mode="0";
			}
		} else {
			
			$mode = getOption($merchant_id,'merchant_ipay_africa_mode');
			$enabled = getOption($merchant_id,'merchant_ipay_africa_enabled');
			
			$vendor_id  = getOption($merchant_id,'merchant_ipay_africa_vendor_id');
			$hashkey  = getOption($merchant_id,'merchant_ipay_africa_hashkey');				
						
			if($mode=="live"){		
				$mode="1";
			} else {
				$mode="0";
			}			
		}
				
		if ($enabled==1){
			return array(
			  'mode'=>strtolower($mode),
			  'vendor_id' =>$vendor_id,
			  'hashkey'=>$hashkey
			);
		}
		return false;
	}
	
	public static function getAdminCredentials()
	{
		$enabled = false;
		$mode = '';		
		$vendor_id='';
		$hashkey='';	
		
		$mode = getOptionA('admin_ipay_africa_mode');
		$enabled = getOptionA('admin_ipay_africa_enabled');
		
		$vendor_id  = getOptionA('admin_ipay_africa_vendor_id');
		$hashkey  = getOptionA('admin_ipay_africa_hashkey');				
					
		if($mode=="live"){		
			$mode="1";
		} else {
			$mode="0";
		}
		
		if ($enabled==1){
			return array(
			  'mode'=>strtolower($mode),
			  'vendor_id' =>$vendor_id,
			  'hashkey'=>$hashkey
			);
		}
		return false;
	}
	
	public static function getEnabledProvider()
	{
		$ipay_africa_enabled_payment = getOptionA('ipay_africa_enabled_payment');
		if(!empty($ipay_africa_enabled_payment)){
			$ipay_africa_enabled_payment = json_decode($ipay_africa_enabled_payment,true);
			if(is_array($ipay_africa_enabled_payment) && count($ipay_africa_enabled_payment)>=1){
				$list = array();
				foreach ($ipay_africa_enabled_payment as $val) {
					$list[]= array(
					  'label'=>strtoupper(t($val)),
					  'key'=>$val
					);
				}
				return $list;
			}
		}
		return false;
	}
	
	public static function getProviderInstructions($key='')
	{
		$content = '';
		switch ($key) {
			case "mpesa":
				$content = getOptionA('mpesa_content');
				break;
				
			case "airtel":
				$content = getOptionA('airtel_content');
				break;	
		
			default:
				break;
		}
		return $content;
	}
	
	public static function ifSuccess($status='')
	{
		$pattern = array(
		  'fe2707etr5s4wq'=>2,
		  'aei7p7yrx4ae34'=>1,
		  'bdi6p2yy76etrs'=>2,
		  'cr5i3pgy9867e1'=>2,
		  'dtfi4p7yty45wq'=>2,
		  'eq3i7p5yt7645e'=>1
		);

		if (array_key_exists($status,$pattern)){
			if ( $pattern[$status]==1){
				return true;
			}
		}
		return false;
	}
	
	public static function statusMeaning($status='')
	{		
		$pattern = array(
		  'fe2707etr5s4wq'=>t("Failed transaction. Not all parameters fulfilled. A notification of this transaction sent to the merchant."),
		  'aei7p7yrx4ae34'=>t("The transaction is valid."),
		  'bdi6p2yy76etrs'=>t("Incoming Mobile Money Transaction Not found. Please try again in 5 minutes."),
		  'cr5i3pgy9867e1'=>t("This code has been used already."),
		  'dtfi4p7yty45wq'=>t("The amount that you have sent via mobile money is LESS than what was required to validate"),
		  'eq3i7p5yt7645e'=>t("The amount that you have sent via mobile money is MORE than what was required to validate this transaction"),
		);
		
		/*'fe2707etr5s4wq'=>t("Failed transaction. Not all parameters fulfilled."),
		  'aei7p7yrx4ae34'=>t("Success: The transaction is valid."),
		  'bdi6p2yy76etrs'=>t("Pending: Incoming Mobile Money Transaction Not found."),
		  'cr5i3pgy9867e1'=>t("Used: This code has been used already."),
		  'dtfi4p7yty45wq'=>t("Less: The amount that you have sent via mobile money is LESS than what was required to validate"),
		  'eq3i7p5yt7645e'=>t("More: The amount that you have sent via mobile money is MORE than what was required to validate this transaction"),*/

		if (array_key_exists($status,$pattern)){
			return $pattern[$status];
		} else return t("Undefined error status");
	}
	
	public static function verifyOrder($order_id='', $vendor_id="",$hash_key='')
	{				
		$datastring = $order_id.$vendor_id;
        $generated_hash = hash_hmac('sha256',$datastring , $hash_key); 
        
        $fields = array(
		  'vid'=>$vid,
		  'hash'=>$generated_hash,
		  'oid'=>$oid
		);		
		$content = http_build_query($fields);		
	}
	
	public static function initiatorRequest($params=array(), $hashkey='')
	{		
		$params_string=$params['live'].$params['oid'].$params['inv'].$params['amount'].$params['tel'].$params['eml'].$params['vid'].$params['curr'].$params['cst'];		
		$generated_hash = hash_hmac('sha256',$params_string , $hashkey);        
        $params['hash']=$generated_hash;
        //dump($params);
        $content = http_build_query($params);
        
        $curl = curl_init(self::$api_transact);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		$response = curl_exec($curl);
		if ($response===FALSE){
			self::$message = t("CURL ERROR").": ".curl_error($curl);
		} else {
			$json_resp = json_decode($response,true);			
			if(is_array($json_resp) && count($json_resp)>=1){				
				//dump($json_resp);
				switch ($json_resp['header_status']) {
					case "200":
						return $json_resp['data'];
						break;
					case "400":
						if(is_array($json_resp['error']) && count($json_resp['error'])>=1){
							foreach ($json_resp['error'] as $val) {
								self::$message.= $val['text']."\n";
							}
						} else self::$$message = "error is undefined";
					    break;					    
					default:
						self::$message = "undefined header status";
						break;
				}
			} else self::$message = "invalid response from api";
		}
		return false;
	}
		
	public static function call($link='', $content='')
	{
		$curl = curl_init($link);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		$response = curl_exec($curl);		
		if ($response===FALSE){
			self::$message = t("CURL ERROR").": ".curl_error($curl);
		} else {
			$json_resp = json_decode($response,true);			
			if(isset($_GET['debug'])){
				dump($json_resp);
			}			
			if(is_array($json_resp) && count($json_resp)>=1){
				return $json_resp;
			} else self::$message = "invalid response from api";
		}	
		return false;
	}
	
	public static function parseError($data=array())
	{		
		$error_text = '';
		if(is_array($data) && count($data)>=1){
		   foreach ($data as $val) {	
		   	    if(isset($val['text'])){
		   		   $error_text.=$val['text']."\n";
		   	    } else $error_text.=$val;
		   	}	
		} else $error_text = "undefined error";
		return $error_text;
	}
	
} /*END CLASS*/