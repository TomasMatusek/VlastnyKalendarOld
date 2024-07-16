<?php

defined('_JEXEC') or die;

set_include_path( get_include_path() . PATH_SEPARATOR . $_SERVER['DOCUMENT_ROOT'] );

jimport('joomla.application.component.controller');

// Get an instance of the controller prefixed by Calendar
$controller = JControllerLegacy::getInstance('Calendar');

$input = JFactory::getApplication()->input;

// Perform the Request task
$controller->execute($input->getCmd('task'));

// Redirect if set by the controller
$controller->redirect();