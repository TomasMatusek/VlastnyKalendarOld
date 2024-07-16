<?php
/**
 * @package			Photobook component
 * @subpackage	PDF creator Helper
 */

defined('_JEXEC') or die('Restricted access');

class FileHelper 
{
	public static function getUserImages($project_id, $type = 'thumb')
	{
		if ($type == 'thumb')
		{
			$folder = '/img_thumbs/';
		}
		else if ($type == 'server')
		{
			$folder = '/img/';
		}
		
		$path_server = CAL_ROOT_SERVER . 'calendar/' . $project_id . $folder;
		
		$path_web = CAL_ROOT_WEB . '/calendar/' . $project_id . $folder;
		
		$file_path = array();
		
		if (file_exists($path_server))
		{
			$file_name = scandir($path_server);
		
			array_shift($file_name); array_shift($file_name);
			
			for ($i=0; $i<count($file_name); $i++)
			{
				$file_path[$i] = $path_web . $file_name[$i];
			}
		}
		
		return $file_path;
	}

	public static function getUserImagesAndThumbs($user_id)
    {
        $path_server = CAL_ROOT_SERVER . 'calendar/' . $user_id . '/img/';

        $path_web = CAL_ROOT_WEB . '/calendar/' . $user_id . '/img/';

        $path_web_thumbs = CAL_ROOT_WEB . '/calendar/' . $user_id . '/img_thumbs/';

        $file_path = array();

        if (file_exists($path_server))
        {
            $file_name = scandir($path_server);

            array_shift($file_name); array_shift($file_name);

            for ($i=0; $i<count($file_name); $i++)
            {
                $file_path['img'][$i] = $path_web . $file_name[$i];
                $file_path['thumb'][$i] = $path_web_thumbs . $file_name[$i];
            }
        }

        return $file_path;
    }
}