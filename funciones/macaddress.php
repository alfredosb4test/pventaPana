<?php		
		ob_start(); // Turn on output buffering
		system('getmac /NH'); //Execute external program to display output
		 
		$mycom=ob_get_contents(); // Capture the output into a variable
		 
		ob_clean(); // Clean (erase) the output buffer
		$mac=trim(substr($mycom,0,19)); // Get Physical Address
		 
		echo $mac;

?>		