<?php

/**
 * @package			Calendar Component
 * @subpackage	User View
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class CalendarViewTesting extends JViewLegacy
{
	function display($tpl = null) 
	{
        // ini_set('display_errors','On'); 
        // error_reporting(E_ALL);
        // ini_set('soap.wsdl_cache_enabled',0);
        // ini_set('soap.wsdl_cache_ttl',0);

        echo '<pre>';
        var_dump($this->createAddInfo('Tomáš Matúšek', 'tomas.matusek@hotmail.co.uk', '+421944290079',
        'Tomáš Matúšek', 'Vajnorská 38', 'Bratislava', '83103', '703', 
        'Tomáš Matúšek', 'Vajnorská 38', 'Bratislava', '83103', '703'));
        echo '</pre>';

        /* try {
            $payment = new WebpayWsSearvice();
            $response = $payment->getPaymentDetail(202108185635876);
            echo '<pre>';
            var_dump($response->status);
            echo '</pre>';
            die();
        } catch(Exception $e) {
            echo '<pre>';
            var_dump($e);
            echo '</pre>';
            die();
        } */
asdasd
        parent::display($tpl);
    }
}