<?php
/**
 * @copyright	Copyright (C) 2009-2012 ACYBA SARL - All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
?>
<br  style="font-size:1px;" />
 <fieldset class='adminform'>
		<legend><?php echo JText::_('LNG_HOTEL_LANGUAGES',true) ?></legend>
	
			<table class="table table-striped adminlist"  id="itemList">
				<thead>
				<tr>
					<th class="title titlenum">
						<?php echo JText::_('LNG_NUMBER',true); ?>
					</th>
					<th class="title titletoggle">
						<?php echo JText::_('LNG_EDIT',true); ?>
					</th>
					<th class="title">
						<?php echo JText::_('LNG_NAME',true); ?>
					</th>
					<th class="title titletoggle">
						<?php echo JText::_('LNG_ID',true); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$k = 0;
					for($i = 0,$a = count($this->languages);$i<$a;$i++){
						$row = $this->languages[$i];
				?>
					<tr class="<?php echo "row$k"; ?>">
						<td align="center">
						<?php echo $i+1; ?>
						</td>
						<td  align="center">
							<?php echo $row->edit; ?>
						</td>
						<td align="center">
							<?php echo $row->name; ?>
						</td>
						<td align="center">
							<?php echo $row->language; ?>
						</td>
					</tr>
				<?php
						$k = 1-$k;
					}
				?>
			</tbody>
		</table>
	</fieldset>
