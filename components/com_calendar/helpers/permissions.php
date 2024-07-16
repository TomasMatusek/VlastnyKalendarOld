<?php

defined('_JEXEC') or die('Restricted access');

class Permissions
{
    public static function isUserAdmin($user_id)
    {
        $permissions = unserialize(CAL_ADMIN_USERS);

        return in_array($user_id, $permissions);
    }
}