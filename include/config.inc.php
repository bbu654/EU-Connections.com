<?php 
	session_start();
	// find out where we are on the server
$current_script = substr( strrchr ( $_SERVER[ "SCRIPT_NAME" ] , "/" ) , 1) ;
$current_path = str_replace ( $current_script , "" , $_SERVER[ "SCRIPT_NAME" ]  ) ;
if ( strpos ( strtolower( $current_path ) , "console" ) ) {
    require_once( "../include/eugroup.class.php" ) ;
    require_once( "../include/eumember.class.php" ) ;
    require_once( "../include/eumeeting.class.php" ) ;
}
else {
    require_once( "include/eugroup.class.php" ) ;
    require_once( "include/eumember.class.php" ) ;
    require_once( "include/eumeeting.class.php" ) ;
 }
	
//	site-wide parameters
$doctype = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">" ;
$s_servername = strtolower( $_SERVER["SERVER_NAME"] )	;
$s_agentname = "Anaheim Experience Unlimited"	;
$s_agentemail = "eudropzone@hotmail.com"	;
  // set time zone
  $timezone = "U" ;


//	MySQL database settings

	$dbhost = "localhost";			// database server
	$tablepre = "euconnect_";		//  Prefix added to data table names.
	$dbname = "eu-connections_org_-_main";		// database name
	$dbuser = "euconnect";   	// database username
	$dbpw = "tango25";   

	// intialize variables for access controls
	$user_uname = "" ;
	$user_uname = ( $_SESSION[ "uun" ] ) ? $_SESSION[ "uun" ] : $user_uname ;
	$user_euid = "000000" ;
	$user_euid = ( $_SESSION[ "ueuid" ] ) ? $_SESSION[ "ueuid" ] : $user_euid ;
	// force admin level 
	$user_admin = 1 ;
	$user_admin = ( $_SESSION[ "uadm" ] ) ? $_SESSION[ "uadm" ] : $user_admin ;
	// set page to home page if not set in query string
	$page_val = ( $_GET['page'] ) ? $_GET['page'] : "home" ;
	$page_val = ( $_POST['page'] ) ? $_POST['page'] : $page_val ;
	$page_name = "include/" . $page_val . ".html";
	if ( $current_script == "member.php"  || $current_script == "logoff.php" ) {
		$page_name = str_replace ( "include/", "console/" , $page_name ) ;
		$page_name = str_replace ( ".html", ".inc.php" , $page_name ) ;
 		$user_euid = ( $_GET["ueuid"] ) ? $_GET["ueuid"] : $user_euid ;
		$user_euid = ( $_POST["ueuid"] ) ? $_POST["ueuid"] : $user_euid ;
		$user_admin = ( $_POST["user_admin"] ) ? $_POST["user_admin"] : $user_admin ;
		if ( !file_exists( $page_name ) ) {
			$page_name = "console/home.html" ;
		}
	}
	elseif ( $current_script == "print.php"  || $current_script == "printpage.php" ) {
		if ( substr_count( $_SERVER["HTTP_REFERER"] , ".org/console/" ) > 0 ){
			$page_name = str_replace ( "include/", "console/" , $page_name ) ;
			$page_name = str_replace ( ".html", ".inc.php" , $page_name ) ;
	 		$user_euid = ( $_GET["ueuid"] ) ? $_GET["ueuid"] : $user_euid ;
			$user_euid = ( $_POST["ueuid"] ) ? $_POST["ueuid"] : $user_euid ;
			$user_admin = ( $_POST["user_admin"] ) ? $_POST["user_admin"] : $user_admin ;	
		}
		if ( !file_exists( $page_name ) ) {
			$page_name = "include/home.html" ;
		}
	}
	elseif ( !file_exists( $page_name ) && $current_script == "index.php" ) {
		$page_name = "include/home.html" ;
	}

//	calendar settings
  // set time zone
  $timezone = "U" ;
	// set month and year to present if month
	// and year if not received from query string
	$month = $HTTP_GET_VARS['month'];
	$year = $HTTP_GET_VARS['year'];
	$m = (!$month) ? date("n") : $month;
	$y = (!$year) ? date("Y") : $year;

//	MySQL database settings

	$dbhost = "localhost";			// database server
	$tablepre = "euconnect_";		//  Prefix added to data table names.
	$dbname = "eu-conne_main";		// database name
	$dbname = "eu-connections_org_-_main";		// database name
//	if ( strpos ( strtolower( $current_path ) , "preview" ) ) { 
//		$dbname = "eu-conne_preview";		// database name
//	}
	$dbuser = "eu-conne_query";   	// database username
	$dbpw = "eu01";   			// database password
	$dbuser = "euconnect";   	// database username
	$dbpw = "****";   			// database password

//  Language selection value (currently unused)
	$language = "en";


