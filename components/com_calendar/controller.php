<?php

/**
 * @package			Calendar Component
 * @subpackage	Calendar Controller
 */

defined('_JEXEC') or die;

include_once(JPATH_COMPONENT . '/constants.php');
include_once(JPATH_COMPONENT . '/helpers/zip.php');
include_once(JPATH_COMPONENT . '/helpers/dir.php');
include_once(JPATH_COMPONENT . '/helpers/user.php');
include_once(JPATH_COMPONENT . '/helpers/file.php');
include_once(JPATH_COMPONENT . '/helpers/route.php');
include_once(JPATH_COMPONENT . '/helpers/array.php');
include_once(JPATH_COMPONENT . '/helpers/pdf.php');
include_once(JPATH_COMPONENT . '/helpers/mail.php');
include_once(JPATH_COMPONENT . '/helpers/sms.php');
include_once(JPATH_COMPONENT . '/helpers/price.php');
include_once(JPATH_COMPONENT . '/helpers/status.php');
include_once(JPATH_COMPONENT . '/helpers/calendar.php');
include_once(JPATH_COMPONENT . '/helpers/pdfcreator.php');
include_once(JPATH_COMPONENT . '/helpers/permissions.php');
include_once(JPATH_COMPONENT . '/helpers/format_count.php');
include_once(JPATH_COMPONENT . '/helpers/ikros.php');
require_once(JPATH_COMPONENT . '/services/payment.service.php');
require_once(JPATH_COMPONENT . '/services/wsPayment.service.php');

jimport('joomla.application.component.controller');

class CalendarController extends JControllerLegacy
{
    protected $model;
    protected $modelAdmin;

	public function __construct()
	{
		parent::__construct();
        
		// variables accessible for all controllers
		$this->app =& JFactory::getApplication();
		$this->session =& JFactory::getSession();
		$this->user_id =& JFactory::getUser()->get('id');
		$this->document =& JFactory::getDocument();
        $this->input =& $this->app->input;

		$this->user_group = UserHelper::group($this->user_id);
        $this->model = $this->getModel('calendar');
        $this->modelAdmin = $this->getModel('admin');

		$this->months = array('cover','january','february','march','april','may','june','july','august','september','october','november','december');			
		$this->years = array();
	}

	public function display($cachable = false, $urlparams = false)
	{
		// set view
		$viewName	= JRequest::getCmd('view', 'calendar');	
		$viewFormat = JFactory::getDocument()->getType();
		$view = $this->getView($viewName, $viewFormat);

		// set layout
		$layoutName	= JRequest::getCmd('layout');
		$layoutName = RouteHelper::getLayout($layoutName, $viewName);
		$view->setLayout($layoutName);

		// push model to view
		$view->setModel($this->model, true);

		// push data to view
		$view->assignRef('app', $this->app);
		$view->assignRef('model', $this->model);
		$view->assignRef('layout', $layoutName);
		$view->assignRef('months', $this->months);
		$view->assignRef('session', $this->session);
		$view->assignRef('user_id', $this->user_id);
		$view->assignRef('document', $this->document);
		$view->assignRef('user_group', $this->user_group);

		$view->display();
	}

	protected function debug($data)
    {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    }
}
