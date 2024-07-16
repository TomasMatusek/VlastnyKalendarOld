<?php

defined('_JEXEC') or die('Restricted access');

class SMS {

    public static function sendSMS($phoneNumber) {

        $sms = new StdClass();
        $message = new StdClass();
        $recipients = array();
        $recipient = new StdClass();

        $message->text = CalendarConstants::$SMS_TEXT;
        $message->sender = "Kalendar";
        $message->type = "gsm";

        $recipient->msisdn = str_replace("+", "00", $phoneNumber);
        $recipient->id = time();

        $recipients[0] = $recipient;

        $sms->username = CalendarConstants::$SMS_USER;
        $sms->password = CalendarConstants::$SMS_PASSWORD;
        $sms->message = $message;
        $sms->recipients = $recipients;

        $curl = curl_init(CalendarConstants::$SMS_API_URL);

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($sms));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json'
        ));

        $result = curl_exec($curl);
        $json = json_decode($result, true);

        echo '<pre>';
        var_dump($sms);
        var_dump($json);
        echo '</pre>';
    }
}
