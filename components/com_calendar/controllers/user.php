<?php

/**
 * @package			Calendar Component
 * @subpackage	User Controller
 */

defined('_JEXEC') or die;

require_once(JPATH_COMPONENT . '/controller.php');
require_once(JPATH_COMPONENT . '/controllers/admin.php');
require_once(JPATH_COMPONENT . '/controllers/calendar.php');

class CalendarControllerUser extends CalendarController
{
    public function orderform()
    {
        $this->app->redirect('index.php?option=com_calendar&view=calendar&layout=order');
    }

    public function submit()
    {
        $user_id = JFactory::getUser()->get('id');
        if ($user_id == 0) {
            return;
        }

        $input = JFactory::getApplication()->input;

        $coupon_code = $input->get('special_code', '');
        $coupon_id = 0;

        if (strlen(trim($coupon_code)) > 0) {
            $coupon = $this->model->getCouponByCode($coupon_code);

            if ( ! $coupon || ! $coupon->valid) {
                $this->app->redirect(
                    'index.php?option=com_calendar&view=calendar&layout=order',
                    'Neplatný zľavový kupón[]Zadaný zľavový kupón nieje správny. Vložte prosím správny kód kupónu, alebo pole ponechajte prázdne',
                    'error'
                );
            }
            $coupon_id = $coupon && $coupon->valid ? $coupon->id : 0;
        }

        $calendar_ids = $input->getVar('calendar_ids', array());
        $calendar_prices = array();
        $calendar_details = CalendarHelper::getCalendars($user_id, $this->model, true);

        if (count($calendar_details) == 0) {
            $this->app->redirect(
                'index.php?option=com_calendar&view=calendar&layout=order',
                'Ospravedlňujeme sa ale objednávku nie je možné odoslať pretože neobsahuje žiaden dokončený kalendár. Vaše rozpracovené kalendáre sú u nás uložené po dobu 7 dní a po jej uplynutí sú automaticky zmazané. Váš kalendár ste pravdepodobne neodoslali v limite 7 dní a preto bol zmazaný, prosím vytvorte si kalendár znova. Ďakujeme.',
                'error'
            );
        }

        foreach ($calendar_details as $calendar_detail) {
            $calendar_prices[$calendar_detail['cal_id']] = $calendar_detail['price_with_cover'];
        }

        $order_id = $this->model->saveOrderDetail(
            $user_id,
            $coupon_id,
            $input->getVar('billing_name', ''),
            $input->getVar('billing_city', ''),
            $input->getVar('billing_address', ''),
            $input->getVar('billing_address_number', ''),
            $input->getVar('billing_zip', ''),
            $input->getVar('billing_mail', ''),
            $input->getVar('billing_phone', ''),
            $input->getVar('billing_ico', ''),
            $input->getVar('billing_dic', ''),
            $input->getVar('billing_icdph', ''),
            $input->getVar('different_shipping_address', false) ? $input->getVar('shipping_name', '')    : $input->getVar('billing_name', ''),
            $input->getVar('different_shipping_address', false) ? $input->getVar('shipping_city', '')    : $input->getVar('billing_city', ''),
            $input->getVar('different_shipping_address', false) ? $input->getVar('shipping_address', '') : $input->getVar('billing_address', ''),
            $input->getVar('different_shipping_address', false) ? $input->getVar('shipping_address_number', '') : $input->getVar('billing_address_number', ''),
            $input->getVar('different_shipping_address', false) ? $input->getVar('shipping_zip', '')     : $input->getVar('billing_zip', ''),
            $input->getVar('different_shipping_address', false) ? $input->getVar('shipping_phone', '')     : $input->getVar('billing_phone', ''),
            $input->getVar('comment', ''),
            $input->getVar('transport_method', ''),
            $input->getVar('payment_method', ''),
            $input->getVar('price_shipping_and_packing', 0),
            $calendar_ids,
            $calendar_prices,
            $input->getVar('depo_pickup_place_id', '')
        );

        (new CalendarControllerAdmin())->createInvoicePdf($order_id, 'order', $order_id, date('d.m.Y'));

        Mail::sendOrderCreatedConfirmation($order_id);

        Mail::sendNewOrderConfirmation($order_id);

        if ($input->getVar('payment_method', '') === 'card') {

            $order = $this->modelAdmin->getOrderDetail($order_id);
            $finalPrice = Price::applyDiscount($order['order']['price_calendars'], $order['order']['discount']) + $order['order']['price_shipping_and_packing'];

            $paymentService = new PaymentService(
                CalendarConstants::$GP_WEBPAY_MERCHANT_NUMBER,
                CalendarConstants::$GP_WEBPAY_PRIVATE_KEY_FILE,
                CalendarConstants::$GP_WEBPAY_PRIVATE_KEY_PASS,
                CalendarConstants::$GP_WEBPAY_PUBLIC_KEY_FILE,
                'https://vlastnykalendar.sk/index.php?option=com_calendar&view=calendar&layout=order_success&order_id=' . $order_id,
                CalendarConstants::$GP_WEBPAY_TESTING
            );

            $order_number = $paymentService->generateRandomOrderNumber();
            $this->model->gpWebpayCreate($order_id, $order_number);

            $addInfo = $paymentService->createAddInfo($order['order']['billing_name'], $order['order']['billing_mail'], $order['order']['billing_phone'],
                $order['order']['billing_name'], $order['order']['billing_address'] . ' ' . $order['order']['billing_address_number'], $order['order']['billing_city'], $order['order']['billing_zip'], 703,
                $order['order']['shipping_name'], $order['order']['shipping_address'] . ' ' . $order['order']['shipping_address_number'], $order['order']['shipping_city'], $order['order']['shipping_zip'], 703
            );

            $paymentLink = $paymentService->createPaymentLink2(
                bcmul($finalPrice, 100), 
                $order_id, 
                $order_number, 
                $input->getVar('billing_mail', ''),
                $addInfo
            );

            $this->app->redirect($paymentLink);
        }

        $this->app->redirect('index.php?option=com_calendar&view=calendar&layout=order_success&order_id=' . $order_id);
    }

