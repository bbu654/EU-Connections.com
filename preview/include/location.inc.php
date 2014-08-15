<?php 
/*function calculate_general_meeting_day( $meetingmonth ) {
	// calculates the date of the next general meeting
	while ( date( "w" , $meetingmonth ) < 3 ) :
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
		default:
		//	add 1,209,600 seconds to the timestamp (the number of seconds in 2 weeks)
		$meetingmonth += 1209600 ;
			break ;
	}
	return $meetingmonth ;
}
*/function calculate_general_meeting_day( $meetingmonth ) {
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
function next_meeting_text( $thismeeting , $nextmeeting , $today ) {
	$nextmeetingstring = "" ;
	switch ( ( $thismeeting - $today ) / 86400 ) {
		case 0 :
			if ( mktime() - $today < 47000 ) {
				$nextmeetingstring .= "Our next meeting starts at 1 o'clock today, " . date( "F j" , $thismeeting ) ;
			} else {
				$nextmeetingstring .= "Our meeting started at 1 o'clock today, " . date( "F j" , $thismeeting ) ;
			}
			break ;
		case 1 :
			$nextmeetingstring .= "Our next meeting is at 1 o'clock tomorrow, " . date( "l, F j" , $thismeeting ) ;
			break ;
		default:
			$nextmeetingstring .= "Our next meeting is " . strval( round( ( $thismeeting - $today ) / 86400 , 0 ) ) . " days from today, on " . date( "l, F j" , $thismeeting ) . "." ;
			break ;
	}
//	$nextmeetingstring .= ". The first meeting after that is scheduled for " . date( "l, F j" , $nextmeeting ) . "." ;
	return $nextmeetingstring ;
}
$today = mktime( 0 , 0 , 0 ) ;
$thismonth = mktime( 0 , 0 , 0 , date( "m" ) , 1 ) ;
$nextmonth = mktime( 0 , 0 , 0 , date( "m" ) + 1 , 1 ) ;
if ( $today < calculate_general_meeting_day( $thismonth ) ) {
   $thismonth = calculate_general_meeting_day( $thismonth ) ;
//   $nextmonth = calculate_general_meeting_day( $nextmonth ) ;
} else {
//   $thismonth = mktime( 0 , 0 , 0 , date( "m" ) + 1 , 1 ) ;
//   $nextmonth = mktime( 0 , 0 , 0 , date( "m" ) + 2 , 1 ) ;
   $thismonth = calculate_general_meeting_day( $nextmonth ) ;
//   $nextmonth = calculate_general_meeting_day( $nextmonth ) ;
}
// echo next_meeting_text( $thismonth , $nextmonth , $today ) ;
?>