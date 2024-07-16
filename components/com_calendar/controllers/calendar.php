<?php

defined('_JEXEC') or die;

require_once(JPATH_COMPONENT . '/controller.php');

class CalendarControllerCalendar extends CalendarController
{
	public function __construct()
	{
		parent::__construct();
	}
	
	/*
	 * Create new calendar project (create database record; set $settings <sessions>; create upload dir)
	 *
	 * @call : this function is called when user submit form; calendar <view> create <layout>
	 * @return <void>
	 */
	public function create()
	{
        $form = JFactory::getApplication()->input->post->getArray();

        $form['user_id'] = $this->user_id;

        if($form['type'] == 'r' || $form['type'] == 's' || $form['type'] == 't')
		{
			$form['front_page'] = 1;
		}

		$calendar_id = $this->model->createNewCalendar($form);
		
		unset($settings);
		$settings['start_month'] = $form['start_month'];
		$settings['start_year'] = $form['start_year'];
		$settings['front_page'] = $form['front_page'];
		$settings['current_month'] = ($settings['front_page'] == 1) ? 'cover' : $form['start_month'];
		$settings['current_year'] = $form['start_year'];
		$settings['type'] = $form['type'];
		$settings['id'] = $calendar_id;
		$settings['pictures'] = array();

		if($form['type'] == 'r' || $form['type'] == 's' || $form['type'] == 't')
		{
			if($settings['start_year'] > (date('Y')+1)) {
				$settings['start_year'] = date('Y')+1;
				$settings['current_year'] = date('Y')+1;
			}
		}

		$modCalStartMonth = unserialize(CAL_START_MONTH_FIXED);
		if(array_key_exists($form['type'], $modCalStartMonth) )
		{
			$settings['start_month'] = $modCalStartMonth[$form['type']];
		}
	
		$this->session->set('settings', $settings);
		
		DirHelper::createUploadFolder($this->user_id, $calendar_id);

		$this->app->redirect('index.php?option=com_calendar&view=calendar&layout=edit#main');
	}

	public function delete()
    {
        $cal_id = JFactory::getApplication()->input->get('cal_id', '0', 'INT');
        $this->model->deleteCalendar($cal_id);
        $this->app->redirect('index.php?option=com_calendar&view=calendar&layout=create');
    }

    public function deleteFromOrderForm()
    {
        $cal_id = JFactory::getApplication()->input->get('cal_id', '0', 'INT');
        $this->model->deleteCalendar($cal_id);
        $this->app->redirect('index.php?option=com_calendar&view=calendar&layout=order');
    }
	
