<?php

defined('_JEXEC') or die('Restricted access');

use Dompdf\Dompdf;
use Dompdf\Exception;
use Dompdf\Options;

require_once(JPATH_COMPONENT . '/controller.php');
require_once(CAL_COMPONENT_SERVER . "libs/dompdf-master/autoload.inc.php");

class CalendarControllerAdmin extends CalendarController
{

    public function __construct()
    {
        parent::__construct();
    }

    /*************************
     * Public actions
     *************************/

    public function generateSinglePdfPage()
    {
        $calendar_id = $this->input->get('calendar_id', 0);
        $order_id = $this->input->get('order_id', 0);
        $series = $this->input->get('series', 0);
        $user_id = $this->input->get('user_id', 0);
        $month = $this->input->get('month', '');

        if ( ! Permissions::isUserAdmin($this->user_id)) {
            jexit('Only admins can run this action');
        }

        $data = $this->modelAdmin->getOrderDetail($order_id);

        $target_dir = $this->generateOrderAndCalendarDir($data['order']['order_id'], $calendar_id, $data['order']['billing_name']);

        $calendar = $this->getCalendarFromList($data['calendars'], $calendar_id);

        $month_preview_url = $this->getMonthPdfPreviewUrl($month, $calendar_id, $series);

        var_dump($month_preview_url);

        $this->generatePdf($calendar_id, $calendar['quantity'], $month_preview_url, $calendar['type'], $month, $target_dir, $series);
    }

