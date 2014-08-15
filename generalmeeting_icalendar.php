<?php 
header("Content-Type: text/x-vCalendar");

require_once( "include/eugroup.class.php" ) ;
require_once( "include/eumember.class.php" ) ;
require_once( "include/eumeeting.class.php" ) ;
	
function calculate_general_meeting( $dbhost , $dbname , $tableprefix , $dbuser , $dbpw ) {
 	  mysql_connect( $dbhost, $dbuser, $dbpw ) or die( mysql_error() ) ;
    mysql_select_db( $dbname ) or die( mysql_error() ) ;
    $select_sql = "SELECT meeting_id , " . $tableprefix . "groups.group_id , group_name , start_time ," ;
    $select_sql .= "\n  duration , address , room , comments " ;
    $select_sql .= "\nFROM " . $tableprefix . "meetings " ;
    $select_sql .= "\n  INNER JOIN " . $tableprefix . "groups " ;
    $select_sql .= "\n  ON " . $tableprefix . "meetings.group_id = " . $tableprefix . "groups.group_id " ;
    $select_sql .= "\nWHERE" ;
    $select_sql .= "\n  start_time = ( SELECT MIN( start_time ) FROM " . $tableprefix . "meetings WHERE start_time > now() )" ;
    $select_sql .= "\n  AND euconnect_groups.group_id = 1 " ;
    $result = mysql_query( $select_sql ) or die( mysql_error() ."<br /><pre>" . $select_sql ."<pre>" ) ;
    //echo mysql_num_rows( $result ) . " rows returned" ;
  	if ( mysql_num_rows( $result ) == 1 ) {
  		$meeting_row = mysql_fetch_assoc( $result ) ;
  		$next_meeting = new eumeeting() ;
  		$next_meeting->meeting_id = $meeting_row[ "meeting_id" ] ;
      $next_meeting->group->group_id = $meeting_row[ "group_id" ] ;
      $next_meeting->group->group_name = $meeting_row[ "group_name" ] ;
	    $next_meeting->setdateODBC( $meeting_row[ "start_time" ] ) ;
      $next_meeting->duration = $meeting_row[ "duration" ] ;
      $next_meeting->address = $meeting_row[ "address" ] ;
      $next_meeting->room = $meeting_row[ "room" ] ;
      $next_meeting->comments = $meeting_row[ "comments" ] ;
  		return $next_meeting ;
	}
	return $next_meeting ;
}

//	site-wide parameters
	$s_servername = strtolower( $_SERVER["SERVER_NAME"] )	;
	$s_agentname = "Anaheim Experience Unlimited"	;
	$s_agentemail = "eudropzone@hotmail.com"	;
	// $test_env = 1 ;
	// find out where we are on the server
	$current_script = substr( strrchr ( $_SERVER[ "SCRIPT_NAME" ] , "/" ) , 1) ;
	$current_path = str_replace ( $current_script , "" , $_SERVER[ "SCRIPT_NAME" ]  ) ;


//	calendar settings
  // set time zone
  $timezone = "U" ;
  
//	MySQL database settings
	$dbhost = "localhost" ;			// database server
	$dbname = "eu-connections_org_-_main" ;		// database name
	$tableprefix = "euconnect_" ;		//  Prefix added to data table names.
	$dbuser = "euconnect" ;   	// database username
	$dbpw = "tango25" ;   			// database password



$thismeeting = calculate_general_meeting( $dbhost , $dbname , $tableprefix , $dbuser , $dbpw ) ;
$startday = str_pad( $thismeeting->start_time[ "year" ] , 2, "0" , STR_PAD_LEFT ) . str_pad( $thismeeting->start_time[ "mon" ] , 2, "0" , STR_PAD_LEFT ) . str_pad( $thismeeting->start_time[ "mday" ] , 2, "0", STR_PAD_LEFT) ;
$starttime = str_pad( $thismeeting->start_time[ "hours" ] , 2 , "0" , STR_PAD_LEFT ) . str_pad( $thismeeting->start_time[ "minutes" ] , 2 , "0" , STR_PAD_LEFT ) . str_pad( $thismeeting->start_time[ "seconds" ] , 2 , "0" , STR_PAD_LEFT ) . $timezone ;
$endtime = str_pad( ( $thismeeting->start_time[ "hours" ] + $thismeeting->duration ) , 2, "0", STR_PAD_LEFT) . str_pad( $thismeeting->start_time[ "minutes" ] , 2, "0", STR_PAD_LEFT) . str_pad( $thismeeting->start_time[ "seconds" ] , 2, "0", STR_PAD_LEFT) . $timezone ;
header("Content-Disposition: inline; filename=EUGeneralMeeting" . $startday . ".ics") ;
$iCalFile = "BEGIN:VCALENDAR\n" ;
$iCalFile .="PRODID:-//William Clardy//EU Website 2.0 MIMEDIR//EN\n" ;
$iCalFile .="VERSION:2.0\n" ;
$iCalFile .="METHOD:PUBLISH\n" ;
$iCalFile .="BEGIN:VEVENT\n" ;
$iCalFile .="ORGANIZER:MAILTO:jhaugen@edd.ca.gov\n" ;
$iCalFile .="DTSTART:" . $startday . "T" . $starttime . "\n" ;
$iCalFile .="DTEND:" . $startday . "T" . $endtime . "\n" ;
$iCalFile .="LOCATION:" . $thismeeting->room . ", " . $thismeeting->address . "\n" ;
$iCalFile .="TRANSP:OPAQUE\n" ;
$iCalFile .="UID:\n" ;
$iCalFile .="DTSTAMP:" . date( "Ymd\This" ) . "Z\n" ;
$iCalFile .="DESCRIPTION:\\n The Anaheim Chapter of\n" ;
$iCalFile .="  Experience Unlimited reminds you of our " . $thismeeting->start_time[ "month" ] . " " . $thismeeting->start_time[ "year" ] . " General Membership\n" ;
$iCalFile .="  Meeting.\\n\\nEvent Title: Experience Unlimited " . $thismeeting->start_time[ "month" ] . " " . $thismeeting->start_time[ "year" ] . " General\n" ;
$iCalFile .="  Membership Meeting\\nDate: " . $thismeeting->start_time[ "month" ] . " " . $thismeeting->start_time[ "mday" ] . ", " . $thismeeting->start_time[ "year" ] . "\\n" ;
if ( $thismeeting->start_time[ "hours" ] > 12 ) {
  $iCalFile .= "Time: " . ( $thismeeting->start_time[ "hours" ] - 12 ) . ":" ;
  $iCalFile .= str_pad( $thismeeting->start_time[ "minutes" ] , 2 , "0" , STR_PAD_LEFT ) . " PM Pacific Time\\n\n" ;
}
else {
  $iCalFile .= "Time: " . $thismeeting->start_time[ "hours" ] . ":" ;
  $iCalFile .= str_pad( $thismeeting->start_time[ "minutes" ] , 2 , "0" , STR_PAD_LEFT ) . " AM Pacific Time\\n\n" ;
}
$iCalFile .=" \\nATTENDANCE INSTRUCTIONS\\n - Wear appropriate business attire.\\n - Be on\n" ;
$iCalFile .="  time.\\n - Be prepared to participate in the meeting.\\n - Remember that\n" ;
$iCalFile .="  the meeting lasts " . $thismeeting->duration . " hours.\\n\\n" ;
$iCalFile .="PARKING\\n - Available in\n" ;
$iCalFile .="  multi-level parking structure on the north side of Center Street.\\n -\n" ;
$iCalFile .="  Available in single-level parking lot east of Community Center on south\n" ;
$iCalFile .="  side of Center Street.\\n\\nDIRECTIONS\\n - " ;
$iCalFile .="  http://maps.google.com/maps?daddr=" . urlencode ( $thismeeting->address ) . "\\n\\n" ;
$iCalFile .="  See you there!\\n\\n\n" ;
$iCalFile .="SUMMARY:Experience Unlimited " . $thismeeting->start_time[ "month" ] . " " . $thismeeting->start_time[ "year" ] . " General Membership Meeting\n" ;
$iCalFile .="PRIORITY:5\n" ;
$iCalFile .="CLASS:PUBLIC\n" ;
$iCalFile .="BEGIN:VALARM\n" ;
$iCalFile .="TRIGGER:-PT45M\n" ;
$iCalFile .="ACTION:DISPLAY\n" ;
$iCalFile .="DESCRIPTION:Reminder\n" ;
$iCalFile .="END:VALARM\n" ;
$iCalFile .="END:VEVENT\n" ;
$iCalFile .="END:VCALENDAR";
echo $iCalFile ;
?>