	public function update()
	{
		$jinput = JFactory::getApplication()->input;
		$cal_id = $jinput->get('cal_id', '0', 'INT');

		if ( ! in_array($this->user_id, array(152, 153))) {
            if(!$this->model->isCalendarAssignedToUser($cal_id, $this->user_id))
            {
                $this->app->redirect('index.php?option=com_calendar&view=calendar&layout=create', 'K tejto položke nemáte oprávenie pristupovať! Skúste operáciu opakovať znova alebo kontaktuje administrátora.', 'error');
            }
        }

		$settings = $this->model->getCalendarData($cal_id);
		$settings['current_month'] = ($settings['front_page'] == 1) ? 'cover' : $settings['start_month'];
		$settings['current_year'] = $settings['start_year'];
		$settings['id'] = $settings['cal_id'];
		$settings['pictures'] = $settings['photos'];


		// MOD
		$settings['pictures'] = array_combine(range(1, count($settings['photos'])), array_values($settings['photos']));
		$picturesToPass = array();
		foreach($settings['pictures'] as $key => $value)
		{
			$picturesToPass[$key]['img'] = $settings['pictures'][$key]['image'];
			$picturesToPass[$key]['top'] = $settings['pictures'][$key]['top'];
			$picturesToPass[$key]['left'] = $settings['pictures'][$key]['left'];
			$picturesToPass[$key]['width'] = $settings['pictures'][$key]['width'];
			$picturesToPass[$key]['height'] = $settings['pictures'][$key]['height'];
			$picturesToPass[$key]['year'] = $settings['pictures'][$key]['year'];
            $picturesToPass[$key]['month'] = $settings['pictures'][$key]['month'];
			$picturesToPass[$key]['rotate'] = 'auto';
		}
		
		$settings['pictures'] = $picturesToPass;
		// END MOD

        // set desired month as first in array so it's correctly loaded
        $picutreIndexToRemove = 0;
        foreach ($settings['pictures'] as $index => $picture) {
            if ($picture['month'] == $settings['current_month']) {
                $picutreIndexToRemove = $index;
            }
        }

        if ($picutreIndexToRemove > 0) {
            $cover = $settings['pictures'][$picutreIndexToRemove];
            array_splice($settings['pictures'], $picutreIndexToRemove, 1);
            array_unshift($settings['pictures'], $cover);
        }

        // Move index to start with 1
        $shifterPictures = array();
        foreach ($settings['pictures'] as $index => $picture) {
            $shifterPictures[$index + 1] = $picture;
        }

        $settings['pictures'] = $shifterPictures;

		$modCalStartMonth = unserialize(CAL_START_MONTH_FIXED);
		if(array_key_exists($settings['type'], $modCalStartMonth) )
		{
			$settings['start_month'] = $modCalStartMonth[$settings['type']];
		}

		$this->session->set('settings', $settings);
		$this->app->redirect('index.php?option=com_calendar&view=calendar&layout=edit');
	}
	
	
	/*
	 * Load month settings from database
	 *
	 * @call : this function is called when user pick any month; calendar <view> edit <layout>
	 * @return <void>
	 */
	public function month()
	{
		$settings = $this->session->get('settings', NULL);

		$month = JRequest::getVar('month', 'januar');
		
		$year = JRequest::getVar('year');

		$month_index = array_search($month, $this->months);	
		
		$settings['current_month'] = $this->months[$month_index];
		
		$settings = $this->model->loadMonth($settings);

		$settings['current_year'] = $year;
		
		$this->session->set('settings', $settings);
		
		$this->app->redirect('index.php?option=com_calendar&view=calendar&layout=edit');
	}
	
	
	/*
	 * Save month settings (images positions) to database and update <sessions>
	 *
	 * @call : this function is called when user save month settings; calendar <view> edit <layout>
	 * @return <void>
	 */
	public function save()
	{

		$form = JRequest::get('post');

		$settings = $this->session->get('settings', NULL);

		if(isset($form['front_page_text']))
		{
			$this->model->setCoverText($settings['id'], $form['front_page_text']);
		}
		
		// save month btn
		if ($form['action'] == 'save')
		{
			$settings = $this->savePictures($settings, $form);

			if($settings['front_page'] != 1)
			{
				array_shift($this->months);
			}
			
			// rotate month
			$months = ArrayHelper::rotate($settings['start_month'], $this->months);

			// all positions must be set
			if (CalendarHelper::arePicturesSet($settings['pictures']))
			{
				if($this->model->existsMonth($settings))
				{
					// update pictures in database
					$this->model->updateMonth($settings);
					
					// create backup of new images
					CalendarHelper::makeBackupOfPictures($settings['pictures'], $this->user_id);
				}
				else
				{
					// save month settings to database
					$this->model->saveMonth($settings);
					
					// make backup of image
					CalendarHelper::makeBackupOfPictures($settings['pictures'], $this->user_id);
				}
				
				$current_month_index = array_search($settings['current_month'], $months);
				
				// move to next month index
                if (!in_array($settings['type'], array('r','s','t','u','v'))) {
                    $next_month_index = ++$current_month_index;
                }

				// if december => next month index is 0 (first one)				
				if ($next_month_index == count($this->months))
				{
					$next_month_index = 0;
				}

				$settings['current_month'] = $months[$next_month_index];
				
				$settings = CalendarHelper::clearPictures($settings);
				
				$msg = 'Zmeny uložené[]Vaše nastavenia boli uložené. Môžete pokračovať v editácii ďalšieho mesiaca.'; $msg_type = '';
			}
			else
			{
				$msg = 'Nevyplnili ste všetky pozície[]Vyplňte prosím všetky pozície kalendára.'; $msg_type = 'error';
			}
			
			$this->session->set('settings', $settings);
			
			
			// january is not first month
			//if(array_search('january', $months) != 1) - MOD MAREK 27.11.2013
			if($settings['start_month'] != 'january')
			{
				// if next month is after janury increase year
				if(array_search($settings['current_month'], $months) >= array_search('january', $months))
				{
					$year = $settings['start_year'] + 1;
				}
				else
				{
					$year = $settings['start_year'];
				}
			}
			else
			{
				$year = $settings['start_year'];
			}
			
			if($settings['type'] == 'r' || $settings['type'] == 's' || $settings['type'] == 't')
			{
				$settings['current_month'] = 'cover';
			}
			
			$this->app->redirect('index.php?option=com_calendar&task=calendar.month&year=' . $year . '&month=' . $settings['current_month'] . '#main', $msg, $msg_type);
		}
		// change picture btn
		else
		{
			$settings = $this->updatePictures($settings, $form);
		
			$this->session->set('settings', $settings);
			
			$position = $form['position'] + 1;
			
			$this->app->redirect('index.php?option=com_calendar&view=calendar&layout=edit' . '&position=' . $position . "#main", $msg = '', $msg_type = '');
		}
	}


	/*
	 * Save pictures settings (images positions) to <sessions>
	 *
	 * @call : this function is called when user save month; calendar <view> edit <layout>
	 * @return <void>
	 */	
	private function savePictures($settings, $form)
	{
		for ($i=1; $i<$form['total_positions']+1; $i++)
		{
			$settings['pictures'][$i]['img'] = empty( $form["cal_img{$i}"] ) ? 'auto' : $form["cal_img{$i}"];
			$settings['pictures'][$i]['top'] = empty( $form["cal_top{$i}"] ) ? 'auto' : $form["cal_top{$i}"];
			$settings['pictures'][$i]['left'] = empty( $form["cal_left{$i}"] ) ? 'auto' : $form["cal_left{$i}"];
			$settings['pictures'][$i]['width'] = empty( $form["cal_width{$i}"] ) ? 'auto' : $form["cal_width{$i}"];
			$settings['pictures'][$i]['height'] = empty( $form["cal_height{$i}"] ) ? 'auto' : $form["cal_height{$i}"];
			$settings['pictures'][$i]['rotate'] = empty( $form["cal_rotate{$i}"] ) ? 'auto' : $form["cal_rotate{$i}"];
		}
		
		return $settings;
	}
	
	
	/*
	 * Update pictures settings (images positions) to <sessions>
	 *
	 * @call : this function is called when user change picture; calendar <view> edit <layout>
	 * @return <void>
	 */	
	private function updatePictures($settings, $form)
	{
		for ($i=1; $i<$form['total_positions']+1; $i++)
		{
			// set default values <auto> if picture at selected position was changed
			if ($form['position'] == $i)
			{
				$settings['pictures'][$i]['img'] = $form["cal_img{$i}"];
				$settings['pictures'][$i]['top'] = 'auto';
				$settings['pictures'][$i]['left'] = 'auto';
				$settings['pictures'][$i]['width'] = 'auto';
				$settings['pictures'][$i]['height'] = 'auto';
				$settings['pictures'][$i]['rotate'] = 'auto';
			}
			// set values from submited form calendar <view> edit <layout>; if $form is empty use default <auto>
			else
			{
				$settings['pictures'][$i]['img'] = $form["cal_img{$i}"];
				$settings['pictures'][$i]['top'] = empty( $form["cal_top{$i}"] ) ? 'auto' : $form["cal_top{$i}"];
				$settings['pictures'][$i]['left'] = empty( $form["cal_left{$i}"] ) ? 'auto' : $form["cal_left{$i}"];
				$settings['pictures'][$i]['width'] = empty( $form["cal_width{$i}"] ) ? 'auto' : $form["cal_width{$i}"];
				$settings['pictures'][$i]['height'] = empty( $form["cal_height{$i}"] ) ? 'auto' : $form["cal_height{$i}"];
				$settings['pictures'][$i]['rotate'] = empty( $form["cal_rotate{$i}"] ) ? 'auto' : $form["cal_rotate{$i}"];
			}
		}
		
		return $settings;
	}

	function debug($a, $exit = false, $userId = 0)
	{
	    if ($userId == 152) {
            echo "<pre>";
            var_dump($a);
            echo "</pre>";
            if($exit) {
                exit;
            }
        }
	}

    /*******************************************
     * AJAX request handlers
     *******************************************/

	public function getShoppingCartDataAsJSON()
    {
        $user = JFactory::getUser()->get('id');

        $finished_calendar = CalendarHelper::getCalendars($user, $this->model, true);


        $price_total = 0;

        $quantity_total = 0;

        foreach ($finished_calendar as $index => $calendar)
        {
            $price_total += $calendar['price_total'];

            $quantity_total += $calendar['quantity'];
        }

        echo json_encode(
            array(
                'price' => $price_total,
                'quantity' => $quantity_total,
            )
        );

        jexit();
    }

    public function setCalendarQuantity()
    {
        $user_id = JFactory::getUser()->get('id');

        $input = JFactory::getApplication()->input;

        $quantity = $input->post->get('quantity', '0');

        $calendar_id = $input->post->get('calendar_id', '0');

        $this->model->updateCalendarQuantity($calendar_id, $user_id, $quantity);

        jexit();
    }

    public function verifyDiscountCoupon()
    {
        $input = JFactory::getApplication()->input;

        $coupon_code = $input->post->get('coupon_code', '');

        $coupon = $this->model->getCouponByCode($coupon_code);

        echo json_encode(
            array(
                'discount' => $coupon->discount,
                'valid'    => $coupon->valid
            )
        );

        jexit();
    }
}