    public function generateSingleZipFile()
    {
        if ( ! Permissions::isUserAdmin($this->user_id)) {
            jexit('Only admins can run this action');
        }

        $calendar_id = $this->input->get('calendar_id', 0);
        $order_id = $this->input->get('order_id', 0);

        $scan_orders_folder = scandir(CalendarControllerAdmin::getOrdersRootDir());
        $calendar_folder_path = '';
        $order_folder_name = '';

        // find order dir

        foreach ($scan_orders_folder as $order_folder) {
            if ($order_folder == '.' || $order_folder == '..') {
                continue;
            }

            if (is_dir(CalendarControllerAdmin::getOrdersRootDir() . $order_folder) && preg_match('/^'.$order_id.'_/', $order_folder)) {
                $order_folder_name = $order_folder;
                $calendar_folder_path = CalendarControllerAdmin::getOrdersRootDir() . $order_folder_name . '/' . $calendar_id;
                break;
            }
        }

        if ( ! file_exists($calendar_folder_path)) {
            echo 'Folder with calendars does not exists!' . $calendar_folder_path;
            jexit();
        }

        // create zip file

        $calendar = $this->modelAdmin->getCalendarDetail($calendar_id);

        $zip_name = $order_folder_name . '_'
            . $calendar_id . '_'
            . $calendar['type'] . '_'
            . $calendar['quantity'] . 'ks.zip';

        $zip_path = CalendarControllerAdmin::getOrdersRootDir() . $order_folder_name . '/' . $zip_name;

        $zip = new ZipArchive;
        $zip->open($zip_path, ZipArchive::CREATE);
        if ($handle = opendir($calendar_folder_path)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && strstr($file, '.pdf')) {
                    $pdf_file_path = $calendar_folder_path . '/' . $file;
                    $zip->addFile($pdf_file_path, $file);
                }
            }
            closedir($handle);
        }
        $zip->close();

        // remove old folder

        $scan_calendar_folder = scandir($calendar_folder_path);

        foreach ($scan_calendar_folder as $pdf_file) {
            if ($pdf_file == '.' || $pdf_file == '..') {
                continue;
            }

            if ( ! is_dir(CalendarControllerAdmin::getOrdersRootDir() . '/' . $pdf_file)) {
                unlink($calendar_folder_path . '/' . $pdf_file);
            }
        }

        rmdir($calendar_folder_path);

        // sent result back

        echo json_encode(
            array(
                'calendar_id' => $calendar_id,
                'file' => $order_folder_name . '/' . $zip_name
            )
        );

        jexit();
    }

    public function deleteZipFile()
    {
        if ( ! Permissions::isUserAdmin($this->user_id)) {
            jexit('Only admins can run this action');
        }

        $calendar_id = $this->input->get('calendar_id', 0);

        $order_id = $this->input->get('order_id', 0);

        if ($calendar_id == 0 || $order_id == 0) {
            jexit('Calendar id or order id is empty');
        }

        $zip_files = Zip::getOrderZipFilesPaths($order_id, array($calendar_id));

        var_dump($zip_files);

        unlink(CalendarControllerAdmin::getOrdersRootDir() . $zip_files[$calendar_id]);

        $this->app->redirect('index.php/component/calendar/?view=admin&layout=order&order_id=' . $order_id);
    }

    public function changeStatus()
    {
        if ( ! Permissions::isUserAdmin($this->user_id)) {
            jexit('Only admins can run this action');
        }

        $order_id = $this->input->get('order_id', 0);

        $status = $this->input->get('status', '');

        if ($order_id > 0 && strlen($status) > 0)
        {
            $this->modelAdmin->setOrderStatus($order_id, $status);
            $order = $this->modelAdmin->getOrderDetail($order_id);
            if (strtolower($status) == 'done') {
                Mail::sendOrderDoneConfirmation($order_id);
                if ($order['order']['transport_method'] == 'personal') {
                    SMS::sendSMS($order['order']['shipping_phone']);
                }
            }
        }

        $this->app->redirect('index.php/component/calendar/?view=admin&layout=order&order_id=' . $order_id);
    }

    public function clearTemporaryFiles()
    {
        if ( ! Permissions::isUserAdmin($this->user_id)) {
            jexit('Only admins can run this action');
        }

        $order_id = $this->input->get('order_id', 0);

        if ($order_id == 0) {
            jexit('Invalid order id');
        }

        $order_folders = Zip::getSubfolders($order_id);

        foreach ($order_folders as $folder) {
            Zip::removeDirWithContent($folder);
        }

        $this->app->redirect('index.php/component/calendar/?view=admin&layout=order&order_id=' . $order_id);
    }


    public function generateInvoice()
    {
        $order_id = $this->input->get('order_id', 0);
        $invoice_type = $this->input->get('invoice_type', '');
        $invoice_number = $this->input->get('invoice_number', 0);
        $invoice_date = $this->input->get('invoice_date', '01.01.' . date('Y'));

        $this->createInvoicePdf($order_id, $invoice_type, $invoice_number, $invoice_date);

        $this->app->redirect('index.php/component/calendar/?view=admin&layout=order&order_id=' . $order_id);
    }

    public function createInvoicePdf($order_id, $invoice_type, $invoice_number, $invoice_date)
    {
        if ($order_id == 0 || ! in_array($invoice_type, array('invoice','order') ) || $invoice_number == 0) {
            jexit('Order id, invoice type or invoice number not set.');
        }

        $url = "https://www.vlastnykalendar.sk/index.php?option=com_calendar&view=invoice&order_id=" . $order_id .
            "&invoice_type=" . $invoice_type .
            "&invoice_date=" . $invoice_date .
            "&invoice_number=" . $invoice_number .
            "&tmpl=component&hash=EDlijt8QoECJYuhAtNFqI9a4b3zC2s5epIHQbTKDGwo4bcsjsODA6JKXhqtJvPza9NoGt0TKa2GiXP7Jzclx1VszUjsqkxUexZauU4yZfXYo28HUJLHq9cCpA8MqsUgZUH83eWcgwxMkBzoDzGI9Bz7O7rL2g9CiVfjGY1CFCnJTOMCt4JFQiUfvLE6hN2p7kqjGv1z7h3SuwRYI7D8sE4BXVUyboAz7UealaP92F3EMn3olQuMcRa0cgU2yQsCU";

        $invoice_html = file_get_contents($url);
        $invoice_html = mb_convert_encoding($invoice_html, 'HTML-ENTITIES', 'UTF-8');

        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $options->setDpi(150);
        $options->setDefaultPaperSize('A4');

        $pdf = new DOMPDF($options);
        $pdf->loadHtml($invoice_html);
        $pdf->render();

        $order_dir_path = $this->getOrderFolderPath($order_id);
        $order_detail = $this->modelAdmin->getOrderDetail($order_id);

        if ($order_dir_path == '') {
            $order_dir_path = $this->generateOrderDir($order_id, $order_detail['order']['billing_name']);
        }

        file_put_contents($order_dir_path . '/' . $invoice_type . '_' . str_pad($order_id, 4, '0', STR_PAD_LEFT) . '.pdf', $pdf->output());

        if ($invoice_type == 'invoice') {
            $this->modelAdmin->updateOrderInvoiceNumber('KA' . date('Y') . str_pad($invoice_number, 4, '0', STR_PAD_LEFT), $order_id);
            $this->modelAdmin->increaseInvoiceNumber();
        }
    }

    public function deleteCoupon()
    {
        if ( ! Permissions::isUserAdmin($this->user_id)) {
            jexit('Only admins can run this action');
        }

        $coupon_id = $this->input->get('coupon_id', 0);
        if ($coupon_id > 0) {
            $this->modelAdmin->deleteDiscountCoupon($coupon_id);
        }
    }

    public function createCoupon()
    {
        if ( ! Permissions::isUserAdmin($this->user_id)) {
            jexit('Only admins can run this action');
        }

        $coupon_code = $this->input->get('coupon_code', '');
        $discount    = $this->input->get('discount', 0);
        $unlimited   = $this->input->get('unlimited', 0);
        $valid_from  = $this->input->get('valid_from', '0000-00-00');
        $valid_till  = $this->input->get('valid_till', '0000-00-00');
        $category    = $this->input->get('category', '');
        $name        = $this->input->get('name', '');

        $coupon_id = $this->modelAdmin->createDiscountCoupon($coupon_code, $discount, $unlimited, $valid_from, $valid_till, $category, $name);

        echo json_encode(
            array(
                'coupon_id' => $coupon_id
            )
        );

        jexit();
    }

    public function updateCoupon()
    {
        if ( ! Permissions::isUserAdmin($this->user_id)) {
            jexit('Only admins can run this action');
        }

        $coupon_id   = $this->input->get('coupon_id', 0);
        $coupon_code = $this->input->get('coupon_code', '');
        $discount    = $this->input->get('discount', 0);
        $unlimited   = $this->input->get('unlimited', 0);
        $valid_from  = $this->input->get('valid_from', '0000-00-00');
        $valid_till  = $this->input->get('valid_till', '0000-00-00');
        $category    = $this->input->get('category', '');
        $name        = $this->input->get('name', '');

        if ($coupon_id > 0) {
            $this->modelAdmin->updateDiscountCoupon($coupon_id, $coupon_code, $discount, $unlimited, $valid_from, $valid_till, $category, $name);
        }
    }

    public function removeEmptyFolders() {
        $hash = "joCNqXq0MIlDsWMjQYzdGBrSpyLCjcLJ4RcXGGwSa0rvnnMxAnZi6yrijNdr5dLIVUnAW28lJPLtQmbUaoT";
        if ($hash != $this->input->get('hash','')) {
            jexit('not permitted to perform operation');
        }

        $path_server = CAL_ROOT_SERVER . 'calendar/';

        if (file_exists($path_server)) {
            $counter = 0;
            $folders = scandir($path_server);

            foreach ($folders as $index => $folder) {
                if (in_array($folder, array('.','..'))) {
                    continue;
                }

                $user_dir_path = $path_server . $folder . '/';
                if (!is_dir($user_dir_path )) {
                    continue;
                }

                $user_img_dir_path = $user_dir_path . 'img/';
                $user_thumb_dir_path = $user_dir_path . 'img_thumbs/';
                $user_backup_dir_path = $user_dir_path . 'img_backup/';

                if (count(scandir($user_img_dir_path)) == 2 &&
                    count(scandir($user_thumb_dir_path)) == 2 &&
                    count(scandir($user_backup_dir_path)) == 2) {

                    echo 'Removing' . $user_img_dir_path . ': ';
                    echo rmdir($user_img_dir_path);
                    echo '<br/>';

                    echo 'Removing' . $user_thumb_dir_path . ': ';
                    echo rmdir($user_thumb_dir_path);
                    echo '<br/>';

                    echo 'Removing' . $user_backup_dir_path . ': ';
                    echo rmdir($user_backup_dir_path);
                    echo '<br/>';

                    echo 'Removing' . $user_dir_path . ': ';
                    echo rmdir($user_dir_path);
                    echo '<br/>';
                }

                echo '=============================================<br/>';

                $counter++;
            }

            echo 'END: ' . $counter;
        }

    }

    public function getClearDiskData()
    {
        $hash = "QZz2HvhejCp0Y6yQZtMXeWam514CIxNgmUHJpsELH5M6kIxK5gJIMzoqDjoCN";
        $path_server = CAL_ROOT_SERVER . 'calendar/';
        $clean_after_days = 5;

        $start = $this->input->get('start',0);
        $limit = 50;

        if ($hash != $this->input->get('hash','')) {
            jexit('not permitted to perform operation');
        }

        // loop all users uplad dirs
        if (file_exists($path_server)) {
            $counter = 0;

            $folders = scandir($path_server);
            foreach ($folders as $index => $file) {
                if (!is_dir($path_server . $file . '/') || in_array($file, array('.','..'))) {
                    unset($folders[$index]);
                }
            }
            sort($folders);

            foreach ($folders as $index => $file) {
                $counter++;

                if ($counter <= $start) {
                    continue;
                }

                if ($counter > $start + $limit) {
                    return;
                }

                if ($index > 0) {
                    $user_id = intval($file);
                    $user_dir_path = $path_server . $file . '/';
                    if (in_array($file, array('.','..')) || !is_dir($user_dir_path )) {
                        continue;
                    }

                    echo '<pre>';

                    // load user profile
                    $user = JFactory::getUser($user_id);
                    $last_visit_date = new DateTime($user->get('lastvisitDate'));
                    $date_diff = $last_visit_date->diff(new DateTime());

                    echo 'User: ' . $user->username . ' (' . $user_id . ') <br/>';
                    echo 'Last visit date: ' . $user->get('lastvisitDate') . ' (' . $date_diff->days . ') <br/>';

                    $orders = $this->model->getOrdersList($user_id);
                    echo 'Orders: ';
                    foreach ($orders as $index => $order) {
                        echo '<a href="https://www.vlastnykalendar.sk/index.php/component/calendar/?view=admin&layout=order&order_id=' . $order['order_id'] . '">' . $order['order_id'] . '</a>, ';
                    }
                    echo '<br/>';

                    // if not logged in for more that 7 days -> remove all uploaded images and thumbnails + unused backup images
                    if ($date_diff->days >= $clean_after_days) {
                        echo 'User not logged in more than ' . $clean_after_days . " in row. Clearing unused data. <br/>";

                        $user_img_dir_path = $user_dir_path . 'img/';
                        $user_thumb_dir_path = $user_dir_path . 'img_thumbs/';
                        $user_backup_dir_path = $user_dir_path . 'img_backup/';

                        DirHelper::removeAllFileFromDir($user_img_dir_path);
                        DirHelper::removeAllFileFromDir($user_thumb_dir_path);

                        // remove from DB all calendars that are unfinished + remove all images with no calendar assigned
                        $removed_calendar = $this->modelAdmin->removeAllUnfinishedCalendarsAndImages($user_id);

                        foreach ($removed_calendar as $index => $calendar_id) {
                            echo 'Removing calendar from DB: ' . $calendar_id . '<br/>';
                        }

                        // get all images used in user's calendars -> these image will be kept; unused images will be deleted
                        $backup_img = $this->modelAdmin->getAllImagesUsedInCalendars($user_id);

                        // remove unused backup files
                        DirHelper::removeAllFileFromDirExpect($user_backup_dir_path, $backup_img);
                    }

                    if ($date_diff->days < $clean_after_days) {
                        echo 'User skipped.';
                    }

                    echo '</pre>';
                }
            }
        }

        jexit();
    }

    public function generateIkrosInvoice() {
        if ( ! Permissions::isUserAdmin($this->user_id)) {
            jexit('Only admins can run this action');
        }

        $order_id = $this->input->get('order_id', 0);
        $user_id = $this->input->get('user_id', 0);

        $order = $this->model->getOrderDetail($order_id, $user_id);
        IkrosServiceBak::pushInvoice($order, $this->model);

        $this->app->redirect('index.php/component/calendar/?view=admin&layout=order&order_id=' . $order_id);
    }

    /*************************
     * Private methods
     *************************/

    private function generateOrderAndCalendarDir($order_id, $calendar_id, $order_billing_name)
    {
        $order_dir_name = $this->getOrderDirPath($order_id, $order_billing_name);

        if ( ! file_exists($order_dir_name)) {
            mkdir($order_dir_name, 0777, true);
        }

        $target_dir_name = $order_dir_name . '/' . $calendar_id . '/';

        if ( ! file_exists($target_dir_name)) {
            mkdir($target_dir_name, 0777, true);
        }

        return $target_dir_name;
    }

    private function getOrderDirPath($order_id, $order_billing_name)
    {
        return CalendarControllerAdmin::getOrdersRootDir() . $order_id . '_' . CalendarControllerAdmin::replaceChar($order_billing_name);
    }

    public static function getOrderDirName($order_id, $order_billing_name)
    {
        return '/' . $order_id . '_' . CalendarControllerAdmin::replaceChar($order_billing_name) . '/';
    }

    private function generateOrderDir($order_id, $order_billing_name)
    {
        $order_dir_name = $this->getOrderDirPath($order_id, $order_billing_name);

        if ( ! file_exists($order_dir_name)) {
            mkdir($order_dir_name, 0777, true);
        }

        return $order_dir_name;
    }

    private static function replaceChar($str)
    {
        $a = array('-', ',', '.', '', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
        $b = array( '',  '',  '',  '', 'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
        return strtolower(str_replace(' ', '_', str_replace($a, $b, $str)));
    }

    public static function getOrdersRootDir()
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/generatedPDF/';
    }

    private function getCalendarFromList($calendars, $calendar_id)
    {
        for ($i = 0; $i < count($calendars); $i++)
        {
            if ($calendars[$i]['cal_id'] == $calendar_id) {
                return $calendars[$i];
            }
        }
    }

    private function getMonthPdfPreviewUrl($month, $calendar_id, $series)
    {
        $url = array();

        $url[] = "https://www.vlastnykalendar.sk/index.php?option=com_calendar&view=ajax_pdfcreator&layout=default&calendar=".$calendar_id."&tmpl=component&month=".$month."&series=".$series;

        return $url;
    }

    private function generatePdf($calendar_id, $calendar_quantity, $month_preview_url, $calendar_type, $month, $target_dir, $series)
    {
        for($i = 0; $i < count($month_preview_url); $i++)
        {
            $month_preview_html = file_get_contents($month_preview_url[$i]);

            $month_info = $this->model->loadMonth(array( 'id' => $calendar_id, 'current_month' => $month));
            
            $calendar_pdf_sizes = unserialize(CAL_PDF_SIZES_TOTAL);

            $paper_size = array(0, 0, $calendar_pdf_sizes[$calendar_type]['wpoints'], $calendar_pdf_sizes[$calendar_type]['hpoints'] + 14.70);

            $options = new Options();
            $options->setIsRemoteEnabled(true);
            $options->setDefaultPaperSize($paper_size);
            $options->setDpi(300);
            $options->setDefaultFont('Courier');

            try {
                $pdf = new DOMPDF($options);
                $pdf->setPaper($paper_size);
                $pdf->loadHtml($month_preview_html);
                $pdf->render();
            } catch (Exception $s) {
                
            }

            $page_number = $month == 'cover' ? '00' : str_pad(date_parse($month)['month'], 2, '0', STR_PAD_LEFT);

            $file_location = $target_dir
                            . $page_number . $series
                            . "_" . $calendar_type
                            . "_" . $month
                            . "_" .$calendar_quantity . "ks.pdf";

            if ( file_exists($file_location)) {
                unlink($file_location);
            }

            var_dump($file_location);

            file_put_contents($file_location, $pdf->output());
        }

        jexit();
    }

    public static function getOrderFolderPath($order_id)
    {
        $scan_orders_folder = scandir(CalendarControllerAdmin::getOrdersRootDir());

        foreach ($scan_orders_folder as $order_folder) {
            if ($order_folder == '.' || $order_folder == '..') {
                continue;
            }

            if (is_dir(CalendarControllerAdmin::getOrdersRootDir() . $order_folder) && preg_match('/^'.$order_id.'_/', $order_folder)) {
                return CalendarControllerAdmin::getOrdersRootDir() . $order_folder . '/';
            }
        }

        return '';
    }

    public function updateCalendarSale()
    {
        $calendar_type = $this->input->post->get('calendar_type', '');
        $discount = $this->input->post->get('discount', '0');
        $valid_from = $this->input->post->get('valid_from', '0000-00-00');
        $valid_till = $this->input->post->get('valid_till', '0000-00-00');

        $this->model->updateCalendarSale($calendar_type, $discount, $valid_from, $valid_till);

        jexit();
    }

    public static function getInvoiceDownloadUrl($order_id, $invoice_type)
    {
        foreach (scandir(CalendarControllerAdmin::getOrdersRootDir()) as $order_folder) {
            if ($order_folder == '.' || $order_folder == '..') {
                continue;
            }

            if (is_dir(CalendarControllerAdmin::getOrdersRootDir() . $order_folder) && preg_match('/^'.$order_id.'_/', $order_folder)) {
                return '/generatedPDF/' . $order_folder . '/' . $invoice_type . '_' . str_pad($order_id, 4, '0', STR_PAD_LEFT) . '.pdf';
            }
        }

        return '';
    }

    public function calendarMonthsLinkGenerator()
    {
        $calendar_id = $this->input->get('calendar', 0);
        $months = ['cover','january','february','march','april','may','june','july','august','october','november','september','december'];

        for ($i = 0; $i < 13; $i++) {
            $link = "https://www.vlastnykalendar.sk/index.php?option=com_calendar&view=ajax_pdfcreator&layout=default&tmpl=component"
                . "&calendar=" . $calendar_id
                . "&month=" . $months[$i]
                . "&series=0";

            echo "<a href='" . $link . "' style='font-size: 65px;'>" . $months[$i] . "<a/><br/>";
        }

        jexit();
    }

    public function createDepoOrder() {
        if ( ! Permissions::isUserAdmin($this->user_id)) {
            jexit('Only admins can run this action');
        }

        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'raw');

        if ($id == 0) {
            return;
        }

        $data = $this->modelAdmin->getOrderDetail($id);
        $order = $data['order'];

        // osobne vyzdvihnutie na odbernom mieste
        if ($order['transport_method'] === 'depo') {
            $depoPlace = $this->getDepoPlaceSelected($order['depo_place_id']);
            $data = array(
                'target'   => $depoPlace['id'],
                'recipient_name'  => $order['shipping_name'],
                'recipient_phone' => str_replace("+","", $order['shipping_phone']),
                'recipient_email' => $order['billing_mail']
            );
        }
        // Dorucenie na adresu kuerirom
        else if ($order['transport_method'] === 'courier') {
            $data = array(
                'recipient_name'     => $order['shipping_name'],
                'recipient_street'   => $order['shipping_address'],
                'recipient_number'   => $order['shipping_address_number'],
                'recipient_zip'      => $order['shipping_zip'],
                'recipient_city'     => $order['shipping_city'],
                'recipient_phone'    => str_replace("+","", $order['shipping_phone']),
                'recipient_email'    => $order['billing_mail'],
                'recipient_name'     => $order['shipping_name'],
                'deliver_to_address' => 1,
                'pickup_from_address' => 1
            );
        } else {
            JFactory::getApplication()->redirect(JRoute::_('/index.php/component/calendar/?view=admin&layout=order&id=' . $id));
        }

        if ($order['payment_method'] === 'card') {
            $data['cod'] = 0;
        } else {
            $data['cod'] = Price::applyDiscount($order['price_calendars'], $order['discount']) + $order['price_shipping_and_packing'];
        }

        $data['insurance'] = Price::applyDiscount($order['price_calendars'], $order['discount']) + $order['price_shipping_and_packing'];
        $data['sender_reference'] = $order['order_id'];

        $this->uploadDepoOrder($data, $order['order_id']);

        // JFactory::getApplication()->redirect(JRoute::_('/index.php/component/calendar/?view=admin&layout=order&order_id=' . $id));
    }

    public function createRemaxOrder() {
        if (!Permissions::isUserAdmin($this->user_id)) {
            jexit('Only admins can run this action');
        }

        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'raw');

        if ($id == 0) {
            return;
        }

        $data = $this->modelAdmin->getOrderDetail($id);
        $order = $data['order'];

        $tomorrow = time() + 86400;

        $data = array(
            'login'               => CalendarConstants::$REMAX_USER,
            'password'            => CalendarConstants::$REMAX_PASSWORD,
            's_odos_nazov'        => CalendarConstants::$ALDO_NAME,
            's_odos_mesto'        => CalendarConstants::$ALDO_CITY,
            's_odos_ulica_cislo'  => CalendarConstants::$ALDO_ADDRESS,
            's_odos_psc'          => CalendarConstants::$ALDO_ZIP,
            's_odos_stat'         => CalendarConstants::$ALDO_COUNTRY_CODE,
            's_odos_kontakt'      => CalendarConstants::$ALDO_PHONE,
            "s_odos_kon_osoba"      => CalendarConstants::$ALDO_NAME,
            "s_odos_poznamka"       => '',

            's_prij_nazov'        => $order['shipping_name'],
            's_prij_mesto'        => $order['shipping_city'],
            's_prij_ulica_cislo'  => $order['shipping_address'] . ' ' . $order['shipping_address_number'],
            's_prij_psc'          => $order['shipping_zip'],
            's_prij_stat'         => 'SK',
            's_prij_kontakt'      => $order['shipping_phone'],
            's_prij_kon_osoba'    => $order['shipping_name'],
            's_prij_poznamka'     => '',

            's_zas_hodnota'       => '1',
            's_zas_hmotnost'      => '1',
            's_pocet_kusov'       => '1',
            's_variabilny_symbol' => $order['order_id'],
            's_variabilny_symbol_dobierka' => $order['order_id'],

            'odos_den'  => '',
            'odos_mes'  => '',
            'odos_rok'  => '',
            'odos_hod'  => '',
            'odos_min'  => '',
            'odos_hod2' => '',
            'odos_min2' => '',
            'prij_den'  => date("d", $tomorrow),
            'prij_mes'  => date("m", $tomorrow),
            'prij_rok'  => date("Y", $tomorrow),
            'prij_hod'  => '08',
            'prij_min'  => '00',
            'prij_hod2' => '18',
            'prij_min2' => '00',

            's_typ'               => 'balik',
            's_popis'             => 'Kalendar zasielka',
            's_dodaci_list'       => 'nie',
            's_uhrada'            => 'fakt',
            's_dobierka'          => Price::applyDiscount($order['price_calendars'], $order['discount']) + $order['price_shipping_and_packing'],
            's_sluzba'            => 'SK Pack',
            's_dobierka_ucet'     => CalendarConstants::$ALDO_BANK_IBAN,

            's_odos_notif_email'           => '',
            's_prij_notif_email'           => $order['billing_mail'],
            's_prij_notif_zas_stav'        => '',
            's_prij_notif_zas_stav_email'  => '',

            's_doplnkove_sluzby'       => '',
            's_id_pobocky_zasielkovna' => '',
            's_dobierka_mena'          => 'EUR',
        );

        try {
            $client = new SoapClient(CalendarConstants::$REMAX_ADD_ORDER);
            $result = $client->__soapCall('pridajZakazku', $data);
            $this->model->updateRemaxResult($id, $result);
            JFactory::getApplication()->redirect(JRoute::_('/index.php/component/calendar/?view=admin&layout=order&order_id=' . $id));
        } catch (SoapFault $e) {
            echo $e->getMessage();
        }
    }

    private function uploadDepoOrder($data, $id) {
        $curl = curl_init('https://admin.depo.sk/v2/api/packages/send');

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic aW5mb0B2bGFzdG5hLWZvdG9rbmloYS5zazpmb3Rva25paGEwMQ=='
        ));

        $result = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $json = json_decode($result, true);

        echo '<pre>';
        var_dump($json);
        echo '</pre>';

        if ($status == 200 && $json != null) {
            echo $id;
            echo $json['number'];
            $this->model->updateDepoNumber($id, $json['number']);
        }
    }

    private function getDepoPlaceSelected($depoPlaceId) {
        $curl = curl_init('https://admin.depo.sk/v2/api/places/selected');

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array('order' => $depoPlaceId)));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic aW5mb0B2bGFzdG5hLWZvdG9rbmloYS5zazpmb3Rva25paGEwMQ=='
        ));

        $result = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $json = json_decode($result, true);

        if ($status == 200 && $json != null) {
            return $json;
        }

        return null;
    }
}