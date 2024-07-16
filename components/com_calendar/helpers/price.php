<?php
/**
 * @package			Photobook component
 * @subpackage	Mail Helper
 */

defined('_JEXEC') or die('Restricted access');

class Price
{
	public static function get($calendar_type, $priceInfo)
	{
		$prices = unserialize(CAL_PRICES);

		if($priceInfo['onSale']) {
			return $priceInfo['newPrice'];	
		} else {
			return $prices[$calendar_type];	
		}
		
	}

	public static function dph($price)
	{
		$final_price = round($price - ($price * (20/(20 + 100))),2);
		
		return $final_price;
	}

	public static function applyDiscount($price, $discount_percentage)
    {
        if ($discount_percentage > 0) {
            return round($price - (($price / 100) * $discount_percentage), 2);
        }
        return $price;
    }
	
	public static function shipping($shipping_method)
	{
		
		switch ($shipping_method)
		{
			case 'courier':
				$price = CAL_SHIPPING_COURIER;
			break;
			
			case 'post':
				$price = CAL_SHIPPING_POST;
			break;
			
			case 'personal':
				$price = CAL_SHIPPING_PERSONAL;
			break;

            case 'depo':
                $price = CAL_SHIPPING_DEPO;
            break;
			
			default;
				$price = 0;
			break;
		}
		
		return $price;
	}
	
	public static function payment($payment_method)
	{
		switch ($payment_method)
		{						
			case 'cod-courier':
				$price = CAL_PAYMENT_COURIER;
			break;
			
			case 'cod':
				$price = CAL_PAYMENT_POST;
			break;
			
			case 'cash':
				$price = CAL_PAYMENT_CASH;
			break;
			
			case 'transfer':
				$price = CAL_PAYMENT_TRANSFER;
			break;
			
			case 'post-transfer':
				$price = CAL_PAYMENT_POST_TRANSFER;
			break;
			
			case 'post-transfer-cz':
				$price = CAL_PAYMENT_TRANSFER_CZ;
			break;
			
			case 'post-transfer-eu':
				$price = CAL_PAYMENT_TRANSFER_EU;
			break;

            case 'depo':
                $price = CAL_PAYMENT_DEPO;
            break;
						
			default:
				$price = 0;
			break;
		}
		
		return $price;
	}

    public static function getCalendarPrices()
    {
        $calendar_prices = unserialize(CAL_PRICES);

        $model = JModelLegacy::getInstance('Calendar', 'CalendarModel');

        $cal_sales = $model->getCalendarSales();

        $calendar_price_info = array();

        foreach ($calendar_prices as $cal_type => $normal_price) {

            // Check if calendar is on sale
            if( isset($cal_sales[$cal_type]) &&
                strtotime($cal_sales[$cal_type]['validFrom']) != 0 &&
                strtotime($cal_sales[$cal_type]['validFrom']) <= time() &&
                strtotime($cal_sales[$cal_type]['validTo']) != 0 &&
                strtotime($cal_sales[$cal_type]['validTo']) >= time()) {

                $current_cal_type = $cal_sales[$cal_type];

                $percent_sale = $current_cal_type['percentSale'];

                $calendar_price_info[$cal_type] = array(
                    'onSale' => true,
                    'originalPrice' => number_format(floatval($normal_price), 2, '.', ''),
                    'percentSale' => $percent_sale,
                    'newPrice' => number_format(floatval(round($normal_price * (1-($current_cal_type['percentSale']/100)),1)), 2, '.', ''),
                );

            } else {
                $calendar_price_info[$cal_type] = array(
                    'onSale' => false,
                    'originalPrice' => number_format(floatval($normal_price), 2, '.', ''),
                    'percentSale' => 0,
                    'newPrice' => number_format(floatval($normal_price), 2, '.', ''),
                );
            }
        }

        return $calendar_price_info;
    }
}