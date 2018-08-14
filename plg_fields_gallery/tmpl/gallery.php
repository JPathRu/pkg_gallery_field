<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Fields.Media
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

if ($field->value == '')
{
	return;
}

$class = $fieldParams->get('image_class');

if ($class)
{
	$class = ' class="' . htmlentities($class, ENT_COMPAT, 'UTF-8', true) . '"';
}

$values  = json_decode($field->value, true);
if (is_array($values) && count($values))
{
?>
<div class="row">
	<ul class="thumbnails">
		<?php
		foreach ($values as $value)
		{
			if(empty($value['image']))
			{
				continue;
			}
		?>
		<li class="span2">
			<div class="thumbnail" style="height: 300px;">
				<img style="max-height: 150px;" src="<?php echo JUri::root().$value['image']; ?>" alt="<?php echo $value['title']; ?>" <?php echo $value['attr']; ?>>
				<h3><?php echo $value['title']; ?></h3>
				<p><?php echo $value['desc']; ?></p>
			</div>
		</li>
		<?php } ?>
	</ul>
</div>
<?php
}
