<?php

/**
 * @package			Calendar Component
 * @subpackage	User Controller
 */

defined('_JEXEC') or die;

require_once(JPATH_COMPONENT . '/controller.php');

class CalendarControllerPayment extends CalendarController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function result()
    {
        if ( ! Permissions::isUserAdmin($this->user_id)) {
            jexit('Only admins can run this action');
        }

        echo 'RESULT';
    }
}