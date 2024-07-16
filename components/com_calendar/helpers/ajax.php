<?php


//Load Joomla environment
if (! defined('_JEXEC')) define('_JEXEC', 1);
$DS=DIRECTORY_SEPARATOR;
define('DS', $DS);

//Get component path
preg_match("/\\{$DS}components\\{$DS}com_.*?\\{$DS}/", __FILE__, $matches, PREG_OFFSET_CAPTURE);
$component_path = substr(__FILE__, 0, strlen($matches[0][0]) + $matches[0][1]);
define('JPATH_COMPONENT', $component_path);

define('JPATH_BASE', substr(__FILE__, 0, strpos(__FILE__, DS.'components'.DS) ));
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once JPATH_BASE .DS.'includes'.DS.'framework.php';
jimport( 'joomla.environment.request' );
$mainframe =& JFactory::getApplication('site');
$mainframe->initialise();

// Token check
JSession::checkToken('request') or exit( JText::_( 'Not allowed' ) );

$response = array(
  'valid' => false,
  'message' => 'Zľavový kód nie je správne vyplnený.<br />Buď pole ponechajte prázdne, alebo zadajte správny zľavový kód!',
	'sale' => 0
);

$jinput = JFactory::getApplication()->input;
$post->code = strtolower($jinput->get('special_code','', 'RAW'));
$post->price = strtolower($jinput->get('price','', 'RAW'));
$post->cover = strtolower($jinput->get('cover','', 'RAW'));
$post->quantity = $jinput->get('quantity','', 'RAW');

include_once(JPATH_COMPONENT . '/constants.php');
$smallCoupons = unserialize(SMALL_COUPONS);

if ( array_key_exists($post->code, $smallCoupons) ) 
{
	// Overenie exspiracie kuponu
	$couponExpire = $smallCoupons[$post->code][0]['expire'];
					
	if( strtotime($couponExpire.' 23:59:59') > time() )
	{
		if(is_numeric($post->quantity)) 
		{
				// Kupon je platny
				$response = array(
					'valid' => true,
					'message' => "Kupón je platný, zľava bola odpočítaná. Celkovú sumu aj so zľavou môžte nájsť nižšie",
					'sale' => (($post->quantity*($post->price+$post->cover))*($smallCoupons[$post->code][0]['percentSale']/100))
				);
		}
		else
		{
			// Kupon je platny ale mnozstvo je zle
			$response = array(
				'valid' => true,
				'message' => "Kupón je platný, avsak zadané množstvo nie je v správnom formáte.",
				'sale' => 0
			);

		}
		
	} else {
			$response = array(
				'valid' => false,
				'message' => 'Platnosť zľavového kupónu vypršala. Vložte prosím správny kód kupónu, alebo pole ponechajte prázdne!',
				'sale' => 0
			);
	}
}
else {
	$response = array(
		'valid' => false,
		'message' => 'Zľavový kód nie je správny. Buď pole ponechajte prázdne, alebo zadajte správny zľavový kód!',
		'sale' => 0
	);
}

echo json_encode($response);