$printpage = str_replace ( "console/", "" , str_replace ( "include/", "" , str_replace ( ".inc" , "", str_replace ( ".html" , "", $page_name ) ) ) ) ;

// authenticate username/password
// returns: -1 if username and password is incorrect
//          0 if username exists and password is incorrect
//          1 if username and password are correct
function authenticate( $user, $pass )
{
	// values for user id and level
	global $user_uname, $user_euid, $user_admin ;
	// mySQL configuration variables
	global $dbhost , $tablepre , $dbname , $dbuser , $dbpw ;
	mysql_connect( $dbhost, $dbuser, $dbpw ) or die( mysql_error() );
	mysql_select_db( $dbname ) or die(mysql_error());
	$sql_checkuname = "SELECT euid, userlevel from " . $tablepre . "members WHERE username = \"$user\" ";
	$sql_checkpass = "SELECT euid, userlevel from " . $tablepre . "members WHERE username = \"$user\" AND pass = SHA1(\"$pass\")";
	// $sql_checkpass = $sql_checkuname ;	// dummy check for username only (remove after debugging)
	$result = mysql_query( $sql_checkpass ) or die( mysql_error() ) ;
	// if row exists -> user/pass combination is correct
	if ( mysql_num_rows( $result ) == 1 ) {
		$user_row = mysql_fetch_assoc( $result ) ;
		$user_uname = $user ;
		$user_euid = $user_row[ "euid" ] ;
		$user_admin = $user_row[ "userlevel" ] ;
		$_SESSION[ "uun" ] = $user_uname ;
		$_SESSION[ "ueuid" ] = $user_euid ;
		$_SESSION[ "uadm" ] = $user_admin ;
		return 1 ;
	}
	else {
		// check to see if user name is valid
		$result = mysql_query( $sql_checkuname ) or die( mysql_error() ) ;
		if ( mysql_num_rows( $result ) == 1 ) {
			return 0 ;
		}
		else {
			return -1 ;
		}
	}
}

