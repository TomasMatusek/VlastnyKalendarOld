<?php
/**
 * @package			Photobook component
 * @subpackage	Zip Helper
 */

defined('_JEXEC') or die('Restricted access');

class Pdfcreator
{
	public static function getRationSize($calWidth, $picWidth, $pdfWidth)
	{
		$ration = ($picWidth / $calWidth);
		
		$picWidth = $pdfWidth * $ration;
		
		return round($picWidth, 2);
	}
	
	public static function getRationPosition($calWidth, $picWidth, $pdfWidth)
	{
		$ration = ($picWidth / $calWidth);
		
		$picWidth = $pdfWidth * $ration;
		
		return round($picWidth, 2);
	}
}