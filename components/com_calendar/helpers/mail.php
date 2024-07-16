<?php
/**
 * @package			Photobook component
 * @subpackage	Mail Helper
 */

defined('_JEXEC') or die('Restricted access');

class Mail
{
    private static function getMailerInstance()
    {
        $mailer = JFactory::getMailer();

        $mailer->useSmtp('true', 'vps480.nameserver.sk', 'objednavky@vlastnykalendar.sk', '4ziM6yOt14Wy5dJN', 'ssl', 465);

        $mailer->setSender(array( 0 =>'objednavky@vlastnykalendar.sk', 1 => 'Vlastnykalendar.sk'));

        $mailer->isSMTP();

        $mailer->isHtml(true);

        return $mailer;
    }

    /**
     * Notification e-mail for customer that order was created.
     */
    public static function sendOrderCreatedConfirmation($order_id)
    {
        $mailer = Mail::getMailerInstance();

        $user = Mail::getUserDetail($order_id);

        $mailer->addRecipient($user->email, $user->name);

        $mailer->setSubject('Vlastnykalendar.sk | Vaša objednávka bola prijatá');

        $mailer->setBody(CAL_MAIL_MESSAGE);

        $file_path = '/home/html/vlastnykalendar.sk/' . CalendarControllerAdmin::getInvoiceDownloadUrl($order_id, 'order');

        $mailer->addAttachment($file_path, 'objednavka_' . $order_id . '.pdf');

        $mailer->Send();
    }

    /**
     * Notification e-mail for customer that order was finished and will be shipped to him.
     */
	public static function sendOrderDoneConfirmation($order_id)
	{
        $mailer = Mail::getMailerInstance();

        $user = Mail::getUserDetail($order_id);

        $mailer->addRecipient($user->email, $user->name);

        $mailer->setSubject('Vlastnykalendar.sk | Vaša objednávka je vyhotovená');

        $mailer->setBody(CAL_MAIL_MESSAGE_DONE);

        $file_path = '/home/html/vlastnykalendar.sk/' . CalendarControllerAdmin::getInvoiceDownloadUrl($order_id, 'invoice');

        $mailer->addAttachment($file_path, 'faktura_' . $order_id . '.pdf');

        $mailer->Send();
	}

    /**
     * Notification e-mail for administrator that new order was created.
     */
	public static function sendNewOrderConfirmation($order_id)
    {
        $mailer = Mail::getMailerInstance();

        $mailer->addRecipient('objednavky@vlastny-kalendar.sk', 'Vlastnykalendar.sk');

        $mailer->setSubject('Vlastnykalendar.sk | Nova objednavka ' . $order_id);

        $mailer->setBody("Nova objednavka cislo: " . $order_id . "bola prijata");

        $mailer->Send();
    }

    private function getUserDetail($order_id)
    {
        $model = JModelLegacy::getInstance('Admin', 'CalendarModel');

        $order_detail = $model->getOrderDetail($order_id);

        $user_id = $order_detail['order']['user_id'];

        $user = JFactory::getUser($user_id);

        return $user;
    }
}