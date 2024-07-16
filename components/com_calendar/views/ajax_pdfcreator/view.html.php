<?php

/**
 * @package			Calendar Component
 * @subpackage	User View
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class CalendarViewAjax_pdfcreator extends JViewLegacy
{

	function display($tpl = null) 
	{

		if ($this->user_group == 'guest')
		{
			//$this->app->redirect('index.php?option=com_users', false);
		}
		
		$calendar['layout'] = JRequest::getVar( 'layout', '', 'get' );

		if($calendar['layout'] == 'pdf')
		{
			//require_once(CAL_COMPONENT_SERVER . "libs/dompdf050/dompdf_config.inc.php");

			$cal_id = JRequest::getVar('calendar', '', 'get');
			$month = JRequest::getVar('month', '', 'get');
		  
			$calendarInfo = array( 'id' => $cal_id, 'current_month' => $month);
			$monthInfo = $this->model->loadMonth($calendarInfo);
			
			$cal_pdf_size = unserialize(CAL_PDF_SIZES);
			$cal_pt_size = unserialize(CAL_PDF_SIZES_TOTAL);
			$this->calendar = $this->model->getCalType($cal_id);
			
			if($month == 'cover')
			{
				$url = "https://www.vlastnykalendar.sk/index.php?option=com_calendar&view=pdfcreator&layout=default&calendar=".$cal_id."&month=".$month."&cover=true&tmpl=component";
			}
			else {
				$url = "https://www.vlastnykalendar.sk/index.php?option=com_calendar&view=pdfcreator&layout=default&calendar=".$cal_id."&month=".$month."&tmpl=component";
			}

			// PARMS
			$dpi = 300;
			//echo $this->calendar["type"];
			$Calendars_300_DPI = unserialize(CAL_300_DPI_TYPES);

			if(in_array($this->calendar["type"], $Calendars_300_DPI))
			{
				$dpi = 300;
			}
			
			// Mod calendar sizes
			$modCalSizePDF = unserialize(CAL_MOD_PDF_SIZE);
			if( array_key_exists($this->calendar['type'], $modCalSizePDF) )
			{
				$currentMonth = $monthInfo["current_month"];
				$currentYear 	= $monthInfo["year"];
				
				if ( array_key_exists($currentYear, $modCalSizePDF[$this->calendar['type']]) ) {
					if ( array_key_exists($currentMonth, $modCalSizePDF[$this->calendar['type']][$currentYear]) ) {
			    	$cal_pt_size[$this->calendar['type']] = $modCalSizePDF[$this->calendar['type']][$currentYear][$currentMonth];
					}
				}
			}
			//echo '<pre>'; print_r($cal_pt_size[$this->calendar['type']]); echo '</pre>';

			
			require_once(CAL_COMPONENT_SERVER . "libs/dompdf/dompdf_config.inc.php");
			
			/*
			 *
			 * NOVE PARAMETRE Z 10.07.2013
			 *
			*/

			$html = file_get_contents($url);

			$dompdf = new DOMPDF();
			$paperSize = array(0, 0, $cal_pt_size[$this->calendar["type"]]['wpoints'], $cal_pt_size[$this->calendar["type"]]['hpoints']+14.70);

			$dompdf->set_paper($paperSize);

			$dompdf->load_html($html);
			$dompdf->render();
			$dompdf->stream("type_".$this->calendar["type"]."_".$month.".pdf", array("Attachment" => 1));
			
		}
		else
		{

			$calendar['id'] = JRequest::getVar( 'calendar', 0, 'get' );
			
			$calendar['current_month'] = JRequest::getVar( 'month', 'january', 'get' );

			$month = $this->model->loadMonth($calendar);

			$this->calendar = $this->model->getCalType($month['id']);

			$this->document->addStyleSheet(CAL_COMPONENT_WEB . 'assets/css/pdfcreator.css');

			$src = CAL_ROOT_SERVER . str_replace( 'https://vlastnykalendar.sk//', '', str_replace('/img/', '/img_backup/', $month['pictures'][1]['img']));

			// var_dump($month);
			// echo $src; exit;

			$image = new Imagick();
            // var_dump($month);exit;
			//$image->setResolution(300, 300);
			//$image->setResolution(100, 100);
			$image->readImage($src);
			//$image->resampleImage(300, 300, imagick::FILTER_UNDEFINED, 1);
			//$image->resampleImage(100, 100, imagick::FILTER_UNDEFINED, 1);
			//$image->writeImage($src);
			$array = $image->getImageResolution();
		
			//var_dump($array);
			//die();
			
			$cal_size = unserialize(CAL_SIZES);
			$cal_cover_size = unserialize(CAL_COVER_SIZES);
			$cal_pdf_size = unserialize(CAL_PDF_SIZES);
			$cal_cover_pdf_size = unserialize(CAL_PDF_COVER_SIZES);
			$cal_size_total = unserialize(CAL_PDF_SIZES_TOTAL);				
			$cal_pos_per_page = unserialize(CAL_LAYOUT_POSITIONS_PER_PAGE);

			$this->formalPicturesCount = 1;
			
			// UNSET UNUSED IMAGES NOT EQUAL SERIES THAT ARE REQUESTED
			$series = JRequest::getVar('series', '1', 'get');
			
			if($series !== false)
			{
				$num_of_pictures = count($month['pictures']);
				if($num_of_pictures > $cal_pos_per_page[$this->calendar['type']])
				{
					//$this->formalPicturesCount = count($month['pictures']);
					$this->formalPicturesCount = count($month['pictures'])/$cal_pos_per_page[$this->calendar['type']];
					
					// MUSIME UNSETOVAT
					$newMonthPictures = array_chunk($month['pictures'], $cal_pos_per_page[$this->calendar['type']]);
					//echo '<hr /><pre>'; print_r($newMonthPictures); echo '</pre>';
					//$month['pictures'] = $newMonthPictures[$series];
					// echo '<pre>'; var_dump($newMonthPictures);
					$month['pictures'] = array_filter(array_merge(array(0), $newMonthPictures[$series]));
					
				}
			}
			//echo '<hr /><pre>'; print_r($month['pictures']); echo '</pre>';
			$cover = JRequest::getVar('cover', 'false', 'get');

			// cover
			if($cover == 'true')
			{
				// loop all cover positions
				for($i=0; $i<count($cal_cover_size[$this->calendar['type']]); $i++)
				{
					$j = $i + 1;

					$width = Pdfcreator::getRationSize( $cal_cover_size[$this->calendar['type']][$i]['width'], $month['pictures'][$j]['width'], $cal_cover_pdf_size[$this->calendar['type']][$i]['width']);
					$height = Pdfcreator::getRationSize( $cal_cover_size[$this->calendar['type']][$i]['height'], $month['pictures'][$j]['height'], $cal_cover_pdf_size[$this->calendar['type']][$i]['height']);
					$top = Pdfcreator::getRationPosition( $cal_cover_size[$this->calendar['type']][$i]['height'], $month['pictures'][$j]['top'], $cal_cover_pdf_size[$this->calendar['type']][$i]['height']);
					$left = Pdfcreator::getRationPosition( $cal_cover_size[$this->calendar['type']][$i]['width'], $month['pictures'][$j]['left'], $cal_cover_pdf_size[$this->calendar['type']][$i]['width']);
					
					$this->calendar['data'] = $this->model->getCalendarDetail($month['id']);
					$this->calendar['month'] = $calendar['current_month'];
					$this->calendar['sizes'] = $cal_size_total[$this->calendar["type"]];
					$this->calendar['year'] = $month['pictures'][$j]['year'];

					$this->img[$i] = "<img src='".str_replace('/img/', '/img_backup/', $month['pictures'][$j]['img'])."' style='left: ".$left."px; top: ".$top."px; width: ".$width."px; height: ".$height."px;' />";
				}
			}
			// month
			else
			{
				// loop all month positions
				for($i=0; $i<count($cal_size[$this->calendar['type']]); $i++)
				{
					$j = $i + 1;
					
					if(isset($month['pictures'][$j]))
					{
						$width = Pdfcreator::getRationSize( $cal_size[$this->calendar['type']][$i]['width'], $month['pictures'][$j]['width'], $cal_pdf_size[$this->calendar['type']][$i]['width']);
						$height = Pdfcreator::getRationSize( $cal_size[$this->calendar['type']][$i]['height'], $month['pictures'][$j]['height'], $cal_pdf_size[$this->calendar['type']][$i]['height']);
						$top = Pdfcreator::getRationPosition( $cal_size[$this->calendar['type']][$i]['height'], $month['pictures'][$j]['top'], $cal_pdf_size[$this->calendar['type']][$i]['height']);
						$left = Pdfcreator::getRationPosition( $cal_size[$this->calendar['type']][$i]['width'], $month['pictures'][$j]['left'], $cal_pdf_size[$this->calendar['type']][$i]['width']);

						$this->calendar['data'] = $this->model->getCalendarDetail($month['id']);
						$this->calendar['month'] = $calendar['current_month'];
						$this->calendar['sizes'] = $cal_size_total[$this->calendar["type"]];
						$this->calendar['year'] = $month['pictures'][$j]['year'];
	
						$this->img[$i] = "<img src='".str_replace('/img/', '/img_backup/', $month['pictures'][$j]['img'])."' style='left: ".$left."px; top: ".$top."px; width: ".$width."px; height: ".$height."px;' />";
					}
				}
			}
		}

		parent::display($tpl);
	}
}