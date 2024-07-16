<?php

defined('_JEXEC') or die('Restricted access');

class Zip
{
	public static function getOrderZipFilesPaths($order_id, $calendar_ids)
    {
        $orders_root_dir = $_SERVER['DOCUMENT_ROOT'] . '/generatedPDF/';
        $order_folder_name = Zip::getOrderFolderPath($order_id);

        $zip_files = array();
        $scan_order_folder = scandir($orders_root_dir . $order_folder_name);
        foreach ($scan_order_folder as $zip_file) {
            if ($zip_file == '.' || $zip_file == '..') {
                continue;
            }

            // cant be folder and must end with .zip extension
            if ( ! is_dir($orders_root_dir . $order_folder_name . '/' . $zip_file) && strstr($zip_file, '.zip')) {

                $zip_file_id = explode('_', $zip_file)[3];
                if ( ! in_array($zip_file_id, $calendar_ids)) {
                    continue;
                }

                $zip_file_name = $order_folder_name . '/' . $zip_file;

                $zip_files[$zip_file_id] = $zip_file_name;

                // we find all required zip files -> break
                if (count($zip_files) == count($calendar_ids)) {
                    break;
                }
            }
        }

        return $zip_files;
    }

    public static function getOrderFolderPath($order_id)
    {
        $orders_root_dir = $_SERVER['DOCUMENT_ROOT'] . '/generatedPDF/';
        $scan_orders_folder = scandir($orders_root_dir);
        $order_folder_name = '';
        foreach ($scan_orders_folder as $order_folder) {
            if ($order_folder == '.' || $order_folder == '..') {
                continue;
            }

            if (is_dir($orders_root_dir . $order_folder) && preg_match('/^'.$order_id.'_/', $order_folder)) {
                $order_folder_name = $order_folder;
                break;
            }
        }

        return $order_folder_name;
    }

    public static function getSubfolders($order_id)
    {
        $order_folder = $_SERVER['DOCUMENT_ROOT'] . '/generatedPDF/' . Zip::getOrderFolderPath($order_id) . '/';
        $scan_order_folder = scandir($order_folder);
        $folders_path = array();

        foreach ($scan_order_folder as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            $file_path = $order_folder . $file;
            if ( is_dir($file_path)) {
                array_push($folders_path, $file_path);
            }
        }

        return $folders_path;
    }

    public static function removeDirWithContent($dir_path)
    {
        $scan_calendar_folder = scandir($dir_path);

        foreach ($scan_calendar_folder as $pdf_file) {
            if ($pdf_file == '.' || $pdf_file == '..') {
                continue;
            }

            if ( ! is_dir($dir_path . '/' . $pdf_file)) {
                unlink($dir_path . '/' . $pdf_file);
            }
        }

        rmdir($dir_path);
    }
}