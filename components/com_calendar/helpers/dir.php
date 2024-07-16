<?php
/**
 * @package			Calendar component
 * @subpackage	Dir Helper
 */

defined('_JEXEC') or die('Restricted access');

class DirHelper 
{
	
	
	/*
	 * Create several folders
	 *
	 * @param <string> $calendar_id : id of created calendar
	 * @return <void>
	 */
	public static function createUploadFolder($user_id, $calendar_id)
	{
		$folders = array(
			$user_id, 
			$user_id . '/img', 
			$user_id . '/img_backup', 
			$user_id . '/img_thumbs'
		);
		
		$user_folder = CAL_UPLOAD_SERVER . $user_id;
		
		if (!file_exists($user_folder))
		{
			mkdir($user_folder);
			chmod($user_folder, 0775);
		}
		
		for ($i=0; $i<count($folders); $i++)
		{
			$folder = CAL_UPLOAD_SERVER . $folders[$i];
			
			if ( ! file_exists($folder))
			{
				mkdir($folder);
				chmod($folder, 0775);
			}
		}
	}

	public static function removeAllFileFromDir($dir_path)
    {
        $scan = @scandir($dir_path);
        if ($scan) {
            echo 'Cleaning dir: ' . $dir_path . '<br/>';
            foreach($scan as $index => $file) {
                $file_path = $dir_path . $file;
                if (in_array($file, array('.','..')) || is_dir($file_path)) {
                    continue;
                }
                $result = unlink($file_path);
                echo 'Removing unused file: ' . $file . '(' . $result . ') <br/>';
            }
        }
    }

    public static function removeAllFileFromDirExpect($dir_path, $exclude)
    {
        $scan = @scandir($dir_path);
        if ($scan) {
            echo 'Cleaning dir: ' . $dir_path . '<br/>';
            foreach($scan as $index => $file) {
                $file_path = $dir_path . $file;
                if (in_array($file, array('.','..')) || is_dir($file_path)) {
                    continue;
                }
                if (in_array($file, $exclude)) {
                    echo 'File skipped, file used in finished calendar: ' . $file . '<br/>';
                    continue;
                }
                $result = unlink($file_path);
                echo 'Removing unused file: ' . $file . '(' . $result . ') <br/>';
            }
        }
    }
}