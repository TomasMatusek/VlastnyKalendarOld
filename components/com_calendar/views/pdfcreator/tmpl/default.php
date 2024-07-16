<?php 
if($this->calendar['data']["start_year"]== 0)
{
	$this->calendar['data']["start_year"] = 2014;
}
$this->calendar['data']["start_year"] = 2014;
$i = 1;

$monthCals = array('c','i','m','f');
$hasCurrentMonth = false;

if(in_array($this->calendar["type"],$monthCals))
{
	$hasCurrentMonth = true;
}

$startMonthNum = date_parse($this->calendar['data']['start_month']);
$currentMonthNum = date_parse($this->calendar['month']);
if($currentMonthNum < $startMonthNum)
{
	if( ($this->calendar['data']["start_year"] - $this->calendar["year"]) != -1)
	{
		$this->calendar["year"] = $this->calendar["year"]+0;
		$this->calendar["year"]++;
	}
}



?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="sk-sk" lang="sk-sk" dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<style media="all">
			@page { margin: 0in 0in 0in 0in;}
		</style>
	<link type="text/css" rel="stylesheet" href="https://www.vlastnykalendar.sk/components/com_calendar/assets/css/pdfcreator.css" />
   <style type="text/css">
	.cal-holder.cal-<?php echo $this->calendar['type']; ?>.<?php echo $this->calendar['month']; ?> {
		background-color: white;
		background-image: url("https://www.vlastnykalendar.sk/components/com_calendar/assets/img/calendars/press/<?php echo $this->calendar['type'].'/type_'.$this->calendar['type'].'_'.$this->calendar["year"].'_'.$this->calendar['month']; ?>.jpg");
		background-position: center center;
		background-repeat: no-repeat;
		height: <?php echo $this->calendar['sizes']['height'];?>px;
   	    overflow: hidden;
 	    position: relative;
        width: <?php echo $this->calendar['sizes']['width'];?>px;
	}
	<?php if($hasCurrentMonth) { ?>
		#currentMonth {
			background-color: transparent;
			background-repeat: no-repeat;
			background-position: center center;
  	  width: <?php echo $this->calendar['sizes']['width'];?>px;
			height: <?php echo $this->calendar['sizes']['height'];?>px;
		}
		#currentMonth img {
			position: absolute;
			top: 0px;
		}
	<?php } ?>
	</style>
</head>
<body style="margin:0 !important;padding:0 !important;">

<div style="margin:0 !important;padding:0 !important;border:0; overflow:hidden; position:relative;">
	<div class="cal-holder cal-<?php echo $this->calendar['type']; ?> <?php echo $this->calendar['month'].' '.$this->calendar['year'];?>">	
 
		<?php if($this->calendar["month"] == 'cover')
		{	
			echo '<div id="customText" style="font-family:dejavu sans;">'.htmlspecialchars_decode($this->calendar["data"]["front_page_text"]).'</div>';
		} ?>
	
		<?php 
		foreach($this->img as $image)
		{
			echo '<div class="image img-'.$i.'" style="position:absolute !important; overflow:hidden !important;">';
			echo $image;
			echo '</div>';
			$i++;
		} ?>
  
		<?php if($hasCurrentMonth) {	
      echo '<div id="currentMonth">'; 
      echo "<img src='https://www.vlastnykalendar.sk/components/com_calendar/assets/img/calendars/press/".$this->calendar['type']."/type_".$this->calendar['type']."_".$this->calendar["year"]."_month_".$this->calendar['month'].".png' />";
      echo "</div>";
    }?>
  
	</div>
		<div style="position:absolute; bottom: 0px; height: 15pt; width:<?php echo $this->calendar['sizes']['width'];?>px; background: white;">&nbsp;</div>
</div>
</body>
</html>

