<?php



$image = new Imagick();
$image->setResolution(300,300) ;
$image->readImage("aa5.jpg");
$image->resampleImage(300,300,imagick::FILTER_UNDEFINED,1);
$image->writeImage('test.jpg');
$array=$image->getImageResolution();

$bleed = 35;
$parmW = 5.125;
$parmH = 5.39;

$defaultWidth = 800;
$defaultHeight = 600;
$defaultTop = -166;
$defaultLeft = -181;

$newWidth = ($defaultWidth*$parmW)+$bleed;
$newHeight = ($defaultHeight*$parmH)+$bleed;
$newTop = ($defaultTop*$parmH)+$bleed;
$newLeft = ($defaultLeft*$parmH)+$bleed;

$html = '<html><head><style>
@page { margin: 0px; }
body { margin: 0px; }
.cal-holder { background: url("cal.jpg") repeat scroll center center red;
    height: 1589px;
    overflow: hidden;
    position: relative;
    width: 3798px;
}

.image {
    background: none repeat scroll 0 0 red;
    left: 1492px;
    top: 80px;
	overflow: hidden;
	position: absolute;
	width: 2245px;
	height: 1430px;
}
img {
    
    max-width: none;
    overflow: hidden;
    position: absolute;
		left: '.$newLeft.'px;
    top: '.$newTop.'px;
		width: '.$newWidth.'px;
		height: '.$newHeight.'px;
}
</style>
</head>
<body style="margin:0;padding:0;border:0">
<div class="cal-holder">	
	<div class="image"><img src="test.jpg" /></div>
</div>
</body>
</html>
';

//



//==============================================================
//==============================================================
//==============================================================

echo $html;

//==============================================================
//==============================================================
//==============================================================

?>
