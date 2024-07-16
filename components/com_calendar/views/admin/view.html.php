<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

include_once(JPATH_COMPONENT . '/controllers/admin.php');
include_once(JPATH_COMPONENT . '/models/admin.php');
include_once(JPATH_COMPONENT . '/models/calendar.php');

class CalendarViewAdmin extends JViewLegacy
{
    protected $db;
    protected $dbCalendar;
    protected $app;
    protected $input;
    protected $user_id;
    protected $user_group;

    public function __construct()
    {
        $this->db = JModelLegacy::getInstance('Admin', 'CalendarModel');
        $this->dbCalendar = JModelLegacy::getInstance('Calendar', 'CalendarModel');
        $this->app = JFactory::getApplication();
        $this->input = JFactory::getApplication()->input;
        $this->user_id = JFactory::getUser()->get('id');
        $this->user_group = UserHelper::group($this->user_id);
        parent::__construct();
    }

    function display($tpl = null)
	{
        $this->document->addStyleSheet( CAL_COMPONENT_WEB . 'assets/css/bootstrap.min.css');
        $this->document->addStyleSheet( CAL_COMPONENT_WEB . 'assets/css/jquery-ui.min.css');
        $this->document->addStyleSheet( CAL_COMPONENT_WEB . 'assets/css/viewer.css');
        $this->document->addScript( CAL_COMPONENT_WEB . 'assets/js/jquery.js' );
        $this->document->addScript( CAL_COMPONENT_WEB . 'assets/js/jquery-ui.min.js' );
        $this->document->addScript( CAL_COMPONENT_WEB . 'assets/js/jquery.scripts.js' );

		if ($this->user_group != 'admin')
		{
			$this->app->enqueueMessage("Nie ste oprávnený pristupovať do tejto časti.","error");
			$this->app->redirect('index.php?option=com_users&view=login');
			return;
		}

		switch($this->getLayout())
        {
            case 'orders':
                $this->processOrdersLayoutData();
            break;

            case 'order':
                $this->processOrderLayoutData();
            break;

            case 'coupons':
                $this->processCouponsLayoutData();
            break;

            case 'sales':
                $this->processSalesLayoutData();
            break;

            case 'cleanup':
                $this->processCleanupLayoutData();
            break;
        }

        parent::display($tpl);
	}

	function processSalesLayoutData()
    {
        if ( ! Permissions::isUserAdmin($this->user_id)) {
            jexit('Only admins can run this action');
        }

        $this->data['sales'] = $this->dbCalendar->getCalendarSales();
    }

    function processOrderLayoutData()
    {
        if ( ! Permissions::isUserAdmin($this->user_id)) {
            jexit('Only admins can run this action');
        }

        $order_id = $this->input->get('order_id', '0');
        $debug = $this->input->get('debug', false);

        $this->data['order'] = $this->db->getOrderDetail($order_id);
        $this->data['invoice_number'] = $this->db->getLatestInvoiceNumber();
        $this->data['invoice_file_download'] = $this->getInvoiceFileDownloadUrl($order_id, $this->data['order']['order']['billing_name']);
        $this->data['user'] = JFactory::getUser($this->data['order']['order']['user_id']);
        $this->data['user_profile'] = JUserHelper::getProfile($this->data['order']['order']['user_id']);
        $this->data['wp_payment'] = $this->dbCalendar->getGpWebpayList($order_id);

        if ($debug) {
            echo '<pre>';
            var_dump($this->data['order']);
            var_dump($this->data['wp_payment']);
            echo '</pre>';
        }

        // Check database
        $this->data['wp_payment']['id_paid'] = false;
        for ($i = 0; $i < count($this->data['wp_payment']); $i++) {
            if ($this->data['wp_payment'][$i]['status'] === 'paid') {
                $this->data['wp_payment']['id_paid'] = true;
            }
        }

        // Check webpay
        if ( ! $this->data['wp_payment']['id_paid']) {
            $webPay = new WebpayWsService();
            for ($i = 0; $i < count($this->data['wp_payment']); $i++) {
                $webPayOrderNumber = $this->data['wp_payment'][$i]['order_number'];
                try {
                    $response = $webPay->getPaymentStatus($webPayOrderNumber);
                    if ($response && ($response->status == 'APPROVED' || $response->status == 'CAPTURED')) {
                        $this->data['wp_payment']['id_paid'] = true;
                        break;
                    }
                    //var_dump($response);
                } catch (Exception $e) {
                    //echo $e;
                }
            }   
        }

        for ($i = 0; $i < count($this->data['order']['calendars']); $i++) {
            $cal_id = $this->data['order']['calendars'][$i]['cal_id'];
            $this->data['images'][$cal_id] = $this->dbCalendar->getCalendarData($cal_id);
        }

        $calendar_ids = array();
        foreach ($this->data['order']['calendars'] as $calendars) {
            array_push($calendar_ids, $calendars['cal_id']);
        }

        $this->data['zip'] = Zip::getOrderZipFilesPaths($order_id, $calendar_ids);

        // var_dump($this->data['wp_payment']);
        // if (count($this->data['wp_payment']) > 0) {
        //     $payment = new WebpayWsService();
        //     $response = $payment->getPaymentDetail(202108185635876);
        // }

    }

	function processOrdersLayoutData()
    {
        if ( ! Permissions::isUserAdmin($this->user_id)) {
            jexit('Only admins can run this action');
        }

        $this->data['status'] = $this->input->get('status', '');
        $this->data['search'] = $this->input->get('search', '');
        $this->data['page_offset'] = $this->input->get('page_offset', '0');

        $order_count = $this->db->getOrdersCount();
        $this->data['pages'] = $order_count == 0 ? 1 :ceil($order_count / 40);
        
        $this->data['orders'] = $this->db->getOrders(
            $this->data['status'], $this->data['search'], $this->data['page_offset']
        );
        
        foreach ($this->data['orders'] as $key => $order) {
            $this->data['orders'][$key]['order_details'] = $this->db->getOrderDetail($order['order_id']);
        }

//        echo '<pre>';
//        var_dump($this->data['orders']);
//        echo '</pre>';
    }

    function processCouponsLayoutData()
    {
        $this->data['coupons'] = $this->db->getDiscountCoupons();
    }

    function processCleanupLayoutData()
    {
        $admin_controller = new CalendarControllerAdmin();
        $admin_controller->getClearDiskData();
    }

    private function getInvoiceFileDownloadUrl($order_id, $billing_name)
    {
        $file_path = CalendarControllerAdmin::getOrderDirName($order_id, $billing_name) . 'invoice_' . str_pad($order_id, 4, '0', STR_PAD_LEFT) . '.pdf';

        $file_root_path = CalendarControllerAdmin::getOrdersRootDir() . $file_path;

        if (file_exists($file_root_path)) {
            return '/generatedPDF' . $file_path;
        }

        return '';
    }
}