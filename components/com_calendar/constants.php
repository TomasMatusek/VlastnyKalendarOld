<?php

class CalendarConstants {

    public static $DOMAIN = "https://vlastnykalendar.sk";

    // Email settingss

    public static $EMAIL_HOST = "mail2.nameserver.sk";
    public static $EMAIL_PORT = 465;
    public static $EMAIL_USER = "objednavky@vlastny-kalendar.sk";
    public static $EMAIL_PASS = "Rida6ggKKhQX";
    public static $EMAIL_FROM = "VlastnyKalendar.sk";

    public static $EMAIL_ADMIN_NOTIFICATION = "objednavky@vlastny-kalendar.sk";

    // Invoice & Remax details
    public static $ALDO_NAME = "Alica Dórová - ALDO";
    public static $ALDO_CONTACT_PERSON_NAME = "Alica Dórová";
    public static $ALDO_ADDRESS = "Ursínyho 1";
    public static $ALDO_CITY = "Bratislava";
    public static $ALDO_ZIP = "83102";
    public static $ALDO_COUNTRY = "Slovenská republika";
    public static $ALDO_COUNTRY_CODE = "SK";
    public static $ALDO_ICO = "34463500";
    public static $ALDO_DIC = "1020189115";
    public static $ALDO_ICDPH = "SK 1020189115";
    public static $ALDO_DETAIL = "ev.č.: žo-96/03270/001, reg. č. 2530/96";
    public static $ALDO_STORE = "Ursínyho 1, 831 02 Bratislava";
    public static $ALDO_PHONE = "+421905471812";
    public static $ALDO_EMAIL = "aldo@aldodesign.sk";
    public static $ALDO_WEB = "vlastnykalendar.sk";

    public static $ALDO_BANK_ACCOUNT = "0011535932/0900";
    public static $ALDO_BANK_NAME = "Slovenská sporiteľňa, a.s.";
    public static $ALDO_BANK_IBAN = "SK2109000000000011535932";
    public static $ALDO_BANK_SWIFT = "GIBASKBX";    
    public static $ALDO_BANK_KS = "0308";

    public static $VAT = 20;

    // Remax
    public static $REMAX_ADD_ORDER = "https://dispatch.remax.sk/dispatch/soap/pridaj_zakazku3.php?wsdl";
    public static $REMAX_USER = "ALICDO01P";
    public static $REMAX_PASSWORD = "perfaxat";

    // SMS
    public static $SMS_USER = "ALDO";
    public static $SMS_PASSWORD = "dhj65LPO45Jtf";
    public static $SMS_API_URL_VALIDATE = "https://api.bsms.viamobile.sk/json/validate";
    public static $SMS_API_URL = "https://api.bsms.viamobile.sk/json/send";
    public static $SMS_TEXT = "Dobry den, Vas Kalendar je hotovy. Info sme poslali na Vas e-mail. V pripade potreby volajte 0905471812. Platba mozna len v hotovosti.";

	// GP WEBPAY
	public static $GP_WEBPAY_TESTING = false;
	public static $GP_WEBPAY_MERCHANT_NUMBER = '7322227414';
	
	// GP PROD
	public static $GP_WEBPAY_PRIVATE_KEY_FILE = '/components/com_calendar/certs/prod/gpwebpay-pvk.key';
	public static $GP_WEBPAY_PRIVATE_KEY_PASS = 'bq2zQF3dp9c_249mERAuuBK';
	public static $GP_WEBPAY_PUBLIC_KEY_FILE = '/components/com_calendar/certs/prod/gpe.signing_prod.pem';

	// GP TEST
	// public static $GP_WEBPAY_PRIVATE_KEY_FILE = '/components/com_calendar/certs/test/gpwebpay-pvk.key';
	// public static $GP_WEBPAY_PRIVATE_KEY_PASS = 'rAqUVFHncia6wnckjKF';
	// public static $GP_WEBPAY_PUBLIC_KEY_FILE = '/components/com_calendar/certs/test/gpe.signing_test.pem';

	// Karta pre testovacie platby:
	// Číslo karty: 4056070000000008
	// Platnosť karty: 12/2023
	// CVC2: 992
}

defined('_JEXEC') or die;

// coupon mode, false - off, true - on
define('CAL_COUPON_MODE', false);

// users allowed to access administration view
define('CAL_ADMIN_USERS', serialize( array( '152', '153' ) ) );

// Kalendare ktore maju 300 DPI [default 450]
define('CAL_300_DPI_TYPES', serialize( array( 'g','h','j','n','i','m' ) ) );

