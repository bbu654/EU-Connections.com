<?php 
header("Content-Type: text/x-vCalendar");
function calculate_general_meeting_day( $meetingmonth ) {
	// calculates the date of the next general meeting
	while ( date( "w" , $meetingmonth ) != 3 ) :
		//	add 86,400 seconds to the timestamp (the number of seconds in 1 day)
		$meetingmonth += 86400 ;
	endwhile ;
	
	switch ( date( "m" , $meetingmonth ) ) {
		case "11" :
		//	add 1,209,600 seconds to the timestamp (the number of seconds in 1 week)
		$meetingmonth += 604800 ;
			break ;
		case "12" :
		//	add 1,209,600 seconds to the timestamp (the number of seconds in 1 week)
		$meetingmonth += 604800 ;
			break ;
		case "2" :
		//	add no seconds to the timestamp for February
		$meetingmonth += 0 ;
			break ;
		case "3" :
		//	add 1,209,600 seconds to the timestamp (the number of seconds in 1 week)
		$meetingmonth += 604800 ;
			break ;
		default:
		//	add 1,209,600 seconds to the timestamp (the number of seconds in 2 weeks)
		$meetingmonth += 1209600 ;
			break ;
	}
	return $meetingmonth ;
} 
$today = mktime( 0 , 0 , 0 ) ;
$thismonth = mktime( 0 , 0 , 0 , date( "m" ) , 1 ) ;
if ( $today < calculate_general_meeting_day( $thismonth ) ) {
   $thismonth = calculate_general_meeting_day( $thismonth ) ;
} else {
   $thismonth = mktime( 0 , 0 , 0 , date( "m" , $thismonth ) + 1 , 1 ) ;
   $thismonth = calculate_general_meeting_day( $thismonth ) ;
}
header("Content-Disposition: inline; filename=EUGeneralMeeting" . date( "Ymd" , $thismonth ) . ".ics") ;

$iCalFile = "BEGIN:VCALENDAR\n" ;
$iCalFile .="PRODID:-//William Clardy//EU Website 2.0 MIMEDIR//EN\n" ;
$iCalFile .="VERSION:2.0\n" ;
$iCalFile .="METHOD:PUBLISH\n" ;
$iCalFile .="BEGIN:VEVENT\n" ;
$iCalFile .="ORGANIZER:MAILTO:jhaugen@edd.ca.gov\n" ;
$iCalFile .="DTSTART:" . date( "Ymd" , $thismonth ) . "T210000Z\n" ;
$iCalFile .="DTEND:" . date( "Ymd" , $thismonth ) . "T230000Z\n" ;
$iCalFile .="LOCATION:Downtown Anaheim Community Center, 250 E. Center Street., Anaheim, CA 92805\n" ;
$iCalFile .="TRANSP:OPAQUE\n" ;
$iCalFile .="UID:\n" ;
$iCalFile .="DTSTAMP:" . date( "Ymd\This" ) . "Z\n" ;
$iCalFile .="DESCRIPTION:\\n\\n The Anaheim Chapter of\n" ;
$iCalFile .="  Experience Unlimited reminds you of our " . date( "F Y" , $thismonth ) . " General Membership\n" ;
$iCalFile .="  Meeting.\\n\\nEvent Title: Experience Unlimited " . date( "F Y" , $thismonth ) . " General\n" ;
$iCalFile .="  Membership Meeting\\nDate: " . date( "F j, Y" , $thismonth ) . "\\nTime: 1:00 PM Pacific Time\\n\n" ;
$iCalFile .=" \\nATTENDANCE INSTRUCTIONS\\n - Wear appropriate business attire.\\n - Be on\n" ;
$iCalFile .="  time.\\n - Be prepared to participate in the meeting.\\n - Remember that\n" ;
$iCalFile .="  the meeting lasts until 3 o'clock.\\n\\nPARKING\\n - Available in\n" ;
$iCalFile .="  multi-level parking structure on the north side of Center Street.\\n -\n" ;
$iCalFile .="  Available in single-level parking lot east of Community Center on south\n" ;
$iCalFile .="  side of Center Street.\\n\\n\n" ;
$iCalFile .="SUMMARY:Experience Unlimited " . date( "F Y" , $thismonth ) . " General Membership Meeting\n" ;
$iCalFile .="PRIORITY:5\n" ;
$iCalFile .="CLASS:PUBLIC\n" ;
$iCalFile .="BEGIN:VALARM\n" ;
$iCalFile .="TRIGGER:-PT60M\n" ;
$iCalFile .="ACTION:DISPLAY\n" ;
$iCalFile .="DESCRIPTION:Reminder\n" ;
$iCalFile .="END:VALARM\n" ;
$iCalFile .="END:VEVENT\n" ;
$iCalFile .="END:VCALENDAR";
echo $iCalFile ;
?>
