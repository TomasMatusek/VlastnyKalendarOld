<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

@include_once(JPATH_BASE . 'configuration.php');

class CalendarModelAdmin extends JModelLegacy
{
    private $dbh;

    public function __construct($config_array)
    {
        $config = new JConfig();

        $this->dbh = new PDO('mysql:host=' . $config->host . ';dbname=' . $config->db, $config->user, $config->password);

        $this->dbh->exec("SET collation_connection = utf8_bin; SET NAMES utf8;");

        parent::__construct($config_array);
    }


    public function saveUserRegistrationAgreement() {
        echo 'HAHAHAHHAHAHAH';
    }

    /**
    SELECT cal_coupon.discount, cal_order.*, sum(cal_calendar.quantity) AS quantity,
    sum(cal_calendar.order_sent_price) AS price_calendars
    FROM cal_order
    LEFT JOIN cal_order_rel_calendar ON cal_order.order_id= cal_order_rel_calendar.cal_order_id
    LEFT JOIN cal_calendar ON cal_calendar.cal_id = cal_order_rel_calendar.cal_id
    LEFT JOIN cal_coupon ON cal_coupon.id= cal_order.coupon_id
    GROUP BY cal_order.order_id ORDER BY cal_order.order_id DESC
     */
    public function getOrders($status, $search, $page_offset)
    {
        if (strlen($search) > 0)
        {
            $sth = $this->dbh->prepare("
                    SELECT cal_order.*, sum(cal_calendar.quantity) AS quantity, cal_coupon.*, 
                    sum(round(cal_calendar.order_sent_price * cal_calendar.quantity, 2)) AS price_calendars,
                    IFNULL(cal_coupon.coupon_code, '') AS coupon_code
                    FROM cal_order 
                    LEFT JOIN cal_order_rel_calendar ON cal_order.order_id= cal_order_rel_calendar.cal_order_id 
                    LEFT JOIN cal_calendar ON cal_calendar.cal_id = cal_order_rel_calendar.cal_id
                    LEFT JOIN cal_coupon ON cal_coupon.id= cal_order.coupon_id
                    WHERE cal_order.billing_name LIKE :search OR cal_order.billing_address LIKE :search
                    GROUP BY cal_order.order_id ORDER BY cal_order.order_id DESC LIMIT ".$page_offset.", 40;
            ");

            $sth->execute(array(':search' => '%'.$search.'%'));
        }
        else if (strlen($status) > 0)
        {
            $sth = $this->dbh->prepare("
                SELECT cal_order.*, sum(cal_calendar.quantity) AS quantity, cal_coupon.*,
                sum(round(cal_calendar.order_sent_price * cal_calendar.quantity, 2)) AS price_calendars,
                IFNULL(cal_coupon.coupon_code, '') AS coupon_code
                FROM cal_order 
                LEFT JOIN cal_order_rel_calendar ON cal_order.order_id= cal_order_rel_calendar.cal_order_id 
                LEFT JOIN cal_calendar ON cal_calendar.cal_id = cal_order_rel_calendar.cal_id
                LEFT JOIN cal_coupon ON cal_coupon.id= cal_order.coupon_id
                WHERE cal_order.status = :status
                GROUP BY cal_order.order_id ORDER BY cal_order.order_id DESC LIMIT ".$page_offset.", 40;
            ");

            $sth->execute(array(':status' => $status));
        }
        else
        {
            $sth = $this->dbh->prepare("
                SELECT cal_order.*, sum(cal_calendar.quantity) AS quantity, cal_coupon.*,
                sum(round(cal_calendar.order_sent_price * cal_calendar.quantity, 2)) AS price_calendars,
                IFNULL(cal_coupon.coupon_code, '') AS coupon_code
                FROM cal_order 
                LEFT JOIN cal_order_rel_calendar ON cal_order.order_id= cal_order_rel_calendar.cal_order_id 
                LEFT JOIN cal_calendar ON cal_calendar.cal_id = cal_order_rel_calendar.cal_id
                LEFT JOIN cal_coupon ON cal_coupon.id= cal_order.coupon_id
                WHERE cal_order.order_id IN (SELECT * FROM (SELECT cal_order.order_id FROM cal_order GROUP BY cal_order.order_id ORDER BY cal_order.order_id DESC LIMIT ".$page_offset.",40) AS table1)
                AND cal_order.status <> 'canceled'
                GROUP BY cal_order.order_id ORDER BY cal_order.order_id DESC;
            ");

            $sth->execute();
        }

        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderDetail($order_id)
    {
        $sql = "SELECT o.*, IFNULL(d.coupon_code, '') AS coupon_code, IFNULL(d.discount, 0) AS discount 
                FROM cal_order AS o 
                LEFT JOIN cal_coupon AS d ON d.id = o.coupon_id 
                WHERE o.order_id = :order_id";
        $sth = $this->dbh->prepare($sql);
        $data = array( ':order_id' => $order_id );
        $sth->execute($data);
        $order = $sth->fetch(PDO::FETCH_ASSOC);

        $sql = "SELECT c.* FROM cal_calendar AS c LEFT JOIN cal_order_rel_calendar o ON o.cal_id = c.cal_id WHERE o.cal_order_id = :order_id";
        $sth = $this->dbh->prepare($sql);
        $data = array( ':order_id' => $order_id );
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

    public function getCalendarDetail($calendar_id)
    {
        $sth = $this->dbh->prepare("SELECT * FROM cal_calendar WHERE cal_id = :calendar_id");

        $sth->execute(array(
            ':calendar_id'   => $calendar_id,
        ));

        return $sth->fetch(PDO::FETCH_ASSOC);
    }

    public function setOrderStatus($order_id, $status)
    {
        $sth = $this->dbh->prepare("UPDATE cal_order SET status = :status WHERE order_id = :order_id");

        $sth->execute(array(
            ':status'   => $status,
            ':order_id' => $order_id
        ));
    }

    public function getLatestInvoiceNumber()
    {
        $sth = $this->dbh->prepare("SELECT invoice_number FROM cal_invoice_number LIMIT 1");
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        return count($result) > 0 ? $result['invoice_number'] : 0;
    }

    public function increaseInvoiceNumber()
    {
        $sth = $this->dbh->prepare("UPDATE cal_invoice_number SET invoice_number = invoice_number + 1;");
        $sth->execute();
    }

    public function updateOrderInvoiceNumber($invoice_number, $order_id)
    {
        $sth = $this->dbh->prepare("UPDATE cal_order SET invoice_number = :invoice_number WHERE order_id = :order_id;");
        $sth->execute(array( 'invoice_number' => $invoice_number, 'order_id' => $order_id));
    }

    public function getOrdersCount()
    {
        $sth = $this->dbh->prepare("SELECT count(*) AS items FROM cal_order;");
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        return $result['items'];
    }

    public function getDiscountCoupons()
    {
        $sth = $this->dbh->prepare("SELECT * FROM cal_coupon ORDER BY unlimited DESC, id DESC");
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteDiscountCoupon($coupon_id)
    {
        $sth = $this->dbh->prepare("DELETE FROM cal_coupon WHERE id = :coupon_id");
        $sth->execute(array(':coupon_id' => $coupon_id));
    }

    public function updateDiscountCoupon($coupon_id, $coupon_code, $discount, $unlimited, $valid_from, $valid_till, $category, $name)
    {
        $sth = $this->dbh->prepare("UPDATE cal_coupon 
          SET `coupon_code` = :coupon_code,
              `discount` = :discount, 
              `unlimited` = :unlimited, 
              `valid_from` = :valid_from, 
              `valid_till` = :valid_till, 
              `category` = :category, 
              `name` = :name 
          WHERE `id` = :coupon_id");

        $sth->execute(
            array(
                ':coupon_code' => $coupon_code,
                ':discount'    => $discount,
                ':unlimited'   => $unlimited,
                ':valid_from'  => $valid_from,
                ':valid_till'  => $valid_till,
                ':category'    => $category,
                ':name'        => $name,
                ':coupon_id'   => $coupon_id
            )
        );
    }

    public function createDiscountCoupon($coupon_code, $discount, $unlimited, $valid_from, $valid_till, $category, $name)
    {
        $sth = $this->dbh->prepare("
          INSERT INTO cal_coupon (`coupon_code`,`discount`,`unlimited`,`valid_from`,`valid_till`,`category`, `name`) 
          VALUES(:coupon_code, :discount, :unlimited, :valid_from, :valid_till, :category, :name); 
        ");

        $sth->execute(
            array(
                ':coupon_code' => $coupon_code,
                ':discount'    => $discount,
                ':unlimited'   => $unlimited,
                ':valid_from'  => $valid_from,
                ':valid_till'  => $valid_till,
                ':category'    => $category,
                ':name'        => $name
            )
        );

        return $this->dbh->lastInsertId();
    }

    public function isUsersOrder($user_id, $order_id)
    {
        $sth = $this->dbh->prepare("select * from cal_order where user_id = :user_id and order_id = :order_id;");
        $sth->execute(
            array(
                ':user_id' => $user_id,
                ':order_id'    => $order_id
            )
        );
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        return count($result) > 0;
    }

    public function getAllImagesUsedInCalendars($user_id)
    {
        $sth = $this->dbh->prepare("SELECT image FROM cal_photo WHERE cal_id IN (SELECT cal_id FROM cal_calendar WHERE user_id = :user_id) AND image <> ''");

        $sth->execute(array(':user_id' => $user_id));

        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        $files = array();

        foreach ($result as $index => $file_path) {
            $file_path_parts = explode("/", $file_path['image']);
            $file_name_position = count($file_path_parts) - 1;
            array_push($files, $file_path_parts[$file_name_position]);
        }

        return $files;
    }

    public function removeAllUnfinishedCalendarsAndImages($user_id)
    {
        $sth = $this->dbh->prepare("SELECT cal_id FROM cal_calendar WHERE cal_id NOT IN (SELECT cal_id FROM cal_order_rel_calendar) AND user_id = :user_id");

        $sth->execute(array(':user_id' => $user_id));

        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        $sth = $this->dbh->prepare("DELETE FROM cal_calendar WHERE cal_id NOT IN (SELECT cal_id FROM cal_order_rel_calendar) AND user_id = :user_id");

        $sth->execute(array(':user_id' => $user_id));

        $sth = $this->dbh->prepare("DELETE FROM cal_photo WHERE cal_id NOT IN (SELECT cal_id FROM cal_calendar);");

        $sth->execute();

        return $result;
    }

    public function removeLinkBetweenImagesAndCalendar($cal_id)
    {
        $sth = $this->dbh->prepare("DELETE FROM cal_photo WHERE cal_id = :cal_id");

        $sth->execute(array(':cal_id' => $cal_id));
    }
}