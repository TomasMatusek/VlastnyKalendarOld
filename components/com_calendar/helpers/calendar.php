<?php
/**
 * @package			Photobook component
 * @subpackage	PDF creator Helper
 */

defined('_JEXEC') or die('Restricted access');

class CalendarHelper 
{
	public static function getLayout($layout_type)
	{
		$path = CAL_COMPONENT_SERVER . 'layouts/' . $layout_type . '.type.php';
		
		return $path;
	}
	
	public static function arePicturesSet($pictures)
	{
		if ( ! isset($pictures))
		{
			return false;
		}
		
		for ($i=1; $i<count($pictures)+1; $i++)
		{
			if (strlen($pictures[$i]['img']) == 0 || $pictures[$i]['img'] == 'auto')
			{
				return false;
			}
		}
		return true;
	}
	
	public static function getPicturePositions($layout_type, $cover = false)
	{
		if($cover)
		{
			$positions = unserialize(CAL_COVER_LAYOUT_POSITIONS);
		}
		else
		{
			$positions = unserialize(CAL_LAYOUT_POSITIONS);
		}
		
		return $positions[$layout_type];
	}
	
	public static function getJquery($pictures)
	{
		$js = "<script type='text/javascript'>";
		
		if (empty($pictures))
		{
			return "<script type='text/javascript'></script>";	
		}
		
		for ($i=1; $i<count($pictures)+1; $i++)
		{
			$js .=
			"jQuery(document).ready(function() {
				var iv1 = jQuery('#move" . $i . "').iviewer({
					src:  '" . $pictures[$i]['img'] . "', 
					update_on_resize: false,
					zoom_animation: true,
					mousewheel: true,
					left_element: '#cal_left" . $i . "',
					top_element: '#cal_top" . $i . "',
					width_element: '#cal_width" . $i . "',
					height_element: '#cal_height" . $i . "',
					rotate_element: '#cal_rotate" . $i . "',
					onMouseMove: function(ev, coords) { },
					onStartDrag: function(ev, coords) { },
					onDrag: function(ev, coords) { },
						top: '" . $pictures[$i]['top'] . "',
						left: '" . $pictures[$i]['left'] . "',
						img_width: '" . $pictures[$i]['width'] . "',
						img_height: '" . $pictures[$i]['height'] . "',
						rotate: '" . $pictures[$i]['rotate'] . "'
				});
			});";
		}
		
		$js .= "</script>";
		
		return $js;
	}
	
	public static function clearPictures(array $settings)
	{
		unset($settings['pictures']);
		
		return $settings;
	}
	
	public static function getYears(array $months, $start_year)
	{
		$december_index = array_search('december', $months);

		for($i=0;$i<count($months);$i++)
		{
			if($i > $december_index)
			{
				$year[$i] = $start_year + 1;
			}
			else
			{
				$year[$i] = $start_year;
			}
		}

		return $year;
	}
	
	public static function makeBackupOfPictures($pictures, $user_id)
	{		
		for($i = 1; $i < count($pictures) + 1; $i++)
		{
			$img_parts = explode("/", $pictures[$i]['img']);
			
			$file_name = end($img_parts);
			
			$img = CAL_ROOT_SERVER . 'calendar/' . $user_id . '/img/' . $file_name;
			
			$img_backup = CAL_ROOT_SERVER . 'calendar/' . $user_id . '/img_backup/' . $file_name;
			
			if(!file_exists($img_backup))
			{
				copy($img, $img_backup);
				
				chmod($img_backup, 0777);
			}
		}
	}
	
	public static function removeBackupImages($pictures, $user_id)
	{
		for($i = 1; $i < count($pictures) + 1; $i++)
		{
			$img_parts = explode("/", $pictures[$i]['img']);
			
			$file_name = end($img_parts);
			
			$img_backup = CAL_ROOT_SERVER . 'calendar/' . $user_id . '/img_backup/' . $file_name;

			if(file_exists($img_backup))
			{
				unlink($img_backup);
			}
		}
	}

	public static function getCalendars($user_id, $model, $onlyFinished)
    {
        if ($onlyFinished) {
            $calendars = $model->getReadyForOrderCalendars($user_id);
        } else {
            $calendars = $model->getNonFihisnedcalendars($user_id);
        }

        $settings = unserialize(CAL_LAYOUT_MOD_POSITIONS_COUNT);

        $cover_prices = unserialize(CAL_COVER_PRICES);

        $finished_calendar = array();

        foreach ($calendars as $key => $calendar)
        {
            $type = $calendar['type'];

            $front_page = $calendar['front_page'] == "1";

            if(array_key_exists($type, $settings))
            {
                $calendar_position_without_cover = $settings[$type]['totalPositionsWithoutCover'];
                $calendar_position_with_cover = ($settings[$type]['totalPositionsWithoutCover'] + $settings[$type]['coverPositions']);
                $calendar_positions = $front_page ? $calendar_position_with_cover : $calendar_position_without_cover;
            }
            else
            {
                $cover_positions = unserialize(CAL_COVER_LAYOUT_POSITIONS);
                $settings = unserialize(CAL_LAYOUT_POSITIONS);
                $calendar_position_without_cover = $settings[$type] * 12;
                $calendar_position_with_cover = ($settings[$type] * 12) + $cover_positions[$type];
                $calendar_positions = $front_page ? $calendar_position_with_cover : $calendar_position_without_cover;
            }

            $calendar_data = $model->getCalendarData($calendar['cal_id']);

            // var_dump(count($calendar_data['photos']));
            // var_dump($calendar_positions);

            $calendar['finished'] = count($calendar_data['photos']) >= $calendar_positions;



            if ($onlyFinished && ! $calendar['finished']) {
                continue;
            }

            $prices = Price::getCalendarPrices();

            $calendar['discount_percentage'] = $prices[$type]['onSale'] ? $prices[$type]['percentSale'] : 0;

            $calendar['price'] = $prices[$type]['onSale'] ? $prices[$type]['newPrice'] : $prices[$type]['originalPrice'];

            $calendar['price_cover'] = $front_page ? $cover_prices[$type] : 0;

            $calendar['price_with_cover'] = $calendar['price_cover'] + $calendar['price'];

            $calendar['price_total'] = $calendar['price_with_cover'] * $calendar['quantity'];

            array_push($finished_calendar, $calendar);
        }

        return $finished_calendar;
    }

    public static function getCalendarStatusTranslate($status) {
	    switch ($status) {
            case 0:
                return 'Nedokončený';
            case 1:
                return 'Dokončený';
            case 2:
                return 'Objedenávka odoslaná';
            case 3:
                return 'Objednávka stornovaná';

        }
    }

    public static function getOrderStatusTranslate($status) {
        switch ($status) {
            case 'sent':
                return 'Objednávka prijatá';
            case 'in_progress':
                return 'Spracováva';
            case 'cancel':
                return 'Zrušená';
            case 'done':
                return 'Dokončená a odoslaná';

        }
    }

    public static function transportMethodTranslate($transport_method) {
        switch ($transport_method) {
            case 'personal':
                return 'Osobný odber';
            case 'courier':
                return 'Kuriér';
            case 'post-sk':
                return 'Slovenskou poštou';
            case 'post-cz':
                return 'Česká Republika - poštou';
            case 'post-eu':
                return 'Európska únia - poštou';
            case 'depo':
                return 'DEPO - odberné miesto';
        }
    }

    public static function paymentMethodTranslate($payment_method) {
        switch ($payment_method) {
            case 'cash':
                return 'Platba v hotovosti';
            case 'courier':
            case 'post-sk':
            case 'on-delivery':
                return 'Dobierka';
            case 'transfer-sk':
            case 'transfer-cz':
            case 'transfer-eu':
                return 'Prevodom na účet';
            case 'gp_webpay':
            case 'gp-webpay':
            case 'card':
                return 'Platba kartou';
            case 'depo':
                return 'DEPO - platba pri prevzatí';
        }
    }

    public static function orderStatusTranslate($status) {
        switch ($status) {
            case 'sent':
                return 'Nová';
            case 'canceled':
                return 'Zrušená';
            case 'in_progress':
                return 'Spracováva sa';
            case 'done':
                return 'Vybavená';
        }
    }

    public static function orderStatusLabelClassName($status) {
        switch ($status) {
            case 'sent':
                return 'label calendar-label label-danger';
            case 'canceled':
                return 'label calendar-label label-default';
            case 'in_progress':
                return 'label calendar-label label-info';
            case 'done':
                return 'label calendar-label label-success';
        }
    }

    public static function showDash($value) {
	    return strlen($value) > 0 ? $value : '-';
    }

    public static function getImagePath($year) {
        $current_year = date('Y');
        $previous_year = date('Y', strtotime('-1 year'));
        $next_year = date('Y', strtotime('+1 year'));

        if ($year == $previous_year) {
            return 'previous';
        } else if ($year == $current_year) {
            return 'current';
        } else if ($year == $next_year) {
            return 'next';
        }

        return '';
    }
}