<?php
// ---------------------- Here are some constants you need to change for your environment
define('MY_URL', 			"http://mycompany.mojohelpdesk.com");
define('MY_KEY', 			"ec91417dddfd7jhgkjhgkjhgjkhg53dce59058932");
define('NUM_CUSTOM_FIELDS', 11);					//If you have custom fields, enter the number of custom fields in your schema

function splitn($string, $needle, $offset)			// This function is to split the description field into 2 strings
{													// We split it this way so we can process the custom_fields separately from the actual description
    $newString = $string;							// The point of the split is the $offset'th occurrence of $needle in $string
    $totalPos = 0;
    $length = strlen($needle);
    for($i = 0; $i < $offset; $i++)
    {
        $pos = strpos($newString, $needle);

        // If you run out of string before you find all your needles
        if($pos === false)
            return false;
        $newString = substr($newString, $pos+$length);
        $totalPos += $pos+$length;
    }
    return array(substr($string, 0, $totalPos-$length),substr($string, $totalPos));
}

// getTicketList() returns an array with all built-in and custom fields.
// You can pass getTicketList 2 optional variables.  The search string and the number of results
// You can see an example of how to format these arguments in the defaults below
// Format the search string like you would in the search in Mojo

function getTicketList($searchString = "status_id:(<60)?sf=created_on&r=0", $numResults = 10)
{
	$curlString = MY_URL . "/api/tickets/search/" . $searchString . "&per_page=" . $numResults . "&access_key=" . MY_KEY;  // Creates the cURL URL from the arguments

	$ch = curl_init();								// Create the cURL client
	curl_setopt($ch, CURLOPT_URL, $curlString);		// Set the cURL URL
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	// Tell cURL to return a string instead of outputting it directly
	curl_setopt($ch, CURLOPT_HTTPHEADER,array('Accept: application/xml','Content-type: application/xml'));	// Set the proper headers
	$output = curl_exec($ch);						// Execute the query					
	curl_close($ch);								// Clean up after yourself
	$xml = new SimpleXMLElement($output);			// Turns the XML into an array of variables
	$ticketList = array();							// This will be our "corrected" array with custom_fields set correctly
	$ticketi=0;										// The index for our ticket array

	foreach ($xml as $ticket) {										// An Array of <TICKET>s is in XML.  This foreach loop cycles through each ticket 
		foreach ($ticket as $key => $val) {							// This array contains the data
			if($key=="description") {								// We need to parse out the description field into custom_fields and the actual description
				$splitString=splitn($val, PHP_EOL, NUM_CUSTOM_FIELDS);				// Split the description field into custom_fields and description
				$description = explode(PHP_EOL,$splitString[0]);	// Break the custom_fields apart by the newline character
				foreach ($description as $desckey => $descval) {	// Run through each line of the custom_fields
					$strLen = strlen($descval);
					$strPos = stripos($descval, ": ");
					$strKey = substr($descval, 0, $strPos);
					$strVal = substr($descval, $strPos+2, $strLen);
					$ticketList[$ticketi][$strKey] = $strVal;		// Create array entry in the ith ticket for the custom_field named $strKey with value $strVal
					
				}
				$ticketList[$ticketi]["description"] = $splitString[1]; // Create the actual description entry in the ticket
			} else {												// If it's not the description, process it normally
				$ticketList[$ticketi][$key] = $val;
			}
		}
		$ticketi++;
	}

	return $ticketList;
}

function ticketCount($ticketList)
{
	$myCount = 0;
	foreach( $ticketList as $temp )
	{
		$myCount++;
	}
	return $myCount;
}
?>
