<?php 
	session_start();
//	site-wide parameters
	$doctype = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">" ;
	$s_servername = strtolower( $_SERVER["SERVER_NAME"] )	;
	$s_agentname = "Anaheim Experience Unlimited"	;
	$s_agentemail = "eudropzone@hotmail.com"	;
	// $test_env = 1 ;
	// find out where we are on the server
	$current_script = substr( strrchr ( $_SERVER[ "SCRIPT_NAME" ] , "/" ) , 1) ;
	$current_path = str_replace ( $current_script , "" , $_SERVER[ "SCRIPT_NAME" ]  ) ;
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
	if ( strpos ( strtolower( $current_path ) , "preview" ) ) { 
		$dbname = "eu-conne_preview";		// database name
	}
	$dbuser = "eu-conne_query";   	// database username
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


echo $doctype ;
?>

