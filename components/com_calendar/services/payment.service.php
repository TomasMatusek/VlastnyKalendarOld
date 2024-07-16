<?php

class PaymentService {

    // Configuration
    private $testing = false;
    private $merchantNumber = '';
    private $privateKeyFile = '';
    private $privateKeyPass = '';
    private $publicKeyFile = '';

    // Static configuration
    private $lang = null;
    private $email = null;
    private $recurrent = false;
    private $referenceNumber = null;
    private $payMethod = null;
    private $addInfo = null;
    private $currency = 978;
    private $depositFlag = 1;
    private $replyUrl = '';
    private $description = 'vlastnykalendar';
    private $operation = 'CREATE_ORDER';
    private $md = 'dda7bab6bf2a11eb85290242ac130003';
    
    // Endpoints
    private $endpointTesting = 'https://test.3dsecure.gpwebpay.com/pgw/order.do';
    private $endpointProduction = 'https://3dsecure.gpwebpay.com/pgw/order.do';

    function __construct($merchantNumber, $privateKeyFile, $privateKeyPass, $publicKeyFile, $replyUrl, $testing) {
        $this->merchantNumber = $merchantNumber;
        $this->privateKeyPass = $privateKeyPass;
        $this->privateKeyFile = $_SERVER["DOCUMENT_ROOT"] . $privateKeyFile;
        $this->publicKeyFile = $_SERVER["DOCUMENT_ROOT"] . $publicKeyFile;
        $this->replyUrl = $replyUrl;
        $this->testing = $testing;
    }
    
    function generateRandomOrderNumber() {
        return date("Ymd") . rand(1000000, 9999999);
    }
    
    function createPaymentLink($amount, $merOrderNum, $orderNumber, $email) {
        if ($this->testing) {
            $url = $this->endpointTesting;
        } else {
            $url = $this->endpointProduction;
        }

        $parameters = $this->createRedirectionParameters($amount, $merOrderNum, $orderNumber, $email, null);
        return $url . '?' . http_build_query($parameters, null, '&'); 
    }

    function createPaymentLink2($amount, $merOrderNum, $orderNumber, $email, $addInfo) {
        if ($this->testing) {
            $url = $this->endpointTesting;
        } else {
            $url = $this->endpointProduction;
        }

        $parameters = $this->createRedirectionParameters($amount, $merOrderNum, $orderNumber, $email, $addInfo);
        return $url . '?' . http_build_query($parameters, null, '&'); 
    }

    function createRedirectionParameters($amount, $merOrderNum, $orderNumber, $email, $addInfo) {
        $addInfo = ($addInfo === null) ? null : $this->removeTabsAndLines($addInfo);
        
        $digest = $this->calculateDigest($this->privateKeyFile, $this->privateKeyPass, $this->replyUrl, $this->operation, 
            $this->merchantNumber, $orderNumber, $amount, $this->currency, $this->depositFlag, $merOrderNum, 
            $this->description, $this->md, $email, $this->recurrent, $this->referenceNumber, $this->payMethod, $addInfo
        );

        $parameters = array();
        $parameters['MERCHANTNUMBER'] = $this->merchantNumber;
        $parameters['OPERATION'] = $this->operation;
        $parameters['ORDERNUMBER'] = $orderNumber;
        $parameters['AMOUNT'] = $amount;
        $parameters['CURRENCY'] = $this->currency;
        $parameters['DEPOSITFLAG'] = $this->depositFlag;
        $parameters['MERORDERNUM'] = $merOrderNum;
        $parameters['URL'] = $this->replyUrl;
        $parameters['DESCRIPTION'] = $this->description;
        $parameters['MD'] = $this->md;
        $parameters['DIGEST'] = $digest;

        if ($this->lang !== null)
            $parameters['LANG'] = $lang;

        if ($email !== null)
            $parameters['EMAIL'] = $email;

        if ($this->recurrent)
            $parameters['USERPARAM1'] = 'R';

        if ($this->referenceNumber !== null)
            $parameters['REFERENCENUMBER'] = $referenceNumber;

        if ($this->payMethod !== null)
            $parameters['PAYMETHOD'] = $payMethod;

        if ($addInfo !== null)
            $parameters['ADDINFO'] = $addInfo;
        
        return $parameters;
    }
    
    function calculateDigest($privateKeyFile, $privateKeyPass, $replyUrl, $operation,
        $merchantNumber, $orderNumber, $amount, $currency, $depositFlag, $merOrderNum,
        $description, $md, $email, $recurrent, $referenceNumber, $payMethod, $addInfo)
    {        
        $digestSrc = $merchantNumber . "|" . $operation . "|" . $orderNumber . "|" . $amount . "|" . $currency . "|" . $depositFlag . "|" . $merOrderNum . "|" . $replyUrl . "|" . $description . "|" . $md;
        if ($recurrent) $digestSrc .= "|R";
        if ($payMethod != null) $digestSrc .= "|" . $payMethod;
        if ($email !== null) $digestSrc .= "|" . $email;
        if ($referenceNumber !== null) $digestSrc .= "|" . $referenceNumber;
        if ($digestSrc[strlen($digestSrc)-1]=='|') $digestSrc = substr($digestSrc,0,strlen($digestSrc)-1);
        if ($addInfo != null) $digestSrc .= "|" . $addInfo;
    
        return $this->calculateSignature($digestSrc, $privateKeyFile, $privateKeyPass);
    }
    
