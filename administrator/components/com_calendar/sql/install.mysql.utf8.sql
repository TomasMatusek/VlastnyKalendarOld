CREATE TABLE IF NOT EXISTS `cal_calendar` (
	`cal_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(11) NOT NULL,
	`type` varchar(10) NOT NULL,
	`start_month` varchar(10) NOT NULL,
	`language` varchar(10) NOT NULL,
	`status` tinyint(4) NOT NULL,
	`order_sent` tinyint(1) NOT NULL,
	`front_page` tinyint(1) NOT NULL,
	`create_time` int(10) unsigned NOT NULL,
	PRIMARY KEY (`cal_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cal_order` (
	`order_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`cal_id` int(11) NOT NULL,
	`user_id` int(11) NOT NULL,
	`billing_name` varchar(25) NOT NULL,
	`billing_address` varchar(25) NOT NULL,
	`billing_city` varchar(25) NOT NULL,
	`billing_zip` varchar(10) NOT NULL,
	`billing_mail` varchar(100) NOT NULL,
	`billing_phone` varchar(15) NOT NULL,
	`shipping_name` varchar(25) NOT NULL,
	`shipping_address` varchar(25) NOT NULL,
	`shipping_city` varchar(25) NOT NULL,
	`shipping_zip` varchar(10) NOT NULL,
	`shipping_phone` varchar(15) NOT NULL,
	`quantity` tinyint(3) unsigned NOT NULL,
	`final_price` double NOT NULL,
	`order_sent` int(10) unsigned NOT NULL,
	PRIMARY KEY (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cal_photo` (
	`photo_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`cal_id` int(11) NOT NULL,
	`image` varchar(255) NOT NULL,
	`position` tinyint(4) NOT NULL,
	`left` double NOT NULL,
	`top` double NOT NULL,
	`width` double NOT NULL,
	`height` double NOT NULL,
	`month` varchar(10) NOT NULL,
	PRIMARY KEY (`photo_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;