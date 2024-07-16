<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

require_once(JPATH_COMPONENT . '/controllers/admin.php');

class CalendarViewInvoice extends JViewLegacy
{
    protected $app;
    protected $user_id;
    protected $input;
    protected $db;

    function __construct()
    {
        parent::__construct();
        $this->app = JFactory::getApplication();
        $this->user_id = JFactory::getUser()->get('id');
        $this->input = $this->app->input;
        $this->db = JModelLegacy::getInstance('Admin', 'CalendarModel');

        $hash = $this->input->get('hash', '');

        if ($hash != 'EDlijt8QoECJYuhAtNFqI9a4b3zC2s5epIHQbTKDGwo4bcsjsODA6JKXhqtJvPza9NoGt0TKa2GiXP7Jzclx1VszUjsqkxUexZauU4yZfXYo28HUJLHq9cCpA8MqsUgZUH83eWcgwxMkBzoDzGI9Bz7O7rL2g9CiVfjGY1CFCnJTOMCt4JFQiUfvLE6hN2p7kqjGv1z7h3SuwRYI7D8sE4BXVUyboAz7UealaP92F3EMn3olQuMcRa0cgU2yQsCU') {
            jexit('Wrong hash');
        }
    }

    function display($tpl = null)
	{
        $order_id = $this->input->get('order_id', 0);
        $invoice_type = $this->input->get('invoice_type', 'order');
        $invoice_date = $this->input->get('invoice_date', '01.01.' . date('Y'));
        $invoice_number = $this->input->get('invoice_number', 1);
        $invoice_number = str_pad($invoice_number, 3, '0', STR_PAD_LEFT);

        if ($order_id == 0 || ! in_array($invoice_type, array('invoice','order'))) {
            jexit('Invalid order id, or invoice type');
        }

        $this->data['order'] = $this->db->getOrderDetail($order_id);
        $this->data['invoice_number'] = $invoice_number;
        $this->data['invoice_file_download'] = CalendarControllerAdmin::getInvoiceDownloadUrl($order_id, $invoice_type);
        $this->data['invoice_date'] = date('d.m.Y', strtotime($invoice_date));
        $this->data['invoice_date_plus_week'] = date('d.m.Y', strtotime($invoice_date . ' + 1 week'));
        $this->data['invoice_type'] = $invoice_type;
        $this->data['invoice_type_translate'] = $invoice_type == 'invoice' ? 'Faktúra' : 'Objednávka';

        parent::display($tpl);
	}
}
