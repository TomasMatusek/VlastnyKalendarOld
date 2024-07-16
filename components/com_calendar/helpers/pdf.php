<?php
/**
 * @package			Calendar component
 * @subpackage	PDF creator Helper
 */

defined('_JEXEC') or die('Restricted access');

include_once(JPATH_COMPONENT . '/libs/pdf/mpdf.php');

class Pdf 
{
	public static function create($order_id, $user_id, $type = 'order', $invoice_nubmer = '', $invoiceDate) // invoice, order
	{
		//$url = "https://www.vlastnykalendar.sk/index.php?option=com_calendar&view=invoice&tmpl=component&type=$type&order=$order_id&hash=8beab81bf697420b2f6dec1f18555ff996c0148d54127c87ff7cb827f27a5e6e";
		
		//var_dump( $invoice_nubmer );
		//exit;
		
		if($invoice_nubmer != false || $invoice_nubmer != '')
		{
			// Vlastne cislo objednavky
			$url = "https://www.vlastnykalendar.sk/index.php?option=com_calendar&view=invoice&tmpl=component&type=" . $type . "&date=".$invoiceDate."&invoicenumber=" . $invoice_nubmer . "&order=" . $order_id . "&hash=8beab81bf697420b2f6dec1f18555ff996c0148d54127c87ff7cb827f27a5e6e";

		}
		else 
		{
			// Cislo z DB
			$url = "https://www.vlastnykalendar.sk/index.php?option=com_calendar&view=invoice&tmpl=component&type=" . $type . "&order=" . $order_id . "&tmpl=component&order=" . $order_id."&hash=8beab81bf697420b2f6dec1f18555ff996c0148d54127c87ff7cb827f27a5e6e";
		}
		//echo $user_id.'<br>'.$order_id.'<br>';
		//echo $url;
		//exit;
		

		
		$html = file_get_contents($url);
		
		$html .= $url;

		$mpdf = new mPDF('utf-8');
		
		$mpdf->allow_charset_conversion = true;
		
		$mpdf->setFooter('{PAGENO}');
		
		$stylesheet = file_get_contents('style.css');
		
		$mpdf->WriteHTML($stylesheet, 1);
		
		$mpdf->WriteHTML($html);
		
		$folder = CAL_ROOT_SERVER . 'calendar/' . $user_id . '/invoice/';
		
		$file = $folder . $type . $order_id . '.pdf';
		
		$mpdf->Output($file, F);
	}	
	
	
	public static function merge_invoice($postData)
	{
		//https://www.vlastnykalendar.sk/index.php?option=com_calendar&view=invoice&merged_invoice=true&layout=merged&tmpl=component&hash=8beab81bf697420b2f6dec1f18555ff996c0148d54127c87ff7cb827f27a5e6e
		
		$url  = 'https://www.vlastnykalendar.sk/index.php?option=com_calendar';
		$url .= '&orders='.$postData["orders"];
		$url .= '&transport='.$postData["transport_method"];
		$url .= '&payment='.$postData["payment_method"];
		$url .= '&coeficient='.$postData["coeficient"];
		$url .= '&invoice_num='.$postData["invoice_num"];
		$url .= '&date='.$postData["date"];
		/// ------- ///
		$url .= '&hash=8beab81bf697420b2f6dec1f18555ff996c0148d54127c87ff7cb827f27a5e6e';
		$url .= '&view=invoice&merged_invoice=true&layout=merged&tmpl=component';
		
		//echo $url;
		//exit;
		
		$html = file_get_contents($url);
		
		$html .= $url;

		$mpdf = new mPDF('utf-8');
		
		$mpdf->allow_charset_conversion = true;
		
		$mpdf->setFooter('{PAGENO}');
		
		$stylesheet = file_get_contents('style.css');
		
		$mpdf->WriteHTML($stylesheet, 1);
		
		$mpdf->WriteHTML($html);
		$name = 'faktura_'.date('Y').$postData["invoice_num"].'.pdf';
		$mpdf->Output($name, 'D');
		exit;
		
	}
	
	
}