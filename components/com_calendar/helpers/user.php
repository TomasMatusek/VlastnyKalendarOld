<?php
/**
 * @package			Photobook component
 * @subpackage	PDF creator Helper
 */

defined('_JEXEC') or die('Restricted access');

class UserHelper 
{
	public static function group($user_id = 0)
	{
		if ($user_id == 0)
		{
			$user_group = 'guest';
		}
		else if (in_array($user_id, unserialize(CAL_ADMIN_USERS)))
		{
			$user_group = 'admin';
		}
		else
		{
			$user_group = 'customer';
		}
		
		return $user_group;
	}
}