function sql_to_cal( $date_str ) {
	// converts mySQL date format (yyyy-mm-dd) to U.S. date format ( day month year)
	$date_array = explode( "-" , $date_str ) ;
	$year_str = $date_array[ 0 ] ;
	$day_str = $date_array[ 2 ] ;
	switch( $date_array[ 1 ] ) {
		case "01" :
			$mon_str = "JANUARY" ;
			break ;
		case "02" :
			$mon_str = "FEBRUARY" ;
			break ;
		case "03" :
			$mon_str = "MARCH" ;
			break ;
		case "04" :
			$mon_str = "APRIL" ;
			break ;
		case "05" :
			$mon_str = "MAY" ;
			break ;
		case "06" :
			$mon_str = "JUNE" ;
			break ;
		case "07" :
			$mon_str = "JULY" ;
			break ;
		case "08" :
			$mon_str = "AUGUST" ;
			break ;
		case "09" :
			$mon_str = "SEPTEMBER" ;
			break ;
		case "10" :
			$mon_str = "OCTOBER" ;
			break ;
		case "11" :
			$mon_str = "NOVEMBER" ;
			break ;
		case "12" :
			$mon_str = "DECEMBER" ;
			break ;
	}
	return $day_str . " " . $mon_str . " " . $year_str ;
}
// function calculate_general_meeting_day
function get_general_meeting( $dbhost , $dbname , $tableprefix , $dbuser , $dbpw ) {
 	  mysql_connect( $dbhost, $dbuser, $dbpw ) or die( mysql_error() ) ;
    mysql_select_db( $dbname ) or die( mysql_error() ) ;
    $select_sql = "SELECT meeting_id , " . $tableprefix . "groups.group_id , group_name , start_time ," ;
    $select_sql .= "\n  duration , address , room , comments " ;
   // $select_sql = ", \n  DATEDIFF( start_time , CURDATE() ) AS days_until , " ;
   // $select_sql = "\n  TIMEDIFF( start_time , CURDATE() ) AS time_until " ;
    $select_sql .= "\nFROM " . $tableprefix . "meetings " ;
    $select_sql .= "\n  INNER JOIN " . $tableprefix . "groups " ;
    $select_sql .= "\n  ON " . $tableprefix . "meetings.group_id = " . $tableprefix . "groups.group_id " ;
    $select_sql .= "\nWHERE" ;
    $select_sql .= "\n  start_time = ( SELECT MIN( start_time ) FROM " . $tableprefix . "meetings WHERE start_time > CURDATE() )" ;
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
	    //print_r( $next_meeting->start_time ) ;
      $next_meeting->duration = $meeting_row[ "duration" ] ;
      $next_meeting->address = $meeting_row[ "address" ] ;
      $next_meeting->room = $meeting_row[ "room" ] ;
      $next_meeting->comments = $meeting_row[ "comments" ] ;
  		return $next_meeting ;
  	}
} 
function next_meeting_text(  ) {
	$thisday = getdate( time() ) ; 
  //echo "<br /><pre>" ;
	//print_r( $thisday ) ;
	$nextmeetingstring = "No meeting" ;
	global $dbhost , $dbname , $tablepre , $dbuser , $dbpw ;
	$thismeeting = get_general_meeting( $dbhost , $dbname , $tablepre , $dbuser , $dbpw  ) ;
	$daysremaining = $thismeeting->start_time[ "yday" ] - $thisday[ "yday" ] ;
	//print_r( $daysremaining ) ;
	switch ( $daysremaining ) {
		case 0 :
			if ( $thismeeting->start_time[ "hours" ] > $thisday[ "hours" ] ) {
    			$nextmeetingstring = "Our next general meeting starts at " . ( $thismeeting->start_time[ "hours" ] > 13 ? $thismeeting->start_time[ "hours" ] : $thismeeting->start_time[ "hours" ] - 12 )  ;
    			$nextmeetingstring .= ":" . str_pad( $thismeeting->start_time[ "minutes" ] , 2, "0", STR_PAD_LEFT) ;
    			$nextmeetingstring .= " " . ( $thismeeting->start_time[ "hours" ] > 13 ? "AM" : "PM" )  ;
    			$nextmeetingstring .= " today."  ;
			} elseif ( $thismeeting->start_time[ "hours" ] + $next_meeting->duration > $thisday[ "hours" ] ) {
				  $nextmeetingstring .= "Our general meeting started at " . ( $thismeeting->start_time[ "hours" ] > 13 ? $thismeeting->start_time[ "hours" ] : $thismeeting->start_time[ "hours" ] - 12 )  ;
    			$nextmeetingstring .= ":" . str_pad( $thismeeting->start_time[ "minutes" ] , 2, "0", STR_PAD_LEFT) ;
    			$nextmeetingstring .= " " . ( $thismeeting->start_time[ "hours" ] > 13 ? "AM" : "PM" )  ;
    			$nextmeetingstring .= " today."  ;
			} else {
				  $nextmeetingstring .= "Our general meeting started at " . ( $thismeeting->start_time[ "hours" ] > 13 ? $thismeeting->start_time[ "hours" ] : $thismeeting->start_time[ "hours" ] - 12 )  ;
    			$nextmeetingstring .= ":" . str_pad( $thismeeting->start_time[ "minutes" ] , 2, "0", STR_PAD_LEFT) ;
    			$nextmeetingstring .= " " . ( $thismeeting->start_time[ "hours" ] > 13 ? "AM" : "PM" )  ;
    			$nextmeetingstring .= " today."  ;
			}
			break ;

		case 1 :
			$nextmeetingstring = "Our next general meeting is at " . ( $thismeeting->start_time[ "hours" ] > 13 ? $thismeeting->start_time[ "hours" ] : $thismeeting->start_time[ "hours" ] - 12 )  ;
			$nextmeetingstring .= ":" . str_pad( $thismeeting->start_time[ "minutes" ] , 2, "0", STR_PAD_LEFT) ;
			$nextmeetingstring .= " " . ( $thismeeting->start_time[ "hours" ] > 13 ? "AM" : "PM" )  ;
			$nextmeetingstring .= " tomorrow, " . $thismeeting->start_time[ "weekday" ] ;
			$nextmeetingstring .= ", " . $thismeeting->start_time[ "month" ] ;
			$nextmeetingstring .= " " . $thismeeting->start_time[ "mday" ] . "." ;
			break ;
		default:
			$nextmeetingstring = "Our next general meeting is " . $daysremaining ;
			$nextmeetingstring .= " days from today, at " . ( $thismeeting->start_time[ "hours" ] > 13 ? $thismeeting->start_time[ "hours" ] : $thismeeting->start_time[ "hours" ] - 12 )  ;
			$nextmeetingstring .= ":" . str_pad( $thismeeting->start_time[ "minutes" ] , 2, "0", STR_PAD_LEFT) ;
			$nextmeetingstring .= " " . ( $thismeeting->start_time[ "hours" ] > 13 ? "AM" : "PM" )  ;
			$nextmeetingstring .= " on " . $thismeeting->start_time[ "weekday" ] ;
			$nextmeetingstring .= ", " . $thismeeting->start_time[ "month" ] ;
			$nextmeetingstring .= " " . $thismeeting->start_time[ "mday" ] . "." ;
			break ;
	}
  //echo "<pre>" ;
  $nextmeetingstring .= "<br /><a href=\"http://maps.google.com/maps?daddr=" . urlencode ( $thismeeting->address ) . "\"  target=\"_blank\">Click here for directions to the meeting</a>" ;
	return $nextmeetingstring ;
}


?>