    public function downloadOrderPdf()
    {
        $input = JFactory::getApplication()->input;

        $order_id = $input->get('order_id', 0);

        if ($order_id > 0) {

            $user_id = JFactory::getUser()->get('id');

            $can_download = $this->modelAdmin->isUsersOrder($user_id, $order_id);

            $file_path = CalendarControllerAdmin::getInvoiceDownloadUrl($order_id, 'order');

            if ( ! file_exists($file_path)) {
                $adminController = new CalendarControllerAdmin();
                $adminController->createInvoicePdf($order_id, 'order', 21, date('d.m.Y'));
            }

            if ($user_id != 0 && $can_download) {
                $file = 'https://www.vlastnykalendar.sk/' . $file_path;
                header('Content-Description: File Transfer');
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename=' . basename($file));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                ob_clean();
                flush();
                readfile($file);
            }

            if ($user_id == 0) {
                $this->app->redirect('index.php/vytvorit-kalendar/login-form');
            } else {
                jexit('Error! Not permitted to download file!');
            }
        }
    }

    public function isDepoPlaceSelected() {
        $input = JFactory::getApplication()->input;
        $depoPlaceId = $input->get('depo_number', '');

        if ($depoPlaceId != '') {
            $curl = curl_init('https://admin.depo.sk/v2/api/places/selected');
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array('order' => $depoPlaceId)));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Basic aW5mb0B2bGFzdG5hLWZvdG9rbmloYS5zazpmb3Rva25paGEwMQ=='
            ));

            $result = curl_exec($curl);
            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $json = json_decode($result, true);
            
            if ($status == 200 && $json != null) {
                $isSelected = $json['is_selected'] != null && $json['is_selected'] === 1;
                die(json_encode(array(
                    'success' => $isSelected,
                    'message' => $isSelected ? 'OK' : 'FAILURE',
                    'name'    => $json['name'],
                    'street'  => $json['street'],
                    'zip'     => $json['zip'],
                    'city'    => $json['city']
                ), JSON_UNESCAPED_UNICODE));
            }
        }

        die(json_encode(array(
            'success' => false,
            'message' => 'FAILURE',
            'name'    => '',
            'street'  => '',
            'zip'     => '',
            'city'    => ''
        )));
    }

	function debug($data) 
	{
		echo "<pre>";
		var_dump($data);
		echo "</pre>";
	}
}