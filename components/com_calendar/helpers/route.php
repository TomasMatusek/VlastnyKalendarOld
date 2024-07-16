<?php
/**
 * @package			Calendar component
 * @subpackage	PDF creator Helper
 */

defined('_JEXEC') or die('Restricted access');

class RouteHelper 
{
	public static function getLayout($layoutName, $viewName)
	{
		if (empty($layoutName))
		{
			$defaultLayout = '';
			
			switch($viewName)
			{
				case 'admin':
					$defaultLayout = 'orders';
					break;
				case 'calendar':
					$defaultLayout = 'create';
					break;
				case 'invoice':
					$defaultLayout = 'create';
					break;
				case 'user':
					$defaultLayout = 'list';
					break;		
			}
			
			return $defaultLayout;
		}
		
		return $layoutName;
	}
	
}