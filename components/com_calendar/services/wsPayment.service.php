<?php

class WebpayWsService {

	var $wsdl, $MerchantNumber, $MuzoPublicKeyFile, $PrivateKeyFile, $PrivateKeyPass, $mws, $provider, $logger;

	function __construct()
	{
        $this->wsdl = dirname(__FILE__) . '/payment.wsdl';
		$this->MerchantNumber = CalendarConstants::$GP_WEBPAY_MERCHANT_NUMBER;
		$this->MuzoPublicKeyFile = '/home/html/vlastnykalendar.sk/' . CalendarConstants::$GP_WEBPAY_PUBLIC_KEY_FILE;
		$this->PrivateKeyFile = '/home/html/vlastnykalendar.sk/' . CalendarConstants::$GP_WEBPAY_PRIVATE_KEY_FILE;
		$this->PrivateKeyPass = CalendarConstants::$GP_WEBPAY_PRIVATE_KEY_PASS;
		$this->provider = '0902';
        $this->mws = new SoapClient($this->wsdl, [
            'cache_wsdl' => WSDL_CACHE_NONE, 
            'trace' => true, 
            'exceptions' => true, 
            'location' => 'https://3dsecure.gpwebpay.com/pay-ws/v1/PaymentService'
        ]);
	}

	// nasleduji jednotlive funkce poskytovane pres Muzo Webservices
	// navratova hodnota urcuje zda byl podpis muzo ok (true, false)
	// posledni argument $res je pole obsahujici navratove hodnoty funkce WebServices
	// popis jednotlivych funkci je uveden v souboru "PayMuzo - Web Services.pdf" dokumentace dodane od Muzo
	// chybu pri komunikaci se WebServices lze zjistit volanim funkce GetError, ktera pri chybe vrati neprazdny retezec s popisem chyby

	function getPaymentStatus($orderNumber) {
		return $this->callServiceSig('getPaymentStatus','paymentStatus',array('paymentNumber'=>$orderNumber));
	}

	function getPaymentDetail($orderNumber) {
		return $this->callServiceSig('getPaymentDetail','paymentDetail',array('paymentNumber'=>$orderNumber));
	}

	function callServiceSig($funcName, $parNameBase, $params) {
		$messageId = uniqid('CAL');
		$params = array('messageId'=>$messageId, 'provider'=>$this->provider, 'merchantNumber'=>$this->MerchantNumber) + $params;
		$sigstr = implode('|', $params);
		$sig = $this->muzo_Sign($sigstr, $this->PrivateKeyFile, $this->PrivateKeyPass);
		$params['signature'] = base64_decode($sig);
		$params = array($parNameBase.'Request' => $params);
		try {
			$paramsLog = $params; 
            unset($paramsLog[$parNameBase.'Request']['signature']);
			$res = call_user_func([$this->mws, $funcName], $params);
		} catch(Exception $e) {
			throw $e;
		}
		$eRes = unserialize(serialize($res)); unset($eRes->{$parNameBase.'Response'}->signature);
		$res = $res->{$parNameBase.'Response'};
		$sigr = $res->signature;
		$resar = get_object_vars($res);
		unset($resar['signature']);
		$sigrstr = implode('|', $resar);
		// if (!($messageId && muzo_Verify($sigrstr, base64_encode($sigr), $this->MuzoPublicKeyFile))) {
		// 	throw new Exception("GPWP SOAP response validation error: neplatny podpis nebo messageId");
		// }
		return $res;
	}

    function muzo_Sign($text, $keyFile, $password) {
        $fp = fopen($keyFile, "r");
        $privatni = fread($fp, filesize($keyFile));
        fclose($fp);
        $pkeyid = openssl_get_privatekey($privatni, $password);
        openssl_sign($text, $signature, $pkeyid);
        $signature = base64_encode($signature);
        return $signature;
      }

}