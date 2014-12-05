<?php defined('_JEXEC') or die('Restricted access'); 
/**
* @copyright	Copyright (C) 2008-2009 CMSJunkie. All rights reserved.
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user		= JFactory::getUser();
$userId		= $user->get('id');

?>

<div class="printbutton">
	<a onclick="window.print()" href="javascript:void(0);">Print</a>
</div>

<div>
	<table width="100%">
		<tr>
			<td> <?php echo $this->item->reservationInfo ?></td>
		</tr>
		<tr>
			<td><br />
				<table style="width: 100%;" border="0" cellspacing="0"
					cellpadding="0">
					<thead>
						<tr>
							<th
								style="padding: 5px 9px 6px 9px; border: 1px solid #bebcb7; border-bottom: none; line-height: 1em;"
								align="left" bgcolor="#d9e5ee" width="48.5%"><?php echo JText::_('LNG_GUEST_INFORMATIONS',true);?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td
								style="padding: 7px 9px 9px 9px; border: 1px solid #bebcb7; border-top: 0; background: #f8f7f5;"
								valign="top"> <?php echo $this->item->billingInformation ?></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td><br />
				<table style="width: 100%;" border="0" cellspacing="0"
					cellpadding="0">
					<thead>
						<tr>
							<th
								style="padding: 5px 9px 6px 9px; border: 1px solid #bebcb7; border-bottom: none; line-height: 1em;"
								align="left" bgcolor="#d9e5ee" width="48.5%"><?php echo JText::_('LNG_PAYMENT_INFORMATION',true);?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td
								style="padding: 7px 9px 9px 9px; border: 1px solid #bebcb7; border-top: 0; background: #f8f7f5;"
								valign="top"><?php echo $this->item->paymentInformation ?></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</table>
</div>
 