// calendar type => calendar positions; used in <CalendarHelper::getPicturePositions>
define('CAL_LAYOUT_POSITIONS', serialize(
    array(
        'a' => 1,
        'b' => 2,
        'c' => 1,
        'd' => 4,
        'e' => 4,
        'f' => 1,
        'g' => 4,
        'h' => 4,
        'i' => 1,
        'j' => 1,
        'm' => 1,
        'n' => 1,
        'k' => 2,
        'l' => 1,
        'o' => 3,
        'p' => 6,
        'q' => 3,
        'w' => 4
    )
));

define('CAL_COVER_LAYOUT_POSITIONS', serialize(
    array(
        'a' => 1,
        'b' => 2,
        'c' => 1,
        'd' => 1,
        'e' => 1,
        'f' => 1,
        'g' => 1,
        'h' => 1,
        'i' => 1,
        'j' => 1,
        'm' => 1,
        'n' => 1,
        'k' => 1,
        'l' => 1,
        'o' => 1,
        'p' => 1,
        'q' => 1,
        'r' => 1,
        's' => 1,
        't' => 1,
        'u' => 1,
        'v' => 1,
        'w' => 1,
    )
));

// Kalendare ktore maju fixny zaciatocny mesiac
define('CAL_START_MONTH_FIXED', serialize( array( 'o' => 'january', 'p' => 'january', 'q' => 'january', 'w' => 'january' ) ) );

// Kalendare ktore iba cover
define('CAL_WITH_COVER_ONLY', serialize( array( 't','s','r' ) ) );

//calendar prices
$calPrices = array( 
	'a'=>9.90, 'b'=>9.90, 'c'=>8.90, 'd'=>9.90, 'e'=>9.90, 'f'=>13.90, 'g'=>14.90, 'h'=>14.90, 'i'=>8.90, 'j'=>8.90, 
	'm'=>14.90, 'n'=>13.90, 'k'=>9.90, 'l'=>9.90, 'o'=>13.90, 'p'=>13.90, 'q'=>13.90, 'r' => 9.90, 's' => 6.90, 't' => 10.90,
    'u' => 10.90, 'v' => 10.90, 'w' => 17.50
);
define('CAL_PRICES', serialize( $calPrices ) );

$calCoverPrices = array(
	'a'=>0 , 'b'=>0 , 'c'=>0.45, 'd'=>0.45, 'e'=>0.45, 'f'=>0.65, 'g'=>0.65, 'h'=>0.65, 'i'=>0, 'j'=>0, 
	'm'=>0.65, 'n'=>0.65, 'k'=>0, 'l'=>0, 'o'=>0, 'p'=>0, 'q'=>0, 'r' => 0, 's' => 0, 't' => 0,
    'u' => 0, 'v' => 0, 'w' => 0 );

define('CAL_COVER_PRICES', serialize( $calCoverPrices ) );

// Pridanie ubratie poctu pozicii pre jednotlive typy
$cal_layout_positions_count = array(
	'o' => array( 
		'totalPositionsWithoutCover' => 27,
        'coverPositions'             => 1,
        'bg' => array(
            'january'  => array( 'positions' => 3, 'bgSize' => 998),
            'june'     => array( 'positions' => 3, 'bgSize' => 998),
            'december' => array( 'positions' => 3, 'bgSize' => 998),
        )
    ),
		
	'p' => array( 
		'totalPositionsWithoutCover' => 54,
        'coverPositions'             => 1,
        'bg' => array(
            'january' => array( 'positions' => 6, 'bgSize' => 998),
            'may'     => array( 'positions' => 6, 'bgSize' => 998),
            'october' => array( 'positions' => 6, 'bgSize' => 998),
        )
    ),
		
	'q' => array( 
        'totalPositionsWithoutCover' => 27,
        'coverPositions'             => 1,
        'bg' => array(
            'january' => array( 'positions' => 3, 'bgSize' => 998),
            'may'     => array( 'positions' => 3, 'bgSize' => 998),
            'october' => array( 'positions' => 3, 'bgSize' => 998),
        )
    ),

    'k' => array(
        'totalPositionsWithoutCover' => 24,
        'coverPositions'             => 1
    ),

    'w' => array(
        'totalPositionsWithoutCover' => 53,
        'coverPositions'             => 1
    )
);

define('CAL_LAYOUT_MOD_POSITIONS_COUNT', serialize($cal_layout_positions_count));

// calendar positions per page - ZALOZENE POTREBNE PRE KALENDARE KTORE MAJU NA MESIAC VIAC LISTOV
$cal_layout_positions_per_page = array(
	'q' => 1,
	'c' => 1,
	'd' => 4,
	'e' => 4,
	'f' => 1,
	'g' => 4,
	'h' => 4,
	'j' => 1,
	'n' => 1,
	'k' => 2,
	'l' => 1,
	'i' => 1,
	'm' => 1,
	'o' => 1,
	'p' => 2,
	'a' => 1,
	't' => 1,
	's' => 1,
	'r' => 1,
    'u' => 1,
    'v' => 1,
	'b' => 2,
    'w' => 1
);

