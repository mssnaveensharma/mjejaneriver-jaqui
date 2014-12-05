<?php
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

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

class JHotelReservationModelEnvironmentTypes extends JModelLegacy
{
	
	function __construct()
	{
		parent::__construct();
	}
	
	function updateEnvironments()
	{
		$ret = true;
		$hotelId = $_POST['hotelId'];
		if( $ret == true )
		{
			$db = JFactory::getDBO();
			$query = "START TRANSACTION";
			$db->setQuery($query);
			$db->queryBatch();
			if( $ret == true )
			{
				$opt_ids = $_POST['environmentIds'];
				$db->setQuery (	" DELETE FROM #__hotelreservation_hotel_environments
										WHERE id NOT IN (".implode(',', $opt_ids).")");

				if (!$db->query() )
				{
					// dmp($db);
					$ret = false;
					$e = 'INSERT / UPDATE sql STATEMENT error !';
				}

				foreach($_POST['environmentNames'] as $key => $value )
				{


					// dmp($key);
					$recordId 			= isset($_POST['environmentIds'][$key]) ?trim($_POST['environmentIds'][$key]) : 0;
					$recordName			= trim($_POST['environmentNames'][ $key ]);


					$db->setQuery( "
												INSERT INTO #__hotelreservation_hotel_environments
												(
													id,
													name
												)
												VALUES
												(
													'$recordId',
													'$recordName'
													
												)
												ON DUPLICATE KEY UPDATE
													id 				= '$recordId',
													name			= '$recordName'
												" );
					//dmp($db);
					if (!$db->query() )
					{
						// dmp($db);
						$ret = false;
						$e = 'INSERT / UPDATE sql STATEMENT error !';
					}

				}
			}

			if( $ret == true )
			{
				$query = "COMMIT";
				$db->setQuery($query);
				$db->queryBatch();
				$m="Environments Saved Successfully!";
			}
			else
			{
				$query = "ROLLBACK";
				$db->setQuery($query);
				$db->queryBatch();
			}


		}

		$buff 		= $ret ? $this->getHTMLContentHotelEnvironments($hotelId) : '';
			
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<room_statement>';
		echo '<answer error="'.($ret ? "0" : "1").'" errorMessage="'.$e.'" mesage="'.$m.'" content_records="'.$buff.'" />';
		echo '</room_statement>';
		echo '</xml>';
		exit;
	}
	function getHTMLContentHotelEnvironments($hotelId)
	{
		$db = JFactory::getDBO();
		$db->setQuery( "
										SELECT 
											*
										FROM #__hotelreservation_hotel_environments
										ORDER BY name
										" );
		$environments 	= $db->loadObjectList();
		$db->setQuery( "
												SELECT 
													*
												FROM #__hotelreservation_hotel_environment_relation where hotelId=".$hotelId );
		$selectedEnvironments 	= $db->loadObjectList();
	
		$buff = $this->displayEnvironments($environments, $selectedEnvironments);
		//var_dump($buff);
		return htmlspecialchars($buff);
	}
	function displayEnvironments($environments, $selectedEnvironments){
		ob_start();
		?>
				<select id="environments" multiple="multiple" name="environments[]">
					<option value=""><?php echo JText::_('LNG_SELECT_EMVIRONMENT',true); ?></option>
					<?php
					foreach( $environments as $environment )
					{
						$selected = false;
						foreach( $selectedEnvironments as $selectedEnvironment ){
							if($environment->id == $selectedEnvironment->environmentId)
							$selected =true;
						}
						?>
						<option <?php echo $selected? 'selected="selected"' : ''?> 	value='<?php echo $environment->id?>'><?php echo $environment->name ?></option>
						<?php
						}
						?>
				</select>
				<?php 
				$buff = ob_get_contents();
				ob_end_clean();
				return $buff;
	}
}
?>