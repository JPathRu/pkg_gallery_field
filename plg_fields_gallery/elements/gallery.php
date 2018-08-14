<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

class JFormFieldGallery extends \Joomla\CMS\Form\FormField
{

    protected $type = 'Gallery';
    protected $directory;
    protected $mediaLayout = 'joomla.form.field.media';

    protected function getInput()
    {
        $doc = JFactory::getDocument();
        JHtml::_('jquery.framework');
        JHtml::_('jquery.ui', array('core', 'sortable'));

        $doc->addScriptVersion(JUri::root() . 'plugins/fields/gallery/assets/js/field_gallery.js');

        $mediaRender = $this->getRenderer($this->mediaLayout);
        $headerImage = JText::_('PLG_FIELDS_GALLERY_IMAGE');
        $headerTitle = JText::_('PLG_FIELDS_GALLERY_TITLE');
        $headerDesc = JText::_('PLG_FIELDS_GALLERY_DESC');
        $headerAttr = JText::_('PLG_FIELDS_GALLERY_ATTR');
        $fill = JText::_('PLG_FIELDS_GALLERY_FILL');
        $sort = JText::_('PLG_FIELDS_GALLERY_SORT_BY_NAME');
        $deleteAll = JText::_('PLG_FIELDS_GALLERY_DELETE_ALL');

		if(!empty($this->value))
		{
            $values = json_decode($this->value, true);
        }
		else
		{
            $values = array();
        }
        $this->directory = (string) $this->element['directory'];
        $select = $this->getFoldersSelect();

        $layoutData = parent::getLayoutData();
        $html = <<<HTML
        <div class="gallery-div">
            <div style="display:flex;margin-bottom:18px;">
                <div class="input-append">
                    <span>$select <input type="hidden" class="main-dir input-xlarge" value="{$this->directory}"></span>
                    <span><input type="button" class="btn btn-success" value="$fill" onclick="fieldGallery.fill(this, '{$this->name}', '{$layoutData["id"]}')"></span>
                </div>
                <span style="display:inline-block;margin-left:18px;"><input type="button" class="btn btn-primary" value="$sort" onclick="fieldGallery.sortRows(this)"></span>
                <span style="display:inline-block;margin-left:18px;"><input type="button" class="btn btn-danger" value="$deleteAll" onclick="fieldGallery.deleteAll(this)"></span>
            </div>
         
            <table class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th>$headerImage</th>
                        <th>$headerTitle</th>
                        <th>$headerDesc</th>
                        <th>$headerAttr</th>
                        <th class="center"><input class="btn btn-small btn-success" type="button" onclick="fieldGallery.addRow(this, '{$this->name}', '{$layoutData["id"]}')" value="+"></th>
                    </tr>
                </thead>
                <tbody class="sortable">
HTML;
		foreach ( $values as $k => $value )
		{
            $title = htmlspecialchars($value['title'], ENT_COMPAT, 'UTF-8');
            $desc = htmlspecialchars($value['desc'], ENT_COMPAT, 'UTF-8');
            $attr = htmlspecialchars($value['attr'], ENT_COMPAT, 'UTF-8');
            $html .= <<<HTML
                    <tr>
                        <td class="center vcenter"><span style="cursor: move;" class="sortable-handler"><span class="icon-menu"></span></span></td>
                        <td>{$mediaRender->render($this->getMediaLayoutData($k, $value['image']))}</td>
                        <td><input class="input-xlarge title span12" type="text" name="{$this->name}[$k][title]" value="{$title}"></td>
                        <td><input class="input-xlarge span12" type="text" name="{$this->name}[$k][desc]" value="{$desc}"></td>
                        <td><input class="input-xlarge span12" type="text" name="{$this->name}[$k][attr]" value="{$attr}"></td>
                        <td class="center"><input class="btn btn-small btn-danger" type="button" onclick="fieldGallery.deleteRow(this)" value="–"></td>
                    </tr>
HTML;
        }
        $html .= <<<HTML
                </tbody>
            </table>
        </div>
        <style>.vcenter, .table td.vcenter, .table th.vcenter { vertical-align: middle; }</style>
HTML;

        $html .= '<script>' .
            'var plg_fieldGallery_notImage = \'' . JText::_('JLIB_FORM_MEDIA_PREVIEW_EMPTY') . '\';' .
            'var plg_fieldGallery_selImage = \'' . JText::_('JLIB_FORM_MEDIA_PREVIEW_SELECTED_IMAGE') . '\';' .
            'var plg_fieldGallery_selectButton = \'' . JText::_('JLIB_FORM_BUTTON_SELECT') . '\';' .
            'var plg_fieldGallery_clearButton = \'' . JText::_('JLIB_FORM_BUTTON_CLEAR') . '\';' .
            'var plg_fieldGallery_deleteAll = \'' . JText::_('JGLOBAL_CONFIRM_DELETE') . '\';' .
        '</script>';

        return $html;
    }

    private function getMediaLayoutData($num, $src="")
    {
        $asset = \JFactory::getApplication()->input->get('option');

        if (file_exists(JPATH_ROOT . '/' . $this->directory))
        {
            $folder = $this->directory;
        }
        else
        {
            $folder = '';
        }
        $data = parent::getLayoutData();
        $data['name'] = $data['name'].'['.$num.'][image]';
        $data['id'] = $data['id'].'_'.$num.'_image';
        $data['value'] = $src;
        $data['folder'] = $folder;
        $data['asset'] = $asset;
        $data['preview'] = 'tooltip';
        $data['link'] = '';
        $data['authorId'] = 0;
        $data['previewHeight'] = 500;
        $data['previewWidth'] = 500;
        return $data;
    }

    private function getFoldersSelect()
    {
        $options = array();

        $path = $this->directory;

        if (!is_dir($path))
        {
            $path = JPATH_ROOT . '/' . $path;
        }

        $path = JPath::clean($path);


        $options[] = JHtml::_('select.option', '', JText::_('JSELECT'));


        // Get a list of folders in the search path with the given filter.
        $folders = JFolder::folders($path, '', true, true);

        // Build the options list from the list of folders.
        if (is_array($folders))
        {
            foreach ($folders as $folder)
            {
                // Check to see if the file is in the exclude mask.
                if ($this->exclude)
                {
                    if (preg_match(chr(1) . $this->exclude . chr(1), $folder))
                    {
                        continue;
                    }
                }

                // Remove the root part and the leading /
                $folder = trim(str_replace($path, '', $folder), '/');

                $options[] = JHtml::_('select.option', $folder, $folder);
            }
        }

        return JHTML::_('select.genericlist', $options, '', ' class="dir-select input-xlarge"' );
    }
}