define('CAL_LAYOUT_POSITIONS_PER_PAGE', serialize($cal_layout_positions_per_page));

// Kalendare ktore su pri generovani pdf rozdelovane na viac stran
$cal_with_splitted_pages = array('o' => true, 'p' => true, 'q' => true);

define('CAL_WITH_SPLITTED_PAGES', serialize($cal_with_splitted_pages));

// Preview - rozmer vyzeru pre danu poziciu daneho kalendara -> tieto rozmery sa potom pretransformuju do press rozmerov
$calendar_sizes = array(
	'a' => array( 
		0 => array('width' => 220, 'height' => 428 ) ),
	
	'b' => array( 
		0 => array('width' => 214, 'height' => 215 ),
		1 => array('width' => 214, 'height' => 215 ) ),
		
	'c' => array( 
		0 => array('width' => 566, 'height' => 661 ) ),
		
	'd' => array( 
		0 => array('width' => 504, 'height' => 356 ), 
		1 => array('width' => 149, 'height' => 192 ),
		2 => array('width' => 147, 'height' => 192 ),
		3 => array('width' => 149, 'height' => 192 ) ),
	
	'e' => array( 
		0 => array('width' => 149, 'height' => 192 ), 
		1 => array('width' => 149, 'height' => 192 ),
		2 => array('width' => 148, 'height' => 192 ),
		3 => array('width' => 503, 'height' => 352 ) ),
		
	'f' => array( 
		0 => array('width' => 566, 'height' => 669 ) ),
	
	'g' => array( 
		0 => array('width' => 509, 'height' => 359 ), 
		1 => array('width' => 151, 'height' => 192 ),
		2 => array('width' => 148, 'height' => 192 ),
		3 => array('width' => 150, 'height' => 192 ) ),
		
	'h' => array( 
		0 => array('width' => 149, 'height' => 192 ), 
		1 => array('width' => 150, 'height' => 192 ),
		2 => array('width' => 150, 'height' => 192 ),
		3 => array('width' => 508, 'height' => 359 ) ),
		
	'j' => array( 
		0 => array('width' => 349, 'height' => 430 ) ),
		
	'n' => array( 
		0 => array('width' => 351, 'height' => 432 ) ),
		
	'i' => array( 
		0 => array('width' => 710, 'height' => 364 ) ),
	
	'm' => array( 
		0 => array('width' => 710, 'height' => 360 ) ),
	
	'k' => array( 
		0 => array('width' => 215, 'height' => 238 ), 
		1 => array('width' => 215, 'height' => 238 ) ),
		
	'l' => array( 
		0 => array('width' => 429, 'height' => 269 ) ),

	'o' => array(
		0 => array('width' => 179, 'height' => 231 ),
		1 => array('width' => 179, 'height' => 231 ),
		2 => array('width' => 179, 'height' => 231 ) ),
		
	'p' => array( 
		0 => array('width' => 182, 'height' => 238 ), 
		1 => array('width' => 182, 'height' => 238 ),
		2 => array('width' => 182, 'height' => 238 ),
		3 => array('width' => 182, 'height' => 238 ),
		4 => array('width' => 182, 'height' => 238 ),
		5 => array('width' => 182, 'height' => 238 ) ),
		
	'q' => array( 
		0 => array('width' => 364, 'height' => 238 ), 
		1 => array('width' => 364, 'height' => 238 ),
		2 => array('width' => 364, 'height' => 238 ) ),

    'r' => array( 0 => array('width' => 550, 'height' => 400) ),
    't' => array( 0 => array('width' => 550, 'height' => 400) ),
    's' => array( 0 => array('width' => 550, 'height' => 400) ),
    'u' => array( 0 => array('width' => 550, 'height' => 400) ),
    'v' => array( 0 => array('width' => 550, 'height' => 400) ),

    'w' => array(
        0 => array('width' => 364, 'height' => 238 ),
        1 => array('width' => 364, 'height' => 238 ),
        2 => array('width' => 364, 'height' => 238 ) ),
		
);

define('CAL_SIZES', serialize($calendar_sizes));

