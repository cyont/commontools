				<?php
					// QUERY: get all the matching display blocks from the sequential feed table
					$result = mysql_query("SELECT * FROM sequentialfeed WHERE displayBlockID = " . $_GET['displayBlockID'] . "");
					// loop through the results
					while ($displayBlocks = mysql_fetch_array($result)) {
						
						// assign the start and end dates
						$startDate = $displayBlocks['startDate'];
						$endDate = $displayBlocks['endDate'];

						// loop through the date range
						while ($startDate <= $endDate) {
							
							// does the start date = today?
							if ($startDate == $dayOfWeek) {
								
								// QUERY: get the specific resource info
								$resultDayResources = mysql_query("SELECT * FROM resource WHERE id = " . $displayBlocks['resourceID'] . "");
								$resources = mysql_fetch_array($resultDayResources);
								echo '<li><a href="edit_resource.php?id=' . $displayBlocks['resourceID'] . '">' . $resources['resourceName'] . '</a></li>';
							} // END IF ($startDate == $date[0])
							
							// increment to next day
							$nextDay = strtotime('+1 day', strtotime($startDate));
							$startDate = date('Y-m-d',$nextDay);
							
						} // END WHILE ($startDate <= $endDate)

					} // END WHILE ($displayBlocks = mysql_fetch_array($result))
					
				?>
