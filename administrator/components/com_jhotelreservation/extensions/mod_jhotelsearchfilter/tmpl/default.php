<?php // no direct access
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


defined( '_JEXEC' ) or die( 'Restricted access' );

$identifier = explode('#', $params->get('type'));
$identifier = $identifier[1];
?>


	<div>
		<ul>
			<?php 
				foreach($items as $item){?>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_jhotelreservation') ?>?controller=search&task=searchHotels&filterParams=<?php echo $identifier.'='.$item->id?>"><?php echo $item->name?></a>
				</li>
			<?php }
			?>
		</ul>
	</div>