// Vyrez cover
$calendar_cover_sizes = array(
	'a' => array( 0 => array('width' => 220, 'height' => 428) ),
	'b' => array( 0 => array('width' => 220, 'height' => 205), 1 => array('width' => 220, 'height' => 205) ),
	'c' => array( 0 => array('width' => 566, 'height' => 661) ),
	'd' => array( 0 => array('width' => 503, 'height' => 389) ),
	'e' => array( 0 => array('width' => 503, 'height' => 388) ),
	'f' => array( 0 => array('width' => 566, 'height' => 669) ),
	'g' => array( 0 => array('width' => 508, 'height' => 447) ),
	'h' => array( 0 => array('width' => 508, 'height' => 447) ),
	'i' => array( 0 => array('width' => 710, 'height' => 364) ),
	'j' => array( 0 => array('width' => 517, 'height' => 388) ),
	'k' => array( 0 => array('width' => 316, 'height' => 238) ),
	'l' => array( 0 => array('width' => 429, 'height' => 269) ),
	'm' => array( 0 => array('width' => 710, 'height' => 366) ),
	'n' => array( 0 => array('width' => 660, 'height' => 393) ),
	'o' => array( 0 => array('width' => 377, 'height' => 259) ),
	'p' => array( 0 => array('width' => 378, 'height' => 257) ),
	'q' => array( 0 => array('width' => 436, 'height' => 256) ),
	'r' => array( 0 => array('width' => 550, 'height' => 400) ),
	't' => array( 0 => array('width' => 550, 'height' => 400) ),
	's' => array( 0 => array('width' => 550, 'height' => 400) ),
    'u' => array( 0 => array('width' => 550, 'height' => 400) ),
    'v' => array( 0 => array('width' => 550, 'height' => 400) ),
    'w' => array( 0 => array('width' => 436, 'height' => 256) ),
);

define('CAL_COVER_SIZES', serialize($calendar_cover_sizes));

// velkost vyrezu pre obrazok; tlacove pdf
$calendar_pdf_sizes = array(
	'a' => array(
		0 => array('width' => 1531, 'height' => 2988 )
    ),

	'b' => array(
		0 => array('width' => 1531, 'height' => 1496 ),
		1 => array('width' => 1531, 'height' => 1496 )
    ),
	
	'c' => array( 
		0 => array('width' => 2516, 'height' => 2954 )
    ),
		
	'e' => array( 
		0 => array('width' => 654, 'height' => 841 ),
		1 => array('width' => 654, 'height' => 841 ),
		2 => array('width' => 654, 'height' => 841 ),
		3 => array('width' => 2208, 'height' => 1565 ),
    ),

	'd' => array( 
		0 => array('width' => 2208, 'height' => 1565 ),
		1 => array('width' => 654, 'height' => 841 ),
		2 => array('width' => 654, 'height' => 841 ),
		3 => array('width' => 654, 'height' => 841 )
    ),
		
	'f' => array( 
		0 => array('width' => 3547, 'height' => 4171 )
    ),
		
	'g' => array( 
		0 => array('width' => 3160, 'height' => 2235 ),
		1 => array('width' => 937, 'height' => 1196 ),
		2 => array('width' => 937, 'height' => 1196 ),
		3 => array('width' => 937, 'height' => 1196 )
    ),

	'h' => array( 
		0 => array('width' => 934, 'height' => 1200 ),
		1 => array('width' => 931, 'height' => 1200 ),
		2 => array('width' => 934, 'height' => 1200 ),
		3 => array('width' => 3158, 'height' => 2233 )
    ),
		
	'j' => array( 
		0 => array('width' => 1807, 'height' => 2219 )
    ),
		
	'n' => array( 
		0 => array('width' => 2584, 'height' => 3169 )
    ),
		
	'i' => array( 
		0 => array('width' => 3696, 'height' => 1897 )
    ),
	
	'm' => array( 
		0 => array('width' => 5232, 'height' => 2703 )
    ),
		
	'k' => array( 
		0 => array('width' => 990, 'height' => 1097 ),
		1 => array('width' => 990, 'height' => 1097 )
    ),

	'l' => array( 
		0 => array('width' => 1980, 'height' => 1250 )
    ),
		
	'o' => array( 
		0 => array('width' => 947, 'height' => 1244 ),
		1 => array('width' => 947, 'height' => 1244 ),
		2 => array('width' => 947, 'height' => 1244 ),
    ),
		
	'p' => array( 
		0 => array('width' => 949, 'height' => 1241 ),
		1 => array('width' => 949, 'height' => 1241 ),
		2 => array('width' => 949, 'height' => 1241 ),
		3 => array('width' => 949, 'height' => 1241 ),
		4 => array('width' => 949, 'height' => 1241 ),
		5 => array('width' => 949, 'height' => 1241 ),
    ),
		
	'q' => array( 
		0 => array('width' => 1894, 'height' => 1240 ),
		1 => array('width' => 1894, 'height' => 1245 ),
		2 => array('width' => 1894, 'height' => 1241 ),
    ),

    'r' => array( 0 => array('width' => 3732, 'height' => 2669) ),
    's' => array( 0 => array('width' => 3732, 'height' => 2669) ),
    't' => array( 0 => array('width' => 3732, 'height' => 2669) ),
    'u' => array( 0 => array('width' => 3732, 'height' => 2669) ),
    'v' => array( 0 => array('width' => 3732, 'height' => 2669) ),

    'w' => array(
        0 => array('width' => 1894, 'height' => 1240 ),
        1 => array('width' => 1894, 'height' => 1245 ),
        2 => array('width' => 1894, 'height' => 1241 ),
    ),
);

