<?php

/**
 * @package			Calendar Component
 * @subpackage	User View
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class CalendarViewUser extends JViewLegacy
{
	function display($tpl = null) 
	{
        $this->document->addStyleSheet( CAL_COMPONENT_WEB . 'assets/css/bootstrap.min.css');
        $this->document->addStyleSheet( CAL_COMPONENT_WEB . 'assets/css/viewer.css');

		if ($this->user_group == 'guest')
		{
			$this->app->redirect('index.php?option=com_users', false);
		}
		
		switch($this->layout)
		{
		    // zoznam objednavok
			case 'list':
			    $user_id = JFactory::getUser()->get( 'id' );
				$this->data['orders'] = $this->model->getOrdersList($user_id);
            break;

            // detail objednávky
			case 'detail':
                $input = JFactory::getApplication()->input;
                $user_id = JFactory::getUser()->get('id');
                $order_id = $input->get('order_id', 0);
                $this->data['detail'] = $this->model->getOrderDetail($order_id, $user_id);
			break;		
		}

		parent::display($tpl);
	}
	
	
	function debug($data) 
	{
		echo "<pre>";
		var_dump($data);
		echo "</pre>";
	}
	
}
