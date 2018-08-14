<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Media File Controller
 *
 * @since  1.5
 */
class MediaControllerAjax extends JControllerLegacy
{
    function get_folder_images()
    {
        header("Content-Type: application/json; charset=UTF-8 ");
        $data = array(
            'error' => 1,
            'message' => '',
            'data' => array()
        );
        $app = JFactory::getApplication();
        $dir = $app->input->getString('dir', '');

        if(empty($dir))
        {
            $data['message'] = 'Empty directory value';
            echo json_encode($data);
            $app->close();
        }

        $dir = JPATH_ROOT.'/'.$dir;
        if(!is_dir($dir))
        {
            $data['message'] = 'Directory '.$dir.' not exisit';
            echo json_encode($data);
            $app->close();
        }

        $mediaHelper = new \Joomla\CMS\Helper\MediaHelper;
        $files = JFolder::files($dir);

        if(is_array($files) && count($files))
        {
            foreach ( $files as $file )
            {
                if($mediaHelper->isImage($file))
                {
                    $data['data'][] = $file;
                }
            }
        }

        if(!count($data['data']))
        {
            $data['message'] = 'Directory '.$dir.' empty';
        }
        else
        {
            $data['error'] = 0;
        }
        echo json_encode($data);
        $app->close();
    }
}