define('CAL_PDF_SIZES', serialize($calendar_pdf_sizes));

// Velkost vyrezu // COVER kalendara
$calendar_pdf_cover_sizes = array(
	'a' => array( 0 => array('width' => 1531, 'height' => 2988) ),
	'b' => array( 0 => array('width' => 1531, 'height' => 1496), 1 => array('width' => 1531, 'height' => 1496) ),
	'c' => array( 0 => array('width' => 2516, 'height' => 2954) ),
	'd' => array( 0 => array('width' => 2209, 'height' => 1708) ),
	'e' => array( 0 => array('width' => 2209, 'height' => 1708) ),
	'f' => array( 0 => array('width' => 3547, 'height' => 4171) ),
	'g' => array( 0 => array('width' => 3165, 'height' => 2775) ),
	'h' => array( 0 => array('width' => 3161, 'height' => 2777) ),
	'i' => array( 0 => array('width' => 3696, 'height' => 2005) ),
	'j' => array( 0 => array('width' => 2669, 'height' => 2006) ),
	'k' => array( 0 => array('width' => 1562, 'height' => 1180) ),
	'l' => array( 0 => array('width' => 2229, 'height' => 1391) ),
	'm' => array( 0 => array('width' => 5232, 'height' => 2703) ),
	'n' => array( 0 => array('width' => 4848, 'height' => 2889) ),
	'o' => array( 0 => array('width' => 1956, 'height' => 1382) ),
	'p' => array( 0 => array('width' => 1958, 'height' => 1339) ),
	'q' => array( 0 => array('width' => 2256, 'height' => 1343) ),
	'r' => array( 0 => array('width' => 3732, 'height' => 2669) ),
	's' => array( 0 => array('width' => 3732, 'height' => 2669) ),
	't' => array( 0 => array('width' => 3732, 'height' => 2669) ),
    'u' => array( 0 => array('width' => 3732, 'height' => 2669) ),
    'v' => array( 0 => array('width' => 3732, 'height' => 2669) ),
    'w' => array( 0 => array('width' => 3732, 'height' => 1343) )
);

define('CAL_PDF_COVER_SIZES', serialize($calendar_pdf_cover_sizes));

// Rozmer tlacoveho nahladku
$calendar_total_size = array( 
	'a' => array('width' => 1902, 'height' => 5623 , 'wpoints' => 456.5, 'hpoints' => 1349.4),
	'b' => array('width' => 1902, 'height' => 5623 , 'wpoints' => 456.5, 'hpoints' => 1349.4),
	'c' => array('width' => 2552, 'height' => 3579 , 'wpoints' => 612.5, 'hpoints' => 859),
	'd' => array('width' => 2552, 'height' => 3579 , 'wpoints' => 612.5, 'hpoints' => 859),
	'e' => array('width' => 2552, 'height' => 3579 , 'wpoints' => 612.5, 'hpoints' => 859),
	'f' => array('width' => 3579, 'height' => 5032 , 'wpoints' => 859,   'hpoints' => 1207.7),
	'g' => array('width' => 3579, 'height' => 5032 , 'wpoints' => 859,   'hpoints' => 1207.7),
	'h' => array('width' => 3579, 'height' => 5032 , 'wpoints' => 859,   'hpoints' => 1207.7),
	'j' => array('width' => 3733, 'height' => 2670 , 'wpoints' => 896.2, 'hpoints' => 641),
	'n' => array('width' => 5268, 'height' => 3733 , 'wpoints' => 1264.6,'hpoints' => 896.5),
	'i' => array('width' => 3734, 'height' => 2670 , 'wpoints' => 896.2, 'hpoints' => 641),
	'm' => array('width' => 5268, 'height' => 3733 , 'wpoints' => 1264.6,'hpoints' => 896.5),
	'k' => array('width' => 3733, 'height' => 1548 , 'wpoints' => 896.2, 'hpoints' => 365),
	'l' => array('width' => 3733, 'height' => 1548 , 'wpoints' => 896.2, 'hpoints' => 365),
	'o' => array('width' => 3734, 'height' => 1784 , 'wpoints' => 896.2, 'hpoints' => 420.2),
	'p' => array('width' => 3734, 'height' => 1784 , 'wpoints' => 896.2, 'hpoints' => 420.2),
	'q' => array('width' => 3734, 'height' => 1784 , 'wpoints' => 896.2, 'hpoints' => 420.2),
	'r' => array('width' => 3732, 'height' => 2669 , 'wpoints' => 895.7, 'hpoints' => 640.6),
	's' => array('width' => 3732, 'height' => 2669 , 'wpoints' => 895.7, 'hpoints' => 640.6),
	't' => array('width' => 3732, 'height' => 2669 , 'wpoints' => 895.7, 'hpoints' => 640.6),
    'u' => array('width' => 3732, 'height' => 2669 , 'wpoints' => 895.7, 'hpoints' => 640.6),
    'v' => array('width' => 3732, 'height' => 2669 , 'wpoints' => 895.7, 'hpoints' => 640.6),
    'w' => array('width' => 3733, 'height' => 1784 , 'wpoints' => 896.9, 'hpoints' => 421.5)
);

define('CAL_PDF_SIZES_TOTAL', serialize($calendar_total_size));

// Minimalne rozmery obrazku pre kalendare pri vytvarani
$minimalDimensions = array(
    'a' => array(
		'cover' => array( 1 => array('width' => 760, 'height' => 1500 )),
		'page' => array(
            1 => array('width' => 760, 'height' => 1500 ),
		)
	),
    'b' => array(
		'cover' => array( 1 => array('width' => 800, 'height' => 800 ), 2 => array('width' => 800, 'height' => 800 )),
		'page' => array(
            1 => array('width' => 800, 'height' => 800 ),
            2 => array('width' => 800, 'height' => 800 ),
		)
	),
    'c' => array(
		'cover' => array( 1 => array('width' => 500, 'height' => 1250 )),
		'page' => array(
            1 => array('width' => 500, 'height' => 1250 ),
		)
	),
    'd' => array(
		'cover' => array( 1 => array('width' => 1100, 'height' => 800 )),
		'page' => array(
            1 => array('width' => 1100, 'height' => 800),
            2 => array('width' => 600, 'height' => 600),
            3 => array('width' => 600, 'height' => 600),
            4 => array('width' => 600, 'height' => 600),
		)
	),
	'e' => array(
		'cover' => array( 1 => array('width' => 1100, 'height' => 800 )),
		'page' => array(
            1 => array('width' => 600, 'height' => 600),
            2 => array('width' => 600, 'height' => 600),
            3 => array('width' => 600, 'height' => 600),
            4 => array('width' => 1100, 'height' => 800),
		)
	),
    'f' => array(
		'cover' => array( 1 => array('width' => 1800, 'height' => 2100 )),
		'page' => array(
            1 => array('width' => 1800, 'height' => 2100 ),
		)
	),
    'g' => array(
		'cover' => array( 1 => array('width' => 2100, 'height' => 1500 )),
		'page' => array(
            1 => array('width' => 2100, 'height' => 1500),
            2 => array('width' => 900, 'height' => 900),
            3 => array('width' => 900, 'height' => 900),
            4 => array('width' => 900, 'height' => 900),
		)
	),
    'h' => array(
		'cover' => array( 1 => array('width' => 2100, 'height' => 1500 )),
		'page' => array(
            1 => array('width' => 900, 'height' => 900),
            2 => array('width' => 900, 'height' => 900),
            3 => array('width' => 900, 'height' => 900),
            4 => array('width' => 2100, 'height' => 1500),
		)
	),
    'i' => array(
		'cover' => array( 1 => array('width' => 1500, 'height' => 1200 )),
		'page' => array(
            1 => array('width' => 1500, 'height' => 1200 ),
		)
	),
    'j' => array(
		'cover' => array( 1 => array('width' => 1100, 'height' => 1100 )),
		'page' => array(
            1 => array('width' => 1100, 'height' => 1100 ),
		)
	),
    'k' => array(
		'cover' => array( 1 => array('width' => 700, 'height' => 700 )),
		'page' => array(
            1 => array('width' => 700, 'height' => 700 ),
            2 => array('width' => 700, 'height' => 700 ),
		)
	),
    'l' => array(
		'cover' => array( 1 => array('width' => 1100, 'height' => 700 )),
		'page' => array(
            1 => array('width' => 1100, 'height' => 700 ),
		)
	),
    'm' => array(
		'cover' => array( 1 => array('width' => 2400, 'height' => 1600 )),
		'page' => array(
            1 => array('width' => 2400, 'height' => 1600 ),
		)
	),
    'n' => array(
		'cover' => array( 1 => array('width' => 1600, 'height' => 1600 )),
		'page' => array(
            1 => array('width' => 1600, 'height' => 1600 ),
		)
	),
    'o' => array(
		'cover' => array( 1 => array('width' => 700, 'height' => 700 )),
		'page' => array(
            1 => array('width' => 700, 'height' => 700 ),
            2 => array('width' => 700, 'height' => 700 ),
            3 => array('width' => 700, 'height' => 700 ),
		)
	),
    'p' => array(
		'cover' => array( 1 => array('width' => 700, 'height' => 700 )),
		'page' => array(
            1 => array('width' => 700, 'height' => 700 ),
            2 => array('width' => 700, 'height' => 700 ),
            3 => array('width' => 700, 'height' => 700 ),
		)
	),
    'q' => array(
		'cover' => array( 1 => array('width' => 960, 'height' => 640 )),
		'page' => array(
            1 => array('width' => 960, 'height' => 640 ),
            2 => array('width' => 960, 'height' => 640 ),
            3 => array('width' => 960, 'height' => 640 ),
		)
	),
);

define('CAL_MIN_DIMENSIONS', serialize($minimalDimensions));

//root
define('CAL_ROOT_WEB', 'https://vlastnykalendar.sk/');

define('CAL_ROOT_SERVER', '/home/html/vlastnykalendar.sk/');

// component folder
define('CAL_COMPONENT_WEB', CAL_ROOT_WEB . 'components/com_calendar/');

define('CAL_COMPONENT_SERVER', CAL_ROOT_SERVER . 'components/com_calendar/');

// upload folder
define('CAL_UPLOAD_WEB', CAL_ROOT_WEB . 'calendar/');

define('CAL_UPLOAD_SERVER', CAL_ROOT_SERVER . 'calendar/');


// Aditional price for calendars over 1 copy
define("CAL_PAYMENT_PRICE_PER_ITEM", 0.3); 


// MOD PRICES START 09-2015

// Dopravne ceny
define("CAL_SHIPPING_PERSONAL", 0); // Osobne
define("CAL_SHIPPING_DEPO", 1.5); // Depo
define("CAL_SHIPPING_COURIER", 3.8); // Kuriérom
define("CAL_SHIPPING_POST_SK", 4.5); // Slovenskou poštou
define("CAL_SHIPPING_POST_CZ", 9); // CZ poštou
define("CAL_SHIPPING_POST_EU", 12); // EU poštou

// Priplatky za sposob platby
define("CAL_PAYMENT_CASH", 0);
define("CAL_PAYMENT_ON_DELIVERY", 1);
define("CAL_PAYMENT_CARD", 0);


// EOF MOD PRICES

// mail settings
define("CAL_MAIL_MESSAGE", '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Vaša objednávka bola prijatá</title>
<style type="text/css">
body {
	font-family: Arial, Helvetica, sans-serif;
}
p {
	font-size: 14px;
}
</style>
</head>
<body style="background:#e9e9e9">
<div style="background:white; border:1px solid #777; width:600px; margin: 0 auto; padding: 15px;">
<div class="top">
	<div style="margin: 0 auto; width: 240px;">
	<a href="https://www.vlastnykalendar.sk"><img alt="Vlastný kalednár - domov" src="https://www.vlastnykalendar.sk/templates/pt-fotokniha/images/logo.png"></a>
  </div>
</div>
<div class="title">
<h1 style="font-size:22px;"><center>Ďakujeme. Vaša objednávka bola úspešne odoslaná.</center></h1>
</div>
<p>Dobrý deň,<br />v prílohe Vám zasielame detail objednávky. O stave Vašej objednávky a jej dokončení, budete informovaný emailom.</p>
<p>
1. Ak ste si zvolili <strong>platbu dobierkou</strong>, bude po vyhotovení vaša objednávka zaslaná poštou. Sumu uhradíte v hotovosti pri preberaní balíka na pošte.
</p>
<p>
2. Ak ste si vybrali <strong>platbu prevodom</strong>, bude vaša objednávka poslaná poštou/kuriérom (ako ste si zvolili v objednávke) až po uhradení sumy na účet, ktorý vám bol zaslaný v predchádzajúcom maily vo vašej objednávke.
</p>
<p>
3. Ak ste si zvolili <strong>platbu kuriérovi</strong>, bude vaša objednávka po vyhotovení zaslaná kuriérskou službou. Sumu uhradíte v hotovosti kuriérovi pri preberaní zásielky.
</p>
<p>
4. Ak ste si zvolili <strong>výdajné miesto</strong>, čakajte na potvrdzovaciu SMS, kde bude uvedené, že je zásielka na výdajnom mieste, pripravená k odberu.
</p>
<p>V prípade OSOBNÉHO ODBERU (platba len v hotovosti) si môžete zákazku vyzdvihnúť v pracovných dňoch od 8:00 do 16:30 na Ursínyho 1, 831 02 Bratislava, alebo volajte 0905 650 811.</p>
<p>V ČASE OD 10.12. DO 22.12. SI MÔŽETE KALENDÁRE VYZDVIHNÚŤ AŽ DO 19.00</p>
<hr />
<p>Ďakujeme za využitie našich služieb - Vlastný kalendár</p>
</div>
</body>
</html>');

define("CAL_MAIL_NOTICE", 1);

define("CAL_MAIL_SUBJECT", "Objednavka");

define("CAL_MAIL_ADDRESS", "objednavky@vlastnykalendar.sk");


define("CAL_ORDER_SUCCESS_TRANSPORT_METHOD_DESCRIPTION_POST", "Zvolili ste si platbu dobierkou. Vaša objednávka bude po vyhotovení zaslaná poštou. Sumu uhradíte v hotovosti pri preberaní balíka na pošte.");
define("CAL_ORDER_SUCCESS_TRANSPORT_METHOD_DESCRIPTION_TRANSFER", "Zvolili ste si platbu prevodom. Vaša objednávka bude poslaná poštou / kuriérom až po uhradení sumy na účet. Číslo účtu nájdete vo faktúre.");
define("CAL_ORDER_SUCCESS_TRANSPORT_METHOD_DESCRIPTION_COURIER", "Zvolili ste si platbu kuriérovi. Vaša objednávka bude po vyhotovení zaslaná kuriérskou službou. Sumu uhradíte v hotovosti kuriérovi pri preberaní zásielky.");
define("CAL_ORDER_SUCCESS_TRANSPORT_METHOD_DESCRIPTION_CASH", "Zvolili ste si osobný odber (platba len v hotovosti). Objednávku si môžete vyzdvihnúť v pracovných dňoch od 8:00 do 16:30 na Ursínyho 1, 831 02 Bratislava, alebo volajte 0905 650 811. V ČASE OD 10.12. DO 22.12. SI MÔŽETE KALENDÁRE VYZDVIHNÚŤ AŽ DO 19.00");
define("CAL_ORDER_SUCCESS_THANKS", "Ďakujeme za využitie našich služieb - Vlastný kalendár");
define("CAL_ORDER_SUCCESS_TRANSPORT_METHOD_DESCRIPTION_DEPO", "Zvolili ste si osobný odber s vyzdvidnutím na odbernom mieste (platba pri prevzatí). Po doručení zásielky na odberné miesto Vás budeme informovať prostredníctvom SMS a emailu.");
define("CAL_ORDER_SUCCESS_TRANSPORT_METHOD_DESCRIPTION_GP_WEBPAY", "Zvolili ste si platbu kartou online.");

define("CAL_MAIL_MESSAGE_DONE", '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Vaša objednávka je vyhotovená</title>
<style type="text/css">
body {
	font-family: Arial, Helvetica, sans-serif;
}
p {
	font-size: 14px;
}
</style>
</head>
<body style="background:#e9e9e9">
<div style="background:white; border:1px solid #777; width:600px; margin: 0 auto; padding: 15px;">
<div class="top">
	<div style="margin: 0 auto; font-size: 22px; text-align: center;">
	<a href="https://www.vlastnykalendar.sk" style="text-decoration: none; color: #000">Vlastný kalendár</a>
  </div>
</div>
<div class="title">
<h1 style="font-size:18px;"><center>Vaša objednávka je vyhotovená</center></h1>
</div>
<p>
1. Ak ste si zvolili <strong>platbu dobierkou</strong>, bude v najbližšom čase vaša objednávka zaslaná poštou. Sumu uhradíte v hotovosti pri preberaní balíka na pošte.
</p>
<p>
2. Ak ste si vybrali <strong>platbu prevodom</strong>, bude vaša objednávka poslaná poštou/kuriérom (ako ste si zvolili v objednávke) až po uhradení sumy na účet, ktorý vám bol zaslaný v predchádzajúcom maily vo vašej objednávke.
</p>
<p>
3. Ak ste si zvolili <strong>platbu kuriérovi</strong>, bude vaša objednávka v najbližšom čase zaslaná kuriérskou službou. Sumu uhradíte v hotovosti kuriérovi pri preberaní zásielky.
</p>
<p>
4. Ak ste si zvolili <strong>výdajné miesto</strong>, čakajte na potvrdzovaciu SMS, kde bude uvedené, že je zásielka na výdajnom mieste, pripravená k odberu.
</p>
<p>V prípade OSOBNÉHO ODBERU (platba len v hotovosti) si môžete zákazku vyzdvihnúť v pracovných dňoch od 8:00 do 16:30 na Ursínyho 1, 831 02 Bratislava, alebo volajte 0905 650 811.</p>
<hr />
<p>Ďakujeme za využitie našich služieb - Vlastný kalendár</p>
</div>
</body>
</html>');

define("CAL_MAIL_SUBJECT_DONE", "Vlastný kalendár - vaša objednávka bola vybavená");

define("CAL_PRICELIST_TEXT", "Každý kalendár môže obsahovať aj titulnú stranu. Pridáte ju na začiatku pri zadaní parametrov kalendára. Kalendár nemusí začínať januárom a končiť decembrom, ale môže začínať napríklad od apríla 2022 do marca 2023 (tzv. aktuálny kalendár, napríklad pri jubileu, svadbe, narodení dieťaťa...). <strong>Všetky kalendáre majú kalendária s menami.</strong> <strong><span style='color:red;'>Šablóny Kalendárov č. 15 až 23 sa dajú vyhotoviť len za kalendárny rok (január až december)</span></strong>.");