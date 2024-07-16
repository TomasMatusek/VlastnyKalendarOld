<?php

/**
 * @package			Calendar Component
 * @subpackage	Calendar View
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class CalendarViewCalendar extends JViewLegacy
{
	function display($tpl = null) 
	{
		if($this->layout != 'pricelist')
		{
			if ($this->user_group == 'guest')
			{
				$this->app->redirect('index.php?option=com_users&view=login&Itemid=133', 'Najprv sa prosím prihláste. Ak ešte nemáte konto registrujte sa u nás!');
			}
		}

		$this->document->addStyleSheet( CAL_COMPONENT_WEB . 'assets/css/bootstrap.min.css');
		$this->document->addStyleSheet( CAL_COMPONENT_WEB . 'assets/css/bootstrap-image-gallery.css');
		$this->document->addStyleSheet( CAL_COMPONENT_WEB . 'assets/css/style.css');
		$this->document->addStyleSheet( CAL_COMPONENT_WEB . 'assets/css/jquery.fileupload-ui.css');
		$this->document->addStyleSheet( CAL_COMPONENT_WEB . 'assets/css/settings.css');
		$this->document->addStyleSheet( CAL_COMPONENT_WEB . 'assets/css/ibutton.css');
		$this->document->addStyleSheet( CAL_COMPONENT_WEB . 'assets/css/viewer.css');

        $this->document->addScript( CAL_COMPONENT_WEB . 'assets/js/jquery.js' );
		$this->document->addScript( CAL_COMPONENT_WEB . 'assets/js/jquery.ui.js' );
		$this->document->addScript( CAL_COMPONENT_WEB . 'assets/js/jquery.mousewheel.js' );
		$this->document->addScript( CAL_COMPONENT_WEB . 'assets/js/jquery.iviewer.js' );
		$this->document->addScript( CAL_COMPONENT_WEB . 'assets/js/jquery.calendar.js' );
		$this->document->addScript( CAL_COMPONENT_WEB . 'assets/js/jquery.scripts.js' );
		$this->document->addScript( CAL_COMPONENT_WEB . 'assets/js/jquery.bootstrap.js' );

		$user = JFactory::getUser();
        $input = JFactory::getApplication()->input;
		$user_id = $user->get( 'id' );

        $this->data['user_id'] = $user_id;
		$this->data['calendarPriceList'] = Price::getCalendarPrices();

		switch($this->layout)
		{
			case 'create':

                $this->data['non_finished_calendars'] = CalendarHelper::getCalendars($user_id, $this->model, false);
			
			break;	
			
			
			case 'edit':
				$settings = $this->session->get('settings', NULL);
				if (empty($settings))
				{
					$this->app->redirect('index.php?option=com_calendar&view=calendar', false);	
				}
				
				// Check if order is send
				if(isset($settings['cal_id']))
				{
					if($this->model->checkOrderSendStatus($settings, $user_id))
					{
						// $this->app->redirect('index.php?option=com_calendar&view=calendar', 'Prosím, pokračujte výberom kalendára', 'Informácia');
					}
				}
				
				if($settings['current_month'] == 'cover')
				{
					$this->data['front_page_text'] = $this->model->getCoverText($settings['id']);
				}

				// array of months
				$this->data['id'] = $settings['id'];

				// remove cover from array
				if($settings['front_page'] == 0)
				{
					array_shift($this->months);
				}
				
				// array of months
				$this->data['months'] = ArrayHelper::rotate($settings['start_month'], $this->months);

                if (in_array($settings['type'], array('r','s','t','u','v'))) {
                    $this->data['months'] = array('cover');
                }
				
				// array of years
				$this->data['years'] = CalendarHelper::getYears($this->data['months'], $settings['start_year']);
				
				// calendar layout to load
				$this->data['layout'] = CalendarHelper::getLayout($settings['type']);
				
				$this->data['type'] = $settings['type'];
				
				if($settings['type'] == 'r' || $settings['type'] == 's' || $settings['type'] == 't')
				{
					$this->data['months'] = array();
					$this->data['months'][0] = 'cover';
				}
				
				// current month
				$this->data['current_month'] = $settings['current_month'];

				// current year
				$this->data['current_year'] = $settings['current_year'];

                // array of full size images uploaded by user
                $this->data['images'] = FileHelper::getUserImagesAndThumbs($this->user_id);

				// already used imagesavealpha
				$this->data['thumbs_used'] = $this->model->getUsedImages($this->data['images']['img'], $settings['id']);

				// IMAGES
				$user = JFactory::getUser();
				$userId = $user->get( 'id' );

				// number of calendar positions according to it's type
				$cover = ($settings['current_month'] == 'cover') ? true : false;
				$this->data['positions'] = CalendarHelper::getPicturePositions($settings['type'], $cover);

				$modCalPositions = unserialize(CAL_LAYOUT_MOD_POSITIONS_COUNT);

				if( array_key_exists($settings['type'], $modCalPositions) )
				{
					$month = $this->data['current_month'];
					$year 	= $this->data['current_year'];
					
					if ( array_key_exists($year, $modCalPositions[$settings['type']]) ) {
						if ( array_key_exists($month, $modCalPositions[$settings['type']][$year]) ) {
				    	$this->data['positions'] = $modCalPositions[$settings['type']][$year][$month]['positions'];
						}
					}
					//$this->debug($modCalPositions);
				}

				// total number of positions (all months)
				$calendar_cover_sizes = unserialize(CAL_SIZES);
				$calendar_positions = unserialize(CAL_LAYOUT_POSITIONS);
				$calendar_cover_positions = unserialize(CAL_COVER_LAYOUT_POSITIONS);


				if( array_key_exists($settings['type'], $modCalPositions) )
				{
					$this->data['positions_total'] = ($settings['front_page'] == 0) ? ($modCalPositions[$settings['type']]['totalPositionsWithoutCover']) : ($modCalPositions[$settings['type']]['totalPositionsWithoutCover']) + $calendar_cover_positions[$settings['type']];
				}
				else
				{
					$this->data['positions_total'] = ($settings['front_page'] == 0) ? ($calendar_positions[$settings['type']] * 12) : ($calendar_positions[$settings['type']] * 12) + $calendar_cover_positions[$settings['type']];
				}
				
				// total positions filled (all months)
				$this->data['positions_filled'] = $this->model->filledMonths($settings['id']);
			
				// if array does not exists => create
				$settings['pictures'] = !isset($settings['pictures']) ? array() : $settings['pictures'];
				
				// if array is not filled => fill with default values
				$settings['pictures'] = ArrayHelper::getEmptyArray($this->data['positions'], $settings['pictures']);


				
				// Check if image was deleted, if yes loag image from backup
				for($g=1;$g<=count($settings['pictures']);$g++)
				{
					if(!@GetImageSize($settings['pictures'][$g]["img"]))
					{
						$oldImgURL = $settings['pictures'][$g]["img"];
						$settings['pictures'][$g]["img"] = str_replace("/img/","/img_backup/",$oldImgURL);
					}
				}

                // $this->debug($settings['pictures'], $user_id);

				// generates iWiever jqueries
				$this->data['javascript'] = CalendarHelper::getJquery($settings['pictures']);
				
				// pictures in use
				$this->data['imgs'] = $settings['pictures'];

                // Check for optimal dimensions
                $dimensionsError = array();
                $optimalImageDimensions = unserialize(CAL_MIN_DIMENSIONS)[$this->data["type"]];

                if($this->data['current_month'] == 'cover') {
                    $optimalImageDimensions = $optimalImageDimensions['cover'];
                } else {
                    $optimalImageDimensions = $optimalImageDimensions['page'];
                }

                foreach($this->data['imgs'] as $key => $usedImage) {

                    if($usedImage['img'] != 'auto') {
                        if (!file_exists($usedImage['img'])) {
                            continue;
                        }
                        $this->data['imgs'][$key]['dimensions'] = getimagesize($usedImage['img']);
						if(isset($optimalImageDimensions[$key])) {
							$optimalWidth = $optimalImageDimensions[$key]['width'];
	                        $optimalHeight = $optimalImageDimensions[$key]['height'];
	
	                        if($this->data['imgs'][$key]['dimensions'][0] < $optimalWidth or $this->data['imgs'][$key]['dimensions'][1] < $optimalHeight) {
	                            $dimensionsError[] = array(
	                                'position' => $key,
	                                'optimalDimensions' => $optimalWidth.'x'.$optimalHeight
	                            );
	                        }
						}
                        
                    }
                }

                $this->data['dimensionError'] = $dimensionsError;

				$this->data['front_page'] = $settings['front_page'];

				if ($this->data['positions_filled'] >= $this->data['positions_total'])
				{
					$this->model->setOrderStatus($settings['id'], '1');
				}

			break;		
			
			
			case 'order':

				$this->data['finished_calendars'] = CalendarHelper::getCalendars($user_id, $this->model, true);
                $this->data['user_id'] = $user_id;

				if ($user_id == 153) {
					$userProfile = JUserHelper::getProfile($user_id);
					if ($userProfile != null) {
						$userProfile->profile['address1'];
						$this->str_replace_once('0', '+421', '0944290079');
					}
				}

                if (empty($this->data['finished_calendars']))
                {
                    $this->app->redirect('index.php?option=com_calendar&view=calendar', false);
                }
				
			
			break;

            case 'order_success':

				// 1. After order submit user is redirected directly to the web pay portal
				// 2. If user is returned from webpay portal (identified by URL parameters) do not redirect
				// 3. Evaluate returns code from web pay portal to veiryf if payment was success or not

                $this->data['order'] = $this->model->getOrderDetail($input->get('order_id', 0), $user_id);
				$order_id = $this->data['order']['order']['order_id'];

				// When online payment enabled
				$payment = $this->data['order']['order']['payment_method'];
				if ($payment === 'card') {

					$paymentService = new PaymentService(
						CalendarConstants::$GP_WEBPAY_MERCHANT_NUMBER,
						CalendarConstants::$GP_WEBPAY_PRIVATE_KEY_FILE,
						CalendarConstants::$GP_WEBPAY_PRIVATE_KEY_PASS,
						CalendarConstants::$GP_WEBPAY_PUBLIC_KEY_FILE,
						'https://vlastnykalendar.sk/index.php?option=com_calendar&view=calendar&layout=order_success&order_id=' . $order_id,
						CalendarConstants::$GP_WEBPAY_TESTING
					);

					$price = $this->data['order']['order']['price_calendars'] + $this->data['order']['order']['price_shipping_and_packing'];
					$order_number = $paymentService->generateRandomOrderNumber();
					$email = $this->data['order']['order']['billing_mail'];

					$this->model->gpWebpayCreate($order_id, $order_number);
					$this->data['price'] = $price;
					$this->data['payment_link'] = $paymentService->createPaymentLink(bcmul($price, 100), $order_id, $order_number, $email);
					$this->data['is_paid'] = count($this->model->getGpWebpayPaid($order_id)) > 0;

					// This is returned from web pay portal
					$returnFromWebPay = $input->get('MD', '') == 'dda7bab6bf2a11eb85290242ac130003';
					$this->data['payment_returned_from_gpwebpay'] = $returnFromWebPay;

					// If not paid redirect directly to paiment portal, but only if user is not returning from webapy portal
					// if ( ! $this->data['is_paid'] && ! returnFromWebPay) {
					// 	$app =& JFactory::getApplication();
					// 	$app->redirect($this->data['payment_link']);
					// 	return;
					// }


					// User is returning from GP Webpay
					if ($returnFromWebPay) {

						// if sucess -> write result into DB
						// digest must be verified so user cant change the response status in URL
						$digestInvalid = ! $paymentService->isResponseSignatureValid($_SERVER['REQUEST_URI']);
						$this->data['payment_error'] = false;
						$this->data['payment_error_text'] = $digestInvalid ? 'Signature' : $input->get('RESULTTEXT', '');
						$this->data['payment_error_srcode'] = $input->get('SRCODE', 0);
						$this->data['payment_error_prcode'] = $prcode = $input->get('PRCODE', 0);
						$this->data['payment_order_number'] = $prcode = $input->get('ORDERNUMBER', 0);
					
						// If digest is invalid or any other error raised -> display error message
						if ($digestInvalid || 
							$this->data['payment_error_srcode'] != 0 || 
							$this->data['payment_error_prcode'] != 0) {
							
							$this->data['payment_error'] = true;
							$this->model->updateGpWebpayState(
								$order_id, 
								$this->data['payment_order_number'],
								$this->data['payment_error_prcode'],
								$this->data['payment_error_srcode'],
								$this->data['payment_error_text'],
								'failed'
							);

						}
						// Payment sucess
						else {
							$this->model->updateGpWebpayState(
								$order_id, 
								$this->data['payment_order_number'],
								$this->data['payment_error_prcode'],
								$this->data['payment_error_srcode'],
								$this->data['payment_error_text'],
								'paid'
							);
						}
					}

					$this->data['is_paid'] = count($this->model->getGpWebpayPaid($order_id)) > 0;
				}

            break;
			
			
			case 'upload':

			
			break;
			
			
			case 'pricelist':
				$this->document->setTitle( 'Cenník' );
			break;
		}

		
		

		parent::display();
	}

	function str_replace_once($needle, $replace, $haystack)
	{
		if (($pos = strpos($haystack, $needle)) === false) {
			return $haystack;
		}
		return substr_replace( $haystack, $replace, $pos, strlen( $needle ) );
	}

	function debug($text, $userId)
	{
		if($userId == 152)
		{
            echo "<pre>";
            var_dump($text);
            echo "</pre>";
		}
	}
}
