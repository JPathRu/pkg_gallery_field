<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Fields.Media
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::import('components.com_fields.libraries.fieldsplugin', JPATH_ADMINISTRATOR);
JLoader::import('components.com_fields.libraries.fieldslistplugin', JPATH_ADMINISTRATOR);

/**
 * Fields Media Plugin
 *
 * @since  3.7.0
 */
class PlgFieldsGallery extends FieldsListPlugin
{
    /**
     * Transforms the field into a DOM XML element and appends it as a child on the given parent.
     *
     * @param   stdClass    $field   The field.
     * @param   DOMElement  $parent  The field node parent.
     * @param   JForm       $form    The form.
     *
     * @return  DOMElement
     *
     * @since   3.7.0
     */
    public function onCustomFieldsPrepareDom($field, DOMElement $parent, JForm $form)
    {
        $form->addFieldPath(JPATH_ROOT.'/plugins/fields/gallery/elements');

        $fieldNode = parent::onCustomFieldsPrepareDom($field, $parent, $form);

        if (!$fieldNode)
        {
            return $fieldNode;
        }

        $fieldNode->setAttribute('hide_default', 'true');
        $fieldNode->setAttribute('directory', $fieldNode->getAttribute('directory'));
        $fieldNode->setAttribute('validate', 'notempty');
        $fieldNode->setAttribute('filter', 'raw');
        return $fieldNode;
    }


    function onUserBeforeDataValidation($form, &$data)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
			->select('name')
			->from('#__fields')
			->where('type = '.$db->quote('gallery'));
        $aliases = $db->setQuery($query)->loadColumn();
		
		if(!is_array($aliases) || !count($aliases))
		{
            return;
        }
		
		foreach ($data['com_fields'] as $k => $v)
		{
			if(in_array($k, $aliases) && (is_array($v)))
			{
                $newValues = array();
				foreach ( $v as $item )
				{
                    $newValues[] = $item;
                }
                $data['com_fields'][$k] = json_encode($newValues);
            }
        }
    }
}
