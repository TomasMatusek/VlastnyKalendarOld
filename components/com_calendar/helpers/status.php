<?php
/**
 * @package			Photobook component
 * @subpackage	Status Helper
 */

defined('_JEXEC') or die('Restricted access');

class Status
{
	public static function email($status)
	{
		if ($status == 1)
		{
			return 'Objednávka odoslaná';
		}
		else if ($status == 0)
		{
			return 'Objednávka čaká na odoslanie';
		}
	}
	
	public static function order($status)
	{
		if ($status == 0)
		{
			return 'Nová(0)';
		}
		else if ($status == 1)
		{
			return 'Spracováva sa(1)';
		}
		else if ($status == 2)
		{
			return 'Vybavená(2)';
		}
	}
	
	public static function css_order($status)
	{
		if ($status == 0)
		{
			return 'red';
		}
		else if ($status == 1)
		{
			return 'yellow';
		}
		else if ($status == 2)
		{
			return 'green';
		}
	}
	
	public static function shipping($status)
	{
		if ($status == 'post')
		{
			return 'Poštou';
		}
		else if ($status == 'personal')
		{
			return 'Osobne';
		}
		else if ($status == 'courier')
		{
			return 'Kuriérom';
		}
		else if($status == 'post-cz') {
			return 'Česká Republika - poštou';
		}
		else if($status == 'post-eu') {
			return 'Európska únia - poštou';
		}
        else if($status == 'depo') {
            return 'DEPO - výdajné miesto';
        }
	}
	
	public static function payment($status)
	{
		if ($status == 'cash')
		{
			return 'Hotovosť';
		}
		else if ($status == 'transfer' or $status == 'post-transfer' or $status == 'post-transfer-cz' or $status == 'post-transfer-eu')
		{
			return 'Prevodom';
		}
		else if ($status == 'cod-courier')
		{
			return 'Kuriérovi';
		}
		else if ($status == 'cod')
		{
			return 'Dobierka';
		}
        else if($status == 'depo')
        {
            return 'DEPO - platba pri prevzatí';
        }
	}
	
	public static function fill($data)
	{
		if (strlen($data) > 0)
		{
			return '-';
		}
		else
		{
			return $data;
		}
	}
	
	public static function coupon($coupon)
	{
		if (strlen($coupon) == 0)
		{
			return 'nie';
		}
		else
		{
			return $coupon;
		}
	}
	
	public static function type($type)
	{
			return "Typ - ".strtoupper($type);
	}
	
	public static function month($month)
	{
		switch($month)
		{
			case 'cover':
				return 'Titulná strana';
				
			case 'january':
				return 'január';
			
			case 'february':
				return 'február';
				
			case 'march':
				return 'marec';
			
			case 'april':
				return 'apríl';
			
			case 'may':
				return 'máj';
				
			case 'june':
				return 'jún';
			
			case 'july':
				return 'júl';
			
			case 'august':
				return 'august';
			
			case 'september':
				return 'september';
			
			case 'october':
				return 'október';
				
			case 'november':
				return 'november';
				
			case 'december':
				return 'december';
		}
	}
	
	
}