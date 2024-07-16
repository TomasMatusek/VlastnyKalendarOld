<?php
require_once("../dompdf_config.inc.php");

$url = "https://www.vlastnykalendar.sk/dompdf/cal/cal-for.php";
$html = file_get_contents($url);

$dompdf = new DOMPDF();
$paperSize = array(0,0,915,400);

$dompdf->set_paper($paperSize);

$dompdf->load_html($html);
$dompdf->render();
$dompdf->stream("welcome.pdf", array("Attachment" => 0));  

?>