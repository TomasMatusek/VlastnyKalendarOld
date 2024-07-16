<?php
/**
 * @package			Calendar component
 * @subpackage	Array Helper
 */

defined('_JEXEC') or die('Restricted access');

class ArrayHelper 
{
	public static function rotate($value, $array)
	{
		// remove cover from array
		
		if(count($array) == 13)
		{
			array_shift($array);
			
			$cover = true;
		}
		
		// rotate months
		
		$start_key = array_search($value, $array);
		
		$flip = array_flip($array);
		
		foreach($array as $key => $valueue)
		{
			if($start_key != $key)
			{
				$elm = array_shift($flip);
				
				$flip[$valueue] = $elm;
			}
			else
			{
				break;
			}
		}
		
		$array = array_flip($flip);
		
		$array = array_values($array);
		
		// add cover to begining
		
		if(isset($cover) && $cover == true)
		{
			array_unshift($array, 'cover');
		}
		
		return $array;
	}
	
	public static function getEmptyArray($positions, $pictures)
	{
		for ($i=1; $i<$positions+1; $i++)
		{
			if (empty($pictures[$i]) || preg_match("/Notice/", $pictures[$i]['img']) === 1)
			{
				$pictures[$i]['img'] = 'auto';
				$pictures[$i]['top'] = 'auto';
				$pictures[$i]['left'] = 'auto';
				$pictures[$i]['width'] = 'auto';
				$pictures[$i]['height'] = 'auto';
				$pictures[$i]['rotate'] = 'auto';
			}
		}
		
		return $pictures;
	}
	
}