    function removeTabsAndLines($s) {
        return trim(str_replace("\t"," ",str_replace("\r","",str_replace("\n"," ",$s))));
    }

    function calculateSignature($text, $keyFile, $password) {
        $fp = fopen($keyFile, "r");
        $privatni = fread($fp, filesize($keyFile));
        fclose($fp);
        $pkeyid = openssl_get_privatekey($privatni, $password);
        openssl_sign($text, $signature, $pkeyid);
        $signature = base64_encode($signature);
        return $signature;
    }
    
    function isResponseSignatureValid($uri) {
        $urlParamsPart = str_replace('/index.php?', '', $uri);
        $queryPairs = explode('&', $urlParamsPart);

        $params = array();
        for ($i = 1; $i <= count($queryPairs); $i++) {
            $parts = explode('=', $queryPairs[$i]);
            $params[$parts[0]] = $parts[1];
        }

        $hash = "CREATE_ORDER";
        $paramNames = array('ORDERNUMBER', 'MERORDERNUM', 'MD', 'PRCODE', 'SRCODE', 'RESULTTEXT', 'USERPARAM1', 'ADDINFO', 'TOKEN', 'EXPIRY', 'ACSRES', 'ACCODE', 'PANPATTERN', 'DAYTOCAPTURE', 'TOKENREGSTATUS', 'ACRC', 'RRN', 'PAR', 'TRACEID');
        foreach ($paramNames as $key) {
            if (isset($params[$key])) {
                $hash = $hash . '|' . $params[$key];
            }
        }
        $digest = $params['DIGEST'];

        return $this->verifySignature($hash, urldecode($digest), $this->publicKeyFile);
    }

    function verifySignature($text, $sigb64, $keyFile) {
        $fp = fopen($keyFile, "r");
        $public = fread($fp, filesize($keyFile));
        fclose($fp);
        $pubkeyid = openssl_get_publickey($public);
        $signature = base64_decode($sigb64);
        $result = openssl_verify($text, $signature, $pubkeyid);
        return (($result==1) ? true : false);
    }

    function createAddInfo($name, $email, $phone,
	    $billingName, $billingAddress, $billingCity, $billingPostalCode, $billingCountry,
	    $shippingName, $shippingAddress, $shippingCity, $shippingPostalCode, $shippingCountry) {

        $addressMatch = "Y";
        if ($billingName !== $shippingName || 
            $billingAddress !== $shippingAddress ||
            $billingCity !== $shippingCity) {
                $addressMatch = "N";
            }

        if ($addressMatch === "Y") {
        $xaddInfo = '<?xml version="1.0" encoding="UTF-8"?>
			<additionalInfoRequest xmlns="http://gpe.cz/gpwebpay/additionalInfo/request" version="4.0">
			  <cardholderInfo>
				<cardholderDetails/>
                <addressMatch>Y</addressMatch>
				<billingDetails/>
			  </cardholderInfo>
			</additionalInfoRequest>';
        } else {
            $xaddInfo = '<?xml version="1.0" encoding="UTF-8"?>
			<additionalInfoRequest xmlns="http://gpe.cz/gpwebpay/additionalInfo/request" version="4.0">
			  <cardholderInfo>
				<cardholderDetails/>
                <addressMatch>N</addressMatch>
				<billingDetails/>
                <shippingDetails/>
			  </cardholderInfo>
			</additionalInfoRequest>';
        }

		$xml = simplexml_load_string($xaddInfo);
		$xml->cardholderInfo->cardholderDetails->addChild("name", mb_substr($name, 0, 45));
		$xml->cardholderInfo->cardholderDetails->addChild("email", mb_substr($email, 0, 255));
        $xml->cardholderInfo->cardholderDetails->addChild("phone", mb_substr(str_replace('+', '00', $phone), 0, 15));
        $xml->cardholderInfo->cardholderDetails->addChild("mobilePhone", mb_substr(str_replace('+', '00', $phone), 0, 15));

		$xml->cardholderInfo->billingDetails->addChild("name", mb_substr($billingName, 0, 45));
		$xml->cardholderInfo->billingDetails->addChild("address1", mb_substr($billingAddress, 0, 50));
		$xml->cardholderInfo->billingDetails->addChild("city", mb_substr($billingCity, 0, 50));
		$xml->cardholderInfo->billingDetails->addChild("postalCode", mb_substr($billingPostalCode, 0, 16));
		$xml->cardholderInfo->billingDetails->addChild("country", mb_substr($billingCountry, 0, 3));

        if ($addressMatch == "N") {
            $xml->cardholderInfo->shippingDetails->addChild("name", mb_substr($shippingName, 0, 45));
            $xml->cardholderInfo->shippingDetails->addChild("address1", mb_substr($shippingAddress, 0, 50));
            $xml->cardholderInfo->shippingDetails->addChild("city", mb_substr($shippingCity, 0, 50));
            $xml->cardholderInfo->shippingDetails->addChild("postalCode", mb_substr($shippingPostalCode, 0, 16));
            $xml->cardholderInfo->shippingDetails->addChild("country", mb_substr($shippingCountry, 0, 3));
        }

		return $xml->asXML();
	}
}