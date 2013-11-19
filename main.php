<DOCTYPE HTML>
<HTML>
<HEAD>
<TITLE>Mojo Helpdesk Ticket Listing Example</TITLE>
</HEAD>
<BODY>

<h1>Getting Tickets using Mojo Helpdesk API</h1>
<p>For more info, visit <a href='http://www.mojohelpdesk.com/'>www.mojohelpdesk.com</a></p>

<?php
require_once('include/mojoLibrary.php');


$myTicketList = getTicketList();		

// --------------------------Display Ticket Contents----------------------------------
print("TicketList returned " . ticketCount($myTicketList) . " tickets.<BR>");
$ticketi = 1;
foreach ($myTicketList as $ticket) {
	print("<U>Ticket #" . $ticketi . "</U><BR>" . PHP_EOL);
	foreach ($ticket as $key => $val) {
		print("><B>" . $key . "</B>: " . $val . "<BR>" . PHP_EOL);
	}
	$ticketi++;
	print("<BR>");
}
?>
</BODY>
</HTML>
