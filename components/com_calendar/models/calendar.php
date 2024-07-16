<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

@include_once(JPATH_BASE . 'configuration.php');

class CalendarModelCalendar extends JModelLegacy
{
	public function __construct()
	{
		parent::__construct();
		
		$config = new JConfig();

		$this->dbh = new PDO('mysql:host=' . $config->host . ';dbname=' . $config->db, $config->user, $config->password);
		$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->dbh->exec("SET collation_connection = utf8_bin; SET NAMES utf8;");
	}

	private function initializeTables()
	{
		$sql = "CREATE TABLE cal_calendar (
                  cal_id int(10) unsigned NOT NULL AUTO_INCREMENT,
                  user_id int(11) NOT NULL,
                  type varchar(10) NOT NULL,
                  quantity int(11) NOT NULL DEFAULT 1,
                  start_month varchar(10) NOT NULL,
                  start_year int(11) NOT NULL,
                  language varchar(10) NOT NULL,
                  status tinyint(4) NOT NULL,
                  order_sent tinyint(1) NOT NULL,
                  front_page tinyint(1) NOT NULL,
                  front_page_text varchar(56) NOT NULL,
                  create_time int(20) unsigned NOT NULL,
                  PRIMARY KEY (cal_id)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
					
		$sth = $this->dbh->prepare($sql);
		
		$sth->execute();
		
		$sql = "CREATE TABLE cal_order (
                  order_id int(10) unsigned NOT NULL AUTO_INCREMENT,
                  cal_id int(11) NOT NULL,
                  user_id int(11) NOT NULL,
                  billing_name varchar(25) CHARACTER SET utf8 NOT NULL,
                  billing_address varchar(25) CHARACTER SET utf8 NOT NULL,
                  billing_city varchar(25) CHARACTER SET utf8 NOT NULL,
                  billing_zip varchar(10) CHARACTER SET utf8 NOT NULL,
                  billing_mail varchar(100) CHARACTER SET utf8 NOT NULL,
                  billing_phone varchar(15) CHARACTER SET utf8 NOT NULL,
                  shipping_name varchar(25) CHARACTER SET utf8 NOT NULL,
                  shipping_address varchar(25) CHARACTER SET utf8 NOT NULL,
                  shipping_city varchar(25) CHARACTER SET utf8 NOT NULL,
                  shipping_zip varchar(10) CHARACTER SET utf8 NOT NULL,
                  shipping_phone varchar(15) CHARACTER SET utf8 NOT NULL,
                  comment text CHARACTER SET utf8 NOT NULL,
                  transport_method varchar(20) CHARACTER SET utf8 NOT NULL,
                  payment_method varchar(20) CHARACTER SET utf8 NOT NULL,
                  quantity tinyint(3) unsigned NOT NULL,
                  final_price double NOT NULL,
                  smallCouponUsage varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'pouzitie maleho kuponu',
                  zlavaDnaCouponUsage enum('yes','no') CHARACTER SET utf8mb4 NOT NULL DEFAULT 'no',
                  order_sent int(10) unsigned NOT NULL,
                  PRIMARY KEY (order_id)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;";
					
		$sth = $this->dbh->prepare($sql);

		$sth->execute();
		
		$sql = "CREATE TABLE cal_photo (
                  photo_id int(10) unsigned NOT NULL AUTO_INCREMENT,
                  cal_id int(11) NOT NULL,
                  image varchar(255) NOT NULL,
                  position tinyint(4) NOT NULL,
                  left double NOT NULL,
                  top double NOT NULL,
                  width double NOT NULL,
                  height double NOT NULL,
                  month varchar(10) NOT NULL,
                  year int(11) NOT NULL,
                  PRIMARY KEY (photo_id)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
					
		$sth = $this->dbh->prepare($sql);
		
		$sth->execute();
	}
	
	
	/*
	 * Create new record in database (cal_calendar) and return last inserted id
	 *
	 * @call : from controller when user create new calendar; calendar <view> create <layout>
	 * @param <array> $form : data from <form>
	 * @return <int> : last inserted id
	 */
	public function createNewCalendar(array $form)
	{
		$sql = "INSERT INTO cal_calendar (user_id, type, quantity, start_month, start_year, language, status, order_sent, order_sent_price, front_page, create_time) 
                VALUES (:user_id, :type, :quantity, :start_month, :start_year, :language, :status, :order_sent, :order_sent_price, :front_page, :create_time)";
		
		$data = array(
			':user_id' => $form['user_id'], 
			':type' => $form['type'],
            ':quantity' => 1,
			':start_month' => $form['start_month'],
			':start_year' => $form['start_year'],
			':language' => $form['language'],
			':status' => 0,
			':order_sent' => 0,
            ':order_sent_price' => 0,
			':front_page' => $form['front_page'],
			':create_time' => time()	
		);
			
		$sth = $this->dbh->prepare($sql);
			
		$sth->execute($data);
		
		return $this->dbh->lastInsertId();
	}

	public function updateCalendarPrice($calendar_id, $price)
    {
        $sth = $this->dbh->prepare("UPDATE cal_calendar SET order_sent_price = :order_sent_price WHERE cal_id = :cal_id");
        $sth->execute($data = array(
            ':order_sent_price' => $price,
            ':cal_id' => $calendar_id,
        ));
    }

	
	/*
	 * Insert new month into (cal_photo) table
	 *
	 * @call : from controller when user save month settings; calendar <view> edit <layout>
	 * @param <array> $settings : data from <form>
	 * @return <void>
	 */
	public function saveMonth(array $settings)
	{
		$sql = "INSERT INTO cal_photo (`cal_id`, `image`, `position`, `left`, `top`, `width`, `height`, `month`, `year`) 
                VALUES (:cal_id, :image, :position, :left, :top, :width, :height, :month, :year)";

		for ($i=1; $i<count($settings['pictures'])+1; $i++)
		{
			$data = array(
				':cal_id'   => $settings['id'],
				':image'    => $settings['pictures'][$i]['img'],
				':position' => $i,
				':left'     => $settings['pictures'][$i]['left'],
				':top'      => $settings['pictures'][$i]['top'],
				':width'    => $settings['pictures'][$i]['width'],
				':height'   => $settings['pictures'][$i]['height'],
				':month'    => $settings['current_month'],
				':year'     => $settings['current_year']
			);
				
			$sth = $this->dbh->prepare($sql);
				
			$result = $sth->execute($data);
		}
	}
	
	
	/*
	 * Update month in (cal_photo) table 
	 *
	 * @call : from controller when user save month settings; calendar <view> edit <layout>
	 * @param <array> $settings : data from <form>
	 * @return <void>
	 */
	public function updateMonth(array $settings)
	{
		$sql = "UPDATE cal_photo SET `image` = :image, `position` = :position, `left` = :left, `top` = :top, `width` = :width, `height` = :height 
                WHERE `cal_id` = :cal_id AND `month` = :month AND `position` = :position AND `year` = :year";

		for ($i=1; $i<count($settings['pictures'])+1; $i++)
		{
			$data = array(
				':cal_id' => $settings['id'], 
				':image' => $settings['pictures'][$i]['img'],
				':position' => $i,
				':left' => $settings['pictures'][$i]['left'],
				':top' => $settings['pictures'][$i]['top'],
				':width' => $settings['pictures'][$i]['width'],
				':height' => $settings['pictures'][$i]['height'],
				':month' => $settings['current_month'],
				':year' => $settings['current_year']
			);
				
			$sth = $this->dbh->prepare($sql);

			$sth->execute($data);
		}
	}
	
	/*
	 * Get images from database
	 */
	public function getOldMonth($settings)
	{		
		$sth = $this->dbh->prepare("SELECT image FROM cal_photo WHERE cal_id = :cal_id AND month = :month");
		
		$sth->execute( array( ':cal_id' => $settings['id'], ':month' => $settings['current_month'] ) );
		
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		return $result;
	}
	
	
	/*
	 * Load month settings for current month by cal_id
	 *
	 * @call : from controller when user click on certain month (to load); calendar <view> edit <layout>
	 * @param <array> $settings : data from <form>
	 * @return <array> $settings : updated month settings
	 */
	public function loadMonth(array $settings)
	{
		$sth = $this->dbh->prepare('SELECT * FROM `cal_photo` WHERE `cal_id` = :cal_id AND `month` = :month ORDER BY position ASC');
		
		$sth->execute( array( ':cal_id' => $settings['id'], ':month' => $settings['current_month'] ) );

		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
				
		if (count($result) > 0)
		{
			for ($i=0; $i<count($result); $i++)
			{
				$j = (1 + $i);
				$settings['year'] = $result[$i]['year'];
				$settings['pictures'][$j]['img'] = $result[$i]['image'];
				$settings['pictures'][$j]['top'] = $result[$i]['top'];
				$settings['pictures'][$j]['left'] = $result[$i]['left'];
				$settings['pictures'][$j]['width'] = $result[$i]['width'];
				$settings['pictures'][$j]['height'] = $result[$i]['height'];
				$settings['pictures'][$j]['rotate'] = 'auto';
				$settings['pictures'][$j]['year'] = $result[$i]['year'];
			}
		}
		else
		{
			unset($settings['pictures']);
		}
		
		return $settings;
	}


	/*
	 * Check if month with cal_id already exists in database (cal_photo)
	 *
	 * @param <array> $settings : data from <form> in calendar <view> edit <layout>
	 * @return <boolean>
	 */
	public function existsMonth(array $settings)
	{
		$sth = $this->dbh->prepare('SELECT * FROM cal_photo WHERE cal_id = :cal_id AND month = :month');
		
		$sth->execute( array( ':cal_id' => $settings['id'], ':month' => $settings['current_month']	) );
		
		if ($sth->rowCount() > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function checkOrderSendStatus(array $settings, $user_id)
	{
	    $sql = "SELECT r.* FROM cal_order_rel_calendar AS r
                LEFT JOIN cal_calendar AS c ON c.cal_id = r.cal_id
                WHERE c.cal_id = :cal_id AND c.user_id = :user_id;";

		$sth = $this->dbh->prepare($sql);
		
		$sth->execute( array( ':cal_id' => $settings['cal_id'], ':user_id' => $user_id	) );
		
		if ($sth->rowCount() > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/*
	 * Return number of filled months with cal_id in database (cal_photo)
	 *
	 * @param <array> $settings : data from <form> in calendar <view> edit <layout>
	 * @return <int> : number of rows
	 */
	public function filledMonths($cal_id)
	{
		$sth = $this->dbh->prepare('SELECT month FROM cal_photo WHERE cal_id = :cal_id');
		
		$sth->execute( array( ':cal_id' => $cal_id ) );
		
		return $sth->rowCount();
	}
	
	
	/*
	 * Change order status [0 - not finished; 1 - finished; 2 - finished and order sent; 3 - in progress; 4 - finished]
	 *
	 * @param <int> $cal_id : calendar identificator
	 * @param <int> $status : status to be change
	 * @return <void>
	 */
	public function setOrderStatus($cal_id, $status)
	{
		$sth = $this->dbh->prepare('UPDATE cal_calendar SET status = :status WHERE cal_id = :cal_id');
		
		$sth->execute( array( ':cal_id' => $cal_id, ':status' => $status ) );
	}
	
	
	/*
	 * Get order status
	 *
	 * @param <int> $cal_id : calendar identificator
	 * @return <int> : order status
	 */
	public function getOrderStatus($cal_id = 0)
	{
		$sth = $this->dbh->prepare('SELECT status FROM cal_calendar WHERE cal_id = :cal_id');
		
		$sth->execute( array( ':cal_id' => $cal_id ) );
		
		$result = $sth->fetch(PDO::FETCH_ASSOC);
		
		return $result;
	}
	
	
	/*
	 * Save order detail to database
	 *
	 * @param <array> $data : data submited from form
	 */
	public function saveOrderDetail($user_id, $coupon_id, $billing_name, $billing_city, $billing_address, $billing_address_number, $billing_zip, $billing_mail,
                                    $billing_phone, $billing_ico, $billing_dic, $billing_icdph, $shipping_name, $shipping_city, $shipping_address, $shipping_address_number,
                                    $shipping_zip, $shipping_phone, $comment, $transport_method, $payment_method, $price_shipping_and_packing, $calendar_ids, $calendar_prices, $depo_number)
	{
		$sql = "
          INSERT INTO cal_order 
			(user_id, coupon_id, billing_name, billing_address, billing_address_number, billing_city, billing_zip, billing_mail, billing_phone, billing_ico, billing_dic, 
			billing_icdph, shipping_name, shipping_address, shipping_address_number, shipping_city, shipping_zip, shipping_phone, comment, transport_method, payment_method, 
			price_shipping_and_packing, status, order_sent, depo_place_id)
		  VALUES (
		    :user_id, :coupon_id, :billing_name, :billing_address, :billing_address_number, :billing_city, :billing_zip, :billing_mail, :billing_phone, :billing_ico, :billing_dic,
		    :billing_icdph, :shipping_name, :shipping_address, :shipping_address_number, :shipping_city, :shipping_zip, :shipping_phone, :comment, :transport_method, :payment_method, 
		    :price_shipping_and_packing, :status, :order_sent, :depo_place_id)";
			
		$sth = $this->dbh->prepare($sql);

        $sth->execute(
            array(
                ':user_id'                    => $user_id,
                ':coupon_id'                  => $coupon_id,
                ':billing_name'               => $billing_name,
                ':billing_address'            => $billing_address,
                ':billing_address_number'     => $billing_address_number,
                ':billing_city'               => $billing_city,
                ':billing_zip'                => $billing_zip,
                ':billing_mail'               => $billing_mail,
                ':billing_phone'              => $billing_phone,
                ':billing_ico'                => $billing_ico,
                ':billing_dic'                => $billing_dic,
                ':billing_icdph'              => $billing_icdph,
                ':shipping_name'              => $shipping_name,
                ':shipping_address'           => $shipping_address,
                ':shipping_address_number'    => $shipping_address_number,
                ':shipping_city'              => $shipping_city,
                ':shipping_zip'               => $shipping_zip,
                ':shipping_phone'             => $shipping_phone,
                ':comment'                    => $comment,
                ':transport_method'           => $transport_method,
                ':payment_method'             => $payment_method,
                ':price_shipping_and_packing' => $price_shipping_and_packing,
                ':status'                     => 'sent',
                ':order_sent'                 => time(),
                ':depo_place_id'              => $depo_number
            )
        );

        $order_id = $this->dbh->lastInsertId();

        foreach ($calendar_ids as $index => $calendar_id) {
            $sql = "UPDATE cal_calendar SET order_sent = :order_sent, status = :status, order_sent_price = :order_sent_price WHERE cal_id = :cal_id";
            $sth = $this->dbh->prepare($sql);
            $data = array( ':cal_id' => $calendar_id, ':order_sent' => 1, ':status' => 2, ':order_sent_price' => $calendar_prices[$calendar_id]);
            $sth->execute($data);

            $sql = "INSERT INTO cal_order_rel_calendar (cal_id, cal_order_id) VALUES (:cal_id, :cal_order_id)";
            $sth = $this->dbh->prepare($sql);
            $data = array( ':cal_id' => $calendar_id, ':cal_order_id' => $order_id);
            $sth->execute($data);
        }

        return $order_id;
    }

	public function gpWebpayCreate($order_id, $order_number) {
		$sql = "
          INSERT INTO cal_payment_gpwebpay (order_id, order_number)
		  VALUES (:order_id, :order_number)";

		$sth = $this->dbh->prepare($sql);

		$sth->execute(
            array(
                ':order_id' => $order_id,
				':order_number' => $order_number
			)
		);

		$order_id = $this->dbh->lastInsertId();
	}

	public function getGpWebpayPaid($order_id) {
		$sql = "SELECT * FROM cal_payment_gpwebpay WHERE order_id = :order_id AND status = :status";

		$sth = $this->dbh->prepare($sql);

		$sth->execute(
            array(
                ':order_id' => $order_id,
				':status' => 'paid'
			)
		);

		$data = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		 return $data;
	}

	public function getGpWebpayList($order_id) {
		try {
			$sql = "SELECT * FROM cal_payment_gpwebpay WHERE order_id = :order_id;";
			$sth = $this->dbh->prepare($sql);
			$sth->execute(
				array(
					':order_id' => $order_id
				)
			);
			return $sth->fetchAll(PDO::FETCH_ASSOC);
		} catch(Exception $e) {
			echo $e;
		}
	}

	public function updateGpWebpayState($order_id, $order_number, $response_prcode, $response_srcode, $response_resulttext, $status) {

		$sql = "
		UPDATE cal_payment_gpwebpay SET 
			response_prcode = :response_prcode, 
			response_srcode = :response_srcode, 
			response_resulttext = :response_resulttext, 
			updated_at = NOW(), 
			status = :status 
		WHERE order_id = :order_id AND order_number = :order_number";

		$sth = $this->dbh->prepare($sql);

		$sth->execute(
            array(
                ':response_prcode' => $response_prcode,
				':response_srcode' => $response_srcode,
				':response_resulttext' => $response_resulttext,
				':order_id' => $order_id,
				':order_number' => $order_number,
				':status' => $status,
			)
		);

		return $this->dbh->lastInsertId();
	}
	
	public function getOrderDetail($order_id, $user_id)
	{
        $sql = "
            SELECT o.*, IFNULL(d.coupon_code, '') AS coupon_code, IFNULL(d.discount, 0) AS discount 
                FROM cal_order AS o 
                LEFT JOIN cal_coupon AS d ON d.id = o.coupon_id 
                WHERE o.order_id = :order_id AND o.user_id = :user_id";
        $sth = $this->dbh->prepare($sql);
        $data = array( ':order_id' => $order_id, ':user_id' => $user_id );
        $sth->execute($data);
        $order = $sth->fetch(PDO::FETCH_ASSOC);

        $sql = "SELECT c.* FROM cal_calendar AS c LEFT JOIN cal_order_rel_calendar o ON o.cal_id = c.cal_id WHERE o.cal_order_id = :order_id AND c.user_id = :user_id";
        $sth = $this->dbh->prepare($sql);
        $data = array( ':order_id' => $order_id, ':user_id' => $user_id );
        $sth->execute($data);
        $calendars = $sth->fetchAll(PDO::FETCH_ASSOC);

        $order['price_calendars'] = 0;
        foreach ($calendars as $calendar) {
            $order['price_calendars'] += $calendar['order_sent_price'] * $calendar['quantity'];
        }

        return array(
            'order'     => $order,
            'calendars' => $calendars
        );
	}
	
	public function getOrdersList($user_id)
	{
	    $sth = $this->dbh->prepare("
            SELECT cal_order.*, sum(cal_calendar.quantity) AS quantity, cal_coupon.*,
            sum(round(cal_calendar.order_sent_price * cal_calendar.quantity, 2)) AS price_calendars,
            IFNULL(cal_coupon.coupon_code, '') AS coupon_code
            FROM cal_order 
            LEFT JOIN cal_order_rel_calendar ON cal_order.order_id= cal_order_rel_calendar.cal_order_id 
            LEFT JOIN cal_calendar ON cal_calendar.cal_id = cal_order_rel_calendar.cal_id
            LEFT JOIN cal_coupon ON cal_coupon.id= cal_order.coupon_id
            WHERE cal_order.user_id = :user_id
            GROUP BY cal_order.order_id ORDER BY cal_order.order_id DESC
        ");

		$data = array( ':user_id' => $user_id );
		
		$sth->execute($data);
		
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		return $result;
	}
	
	public function getAllOrders($limit = 0, $status = null, $filter = null)
	{
	
		if($status != null || $status != '')
		{
			if($filter != null || $filter != '')
			{
				$sql  = "SELECT cal_order.*, cal_calendar.* FROM cal_order JOIN cal_calendar ON cal_order.cal_id = cal_calendar.cal_id WHERE cal_calendar.status = :status ";
				$sql .= " AND cal_order.billing_name LIKE '%".$filter."%' ";
				$sql .= " ORDER BY order_id ASC LIMIT $limit, 25";
			}
			else
			{
				$sql = "SELECT cal_order.*, cal_calendar.* FROM cal_order JOIN cal_calendar ON cal_order.cal_id = cal_calendar.cal_id WHERE cal_calendar.status = :status ORDER BY order_id ASC LIMIT $limit, 15";
			}
			
			$sth = $this->dbh->prepare($sql);
		
			$sth->execute(array(':status' => $status));
		}
		else
		{
			
			if($filter != null || $filter != '')
			{
				$sql = "SELECT cal_order.*, cal_calendar.* FROM cal_order JOIN cal_calendar ON cal_order.cal_id = cal_calendar.cal_id ";
				$sql .= " AND cal_order.billing_name LIKE '%".$filter."%' ";
				$sql .= " ORDER BY order_id ASC LIMIT $limit, 15";
			}
			else
			{
				$sql = "SELECT cal_order.*, cal_calendar.* FROM cal_order JOIN cal_calendar ON cal_order.cal_id = cal_calendar.cal_id ORDER BY order_id ASC LIMIT $limit, 15";
			}
			
			
			
			$sth = $this->dbh->prepare($sql);
		
			$sth->execute();
		}
		
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		return $result;
	}
	
	public function getAllOrdersNumRows($status = null, $filter = null)
	{		
		if($status != null || $status != '')
		{
			
			if($filter != null || $filter != '')
			{
				$sql = "SELECT cal_order.*, cal_calendar.* FROM cal_order JOIN cal_calendar ON cal_order.cal_id = cal_calendar.cal_id WHERE status = :status";
				$sql .= " AND cal_order.billing_name LIKE '%".$filter."%' ";
			}
			else
			{
				$sql = "SELECT cal_order.*, cal_calendar.* FROM cal_order JOIN cal_calendar ON cal_order.cal_id = cal_calendar.cal_id WHERE status = :status";
			}
		
			$sth = $this->dbh->prepare($sql);
			
			$sth->execute(array(':status' => $status));
		}
		else
		{
			if($filter != null || $filter != '')
			{
				$sql = "SELECT cal_order.*, cal_calendar.* FROM cal_order JOIN cal_calendar ON cal_order.cal_id = cal_calendar.cal_id";
				$sql .= " AND cal_order.billing_name LIKE '%".$filter."%' ";
			}
			else
			{
				$sql = "SELECT cal_order.*, cal_calendar.* FROM cal_order JOIN cal_calendar ON cal_order.cal_id = cal_calendar.cal_id";
			}
			
			
			$sth = $this->dbh->prepare($sql);
			
			$sth->execute();
		}
		
		$num_rows = $sth->rowCount();
		
		return $num_rows;
	}
	
	public function getCalType($cal_id)
	{
		$sth = $this->dbh->prepare("SELECT type FROM cal_calendar WHERE cal_id = :cal_id");
		
		$sth->execute( array( ':cal_id' => $cal_id ) );
		
		$result = $sth->fetch(PDO::FETCH_ASSOC);
		
		return $result;
	}
	
	public function deleteOrder($cal_id)
	{
		$sth = $this->dbh->prepare("DELETE FROM cal_calendar WHERE cal_id = :cal_id");
		
		$sth->execute( array( ':cal_id' => $cal_id ) );
		
		$sth = $this->dbh->prepare("DELETE FROM cal_order WHERE cal_id = :cal_id");
		
		$sth->execute( array( ':cal_id' => $cal_id ) );
		
		$sth = $this->dbh->prepare("DELETE FROM cal_photo WHERE cal_id = :cal_id");
		
		$sth->execute( array( ':cal_id' => $cal_id ) );
	}
	
	public function getCalendarDetail($cal_id)
	{
		$sth = $this->dbh->prepare("SELECT * FROM cal_calendar WHERE cal_id = :cal_id");
		
		$sth->execute( array( ':cal_id' => $cal_id ) );
		
		$result = $sth->fetch(PDO::FETCH_ASSOC);

		return $result;
	}
	
	public function setCoverText($cal_id, $front_page_text)
	{

		$sth = $this->dbh->prepare('UPDATE cal_calendar SET front_page_text = :front_page_text WHERE cal_id = :cal_id');
		
		$sth->execute( array( ':cal_id' => $cal_id, ':front_page_text' => $front_page_text ) );
	}
	
	public function getCoverText($cal_id)
	{
		$sth = $this->dbh->prepare("SELECT front_page_text FROM cal_calendar WHERE cal_id = :cal_id");
		
		$sth->execute( array( ':cal_id' => $cal_id ) );
		
		$result = $sth->fetch(PDO::FETCH_ASSOC);
		
		return $result['front_page_text'];
	}
	
	public function getUsedImages($pictures, $cal_id)
	{
	    if (!$pictures) {
	        return array();
        }

		$sth = $this->dbh->prepare("SELECT image FROM cal_photo WHERE cal_id = :cal_id");
		
		$sth->execute( array( ':cal_id' => $cal_id ) );
		
		$used_pictures = $sth->fetchAll(PDO::FETCH_COLUMN);

		$pictures_used = array();
		
		if(count($used_pictures) > 0)
		{			
			for($i=0; $i<count($pictures); $i++)
			{
				$pictures_2[$i] = str_replace('img_thumbs', 'img', $pictures[$i]);
			}
			
			$used = array_values(array_intersect($pictures_2, $used_pictures));
			
			for($i=0; $i<count($used); $i++)
			{
				$pictures_used[$i] = str_replace('img', 'img_thumbs', $used[$i]);
			}
		}
		
		return $pictures_used;
	}

	public function isCalendarAssignedToUser($cal_id, $user_id)
	{
		$sql  = "SELECT * FROM cal_calendar WHERE user_id = :user_id AND cal_id = :cal_id";

		$sth = $this->dbh->prepare($sql);
		
		$sth->execute( array( ':user_id' => $user_id, ':cal_id' => $cal_id ) );
		
		$calendar = $sth->fetch(PDO::FETCH_ASSOC);
		
		if(!empty($calendar))
		{
			return true;
		} else {
			return false;
		}

	}

	public function getCalendars($user_id)
	{
		$sql = "SELECT c.* FROM cal_calendar AS c
                LEFT JOIN cal_order_rel_calendar AS o ON c.cal_id = o.cal_id
                WHERE c.user_id = :user_id AND c.status IN (0,1) AND o.cal_id IS NULL
                ORDER BY c.cal_id DESC;";

		$sth = $this->dbh->prepare($sql);

		$sth->execute( array( ':user_id' => $user_id ) );
		
		$calendars = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		return $calendars;
	}

    public function getNonFihisnedcalendars($user_id)
    {
        $sql = "SELECT c.* FROM cal_calendar AS c
                LEFT JOIN cal_order_rel_calendar AS o ON c.cal_id = o.cal_id
                WHERE c.user_id = :user_id AND c.status = 0 AND o.cal_id IS NULL
                ORDER BY c.cal_id DESC;";

        $sth = $this->dbh->prepare($sql);

        $sth->execute( array( ':user_id' => $user_id ) );

        $calendars = $sth->fetchAll(PDO::FETCH_ASSOC);

        return $calendars;
    }

    public function getReadyForOrderCalendars($user_id)
    {
        $sql = "SELECT c.* FROM cal_calendar AS c
                LEFT JOIN cal_order_rel_calendar AS o ON c.cal_id = o.cal_id
                WHERE c.user_id = :user_id AND c.status = 1 AND o.cal_id IS NULL
                ORDER BY c.cal_id DESC;";

        $sth = $this->dbh->prepare($sql);

        $sth->execute( array( ':user_id' => $user_id ) );

        $calendars = $sth->fetchAll(PDO::FETCH_ASSOC);

        return $calendars;
    }
	
	public function getCalendarData($cal_id)
	{
		$sth = $this->dbh->prepare("SELECT * FROM cal_calendar WHERE cal_id = :cal_id");
		
		$sth->execute( array( ':cal_id' => $cal_id ) );
		
		$calendar = $sth->fetch(PDO::FETCH_ASSOC);
		
		$sth = $this->dbh->prepare("SELECT * FROM cal_photo WHERE cal_id = :cal_id");
		
		$sth->execute( array( ':cal_id' => $cal_id ) );
		
		$calendar['photos'] = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		return $calendar;
	}
	
	public function getInvoiceNumber()
	{		
		$sth = $this->dbh->prepare("SELECT invoice_number FROM cal_invoice_numbering ORDER BY id DESC LIMIT 1;");
		
		$sth->execute();
		
		$result = $sth->fetch(PDO::FETCH_ASSOC);
		
		$sth = $this->dbh->prepare("INSERT INTO cal_invoice_numbering (invoice_number, order_number) VALUES (:next_id, :order)");
		
		$next_id = $result['invoice_number'] + 1;
		
		$sth->execute(array(':next_id' => $next_id, ':order' => 0));
		
		return $result['invoice_number'];
	}

    public function updateOrderInvoiceURL($order_id, $url)
    {
        $sth = $this->dbh->prepare("UPDATE cal_order SET ikros_url = :ikros_url WHERE order_id = :order_id;");

        $sth->execute( array(
            ':ikros_url' => $url,
            ':order_id'  => $order_id
        ));
    }

	public function updateCalendarQuantity($calendar_id, $user_id, $quantity)
    {
        $sth = $this->dbh->prepare("UPDATE cal_calendar SET quantity = :quantity WHERE cal_id = :cal_id AND user_id = :user_id AND order_sent = :order_sent;");

        $sth->execute( array(
            ':cal_id'     => $calendar_id,
            ':quantity'   => $quantity,
            ':user_id'    => $user_id,
            ':order_sent' => 0
        ));
    }

	public function deleteCalendar($cal_id)
    {
	    $sth = $this->dbh->prepare("DELETE FROM cal_calendar WHERE cal_id = :cal_id");

	    $sth->execute(array(':cal_id' => $cal_id));

        $sth = $this->dbh->prepare("DELETE FROM cal_photo WHERE cal_id = :cal_id");

        $sth->execute(array(':cal_id' => $cal_id));
    }

    public function getCouponByCode($coupon_code)
    {
        $sth = $this->dbh->prepare("SELECT c.*, o.order_id IS NOT NULL AS used FROM cal_coupon AS c LEFT JOIN cal_order AS o ON o.coupon_id = c.id WHERE c.coupon_code = :coupon_code;");

        $sth->execute(array(':coupon_code' => $coupon_code));

        $result = $sth->fetch(PDO::FETCH_ASSOC);

        if ($result)
        {
            $coupon = new StdClass();

            $coupon->code = $coupon_code;

            $coupon->id = $result['id'];

            $coupon->used = $result['used'] == "1";

            $coupon->unlimited = $result['unlimited'] == "1";

            $coupon->discount = $result['discount'];

            $coupon->category = $result['category'];

            $coupon->valid_from = $result['valid_from'];

            $coupon->valid_from = $result['valid_till'];

            $coupon->in_date_range = strtotime($coupon->valid_from) >= time() && strtotime($coupon->valid_till) <= time();

            $coupon->valid = ($coupon->unlimited || ! $coupon->unlimited && ! $coupon->used) && $coupon->in_date_range ;

            return $coupon;
        }

        return false;
    }

    public function getCalendarSales()
    {
        $sth = $this->dbh->prepare("SELECT * FROM cal_sales");

        $sth->execute();

        $sales = $sth->fetchAll(PDO::FETCH_ASSOC);

        $result = array();

        foreach ($sales as $sale) {
            $result[$sale['calendar_type']]['percentSale'] = $sale['discount'];
            $result[$sale['calendar_type']]['validFrom'] = $sale['valid_from'];
            $result[$sale['calendar_type']]['validTo'] = $sale['valid_till'];
        }

        return $result;
    }

    public function updateCalendarSale($calendar_type, $discount, $valid_from, $valid_till)
    {
        $sth = $this->dbh->prepare("UPDATE cal_sales SET discount = :discount, valid_from = :valid_from, valid_till = :valid_till WHERE calendar_type = :calendar_type");

        $sth->execute(array(
            ':discount'      => $discount,
            ':valid_from'    => $valid_from,
            ':valid_till'    => $valid_till,
            ':calendar_type' => $calendar_type
        ));
    }

    public function updateDepoNumber($orderId, $depoNumber)
    {
        $sth = $this->dbh->prepare("UPDATE cal_order SET depo_number = :depo_number WHERE order_id = :order_id;");

        $sth->execute(array(
            ':depo_number' => $depoNumber,
            ':order_id'    => $orderId
        ));
    }

    public function updateRemaxResult($orderId, $remaxResult)
    {
        $sth = $this->dbh->prepare("UPDATE cal_order SET remax_result = :remax_result WHERE order_id = :order_id;");

        $sth->execute(array(
            ':remax_result' => $remaxResult,
            ':order_id'    => $orderId
        ));
    }
}