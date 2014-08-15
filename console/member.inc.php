<?php
include_once("console/commonfunctions.inc.php") ;

mysql_connect( $dbhost, $dbuser, $dbpw ) or die( mysql_error() );
mysql_select_db( $dbname ) or die(mysql_error());
$sql_count_cat = "SELECT cat_num, cat_id, cat_descr FROM " . $tablepre . "profile_cat ORDER BY cat_descr ASC" ;
$result = mysql_query( $sql_count_cat ) or die( mysql_error() ) ;
$max_cat_num =   mysql_num_rows( $result ) ;
if ( mysql_num_rows( $result ) ) {
	while ( $row = mysql_fetch_assoc( $result ) ) {
	//	$text_array = array( $row["cat_num"] , $text_array );
		$cat_array[ $row["cat_id"] ] = $row["cat_descr"]   ;
	}
}

$row = mysql_fetch_assoc( $result ) ;

function write_droplist( $list_array , $list_name , $current_val = "  " , $none_select = 1 ){
	// function to create a drop-list with the current value selected
	// $list_array is an associative array with the option values as the keys and the display text as the value
	// $list_name is the name of the form field
	// $current_value is both self-explanatory and an optional parameter (it defaults to 2 spaces when not 
	//    explicitly set in the function call 
	// $none_select is a boolean for whether or not to display a "none selected" option, and is also optional
	global $max_cat_num ;
	// function to write out drop-down list of categories
	$droplist = "<select name=\"" . $list_name . "\" id=\"" . $list_name . "\" size=\"1\">\n" ;
	$i = 1 ;
	if ( $none_select ) {
	$droplist .= writeoption( $current_val , "  " , "none selected" ) ;
	}
	foreach( $list_array as $key => $value ){
			$droplist .= writeoption( $current_val , $key , $value ) ;
	}
	$droplist .= "</select>\n" ;
	return  $droplist  ;
}


function write_datecontrol( $control_prefix , $default_date ) {
	// create common date form input
	// date value must be passed in form of yyyy-mm-dd
	$controlstring = "" ;
	$year_array = array ( "2000" => "2000" ,
	                      "2001" => "2001" ,
	                      "2002" => "2002" ,
	                      "2003" => "2003" ,
	                      "2004" => "2004" ,
	                      "2005" => "2005" ,
	                      "2006" => "2006" ,
	                      "2007" => "2007" ,
	                      "2008" => "2008" ,
	                      "2009" => "2009" ,
	                      "2010" => "2010" ,
	                      "2011" => "2011" ,
	                      "2012" => "2012" ,
	                      "2013" => "2013" ,
	                      "2014" => "2014" ,
	                      "2015" => "2015" ) ;
	$month_array = array ( "01" => "January" ,
	                       "02" => "February" ,
	                       "03" => "March" ,
	                       "04" => "April" ,
	                       "05" => "May" ,
	                       "06" => "June" ,
	                       "07" => "July" ,
	                       "08" => "August" ,
	                       "09" => "September" ,
	                       "10" => "October" ,
	                       "11" => "November" ,
	                       "12" => "December" ) ;
	$day_array = array ( "01" => "01" ,
	                      "02" => "02" ,
	                      "03" => "03" ,
	                      "04" => "04" ,
	                      "05" => "05" ,
	                      "06" => "06" ,
	                      "07" => "07" ,
	                      "08" => "08" ,
	                      "09" => "09" ,
	                      "10" => "10" ,
	                      "11" => "11" ,
	                      "12" => "12" ,
	                      "13" => "13" ,
	                      "14" => "14" ,
	                      "15" => "15" ,
	                      "16" => "16" ,
	                      "17" => "17" ,
	                      "18" => "18" ,
	                      "19" => "19" ,
	                      "20" => "20" ,
	                      "21" => "21" ,
	                      "22" => "22" ,
	                      "23" => "23" ,
	                      "24" => "24" ,
	                      "25" => "25" ,
	                      "26" => "26" ,
	                      "27" => "27" ,
	                      "28" => "28" ,
	                      "29" => "29" ,
	                      "30" => "30" ,
	                      "31" => "31" ) ;
	$control_day = substr( $default_date , 8 , 2 ) ;
	$control_month = substr( $default_date , 5 , 2 ) ;
	$control_year = substr( $default_date , 0 , 4 ) ;
	if ( $control_day > cal_days_in_month( CAL_GREGORIAN , $control_month , $control_year ) ) {
		$controlstring = "There are not " . $control_day . " days in " . $month_array[ $control_month ] . " " . $year_array[ $control_year ] . "!" ;
	}
	else {
		$controlstring .= write_droplist( $month_array, $control_prefix . "_month" , $control_month , 0 ) . "&nbsp;" ;
		$controlstring .= write_droplist( $day_array, $control_prefix . "_day" , $control_day , 0 ) . "&nbsp;" ;
		$controlstring .= write_droplist( $year_array, $control_prefix . "_year" , $control_year , 0 ) . "&nbsp;" ;
	}
	return $controlstring ;
}

function convert_timestamp ( $timestamp ) {
	$timestring = substr( $timestamp , 4 , 2 )."/".
					substr( $timestamp , 6 , 2 )."/".
					substr( $timestamp , 0 , 4 )." ".
					substr( $timestamp , 8 , 2 ).":".
					substr( $timestamp , 10 , 2 ).":".
					substr( $timestamp , 12 , 2 );
	return $timestring ;
}

function timestamp_to_text ( $timestamp, $dtchoice = "date" ) {
		switch( $dtchoice ){
		case "date" :
			$timestring = date("F j, Y", 
							mktime( substr( $timestamp , 8 , 2 ) ,
								substr( $timestamp , 10 , 2 ) ,
								substr( $timestamp , 12 , 2 ) ,
								substr( $timestamp , 4 , 2 ) ,
								substr( $timestamp , 6 , 2 ) ,
								substr( $timestamp , 0 , 4 ) ) ) ;
			break ;
		case "time" :
			$timestring = date("h:i:s a", 
							mktime( substr( $timestamp , 8 , 2 ) ,
								substr( $timestamp , 10 , 2 ) ,
								substr( $timestamp , 12 , 2 ) ,
								substr( $timestamp , 4 , 2 ) ,
								substr( $timestamp , 6 , 2 ) ,
								substr( $timestamp , 0 , 4 ) ) ) ;
			break ;
		case "datetime" :
			$timestring = date("F j, Y @ h:i:s A ", 
							mktime( substr( $timestamp , 8 , 2 ) ,
								substr( $timestamp , 10 , 2 ) ,
								substr( $timestamp , 12 , 2 ) ,
								substr( $timestamp , 4 , 2 ) ,
								substr( $timestamp , 6 , 2 ) ,
								substr( $timestamp , 0 , 4 ) ) ) ;
			break ;
		default:
			$timestring = date("F j, Y", 
							mktime( substr( $timestamp , 8 , 2 ) ,
								substr( $timestamp , 10 , 2 ) ,
								substr( $timestamp , 12 , 2 ) ,
								substr( $timestamp , 4 , 2 ) ,
								substr( $timestamp , 6 , 2 ) ,
								substr( $timestamp , 0 , 4 ) ) ) ;
			break ;	
	}
	return $timestring ;
}


function check_member( $uname , $initlen , $first , $last , $final4 = "0000" , $tablepre = "euconnect_" ){
	// function to check for pre-existing record 
/*	$check_sql .= " ,\n username= \"" . $uname . "\""  ;
	$check_sql .= " ,\n firstname = \"" . ucwords ( $_POST["firstname"] ) . "\""  ;
	$check_sql .= " ,\n lastname = \"" . ucwords ( $_POST["lastname"] ) . "\""  ;
*/	$check_sql = "SELECT euid, final4, firstname , lastname FROM " . $tablepre . "members WHERE ( username = \"" . $uname  . "\" ) OR ( lastname = \"" . $last  . "\" AND LEFT( firstname , " . $initlen . " )  = \"" . substr( $first , 0 , $initlen )  . "\" AND final4 = " . $final4  . " ) " ;
	//	echo $check_sql ;
	$result = mysql_query( $check_sql ) or die( mysql_error() ) ;
	if ( mysql_num_rows( $result )  ) {
		$row = mysql_fetch_assoc( $result ) ;
		if ( $final4 == $row["final4"] && "0000" != $row["final4"] && strtolower( $last ) == strtolower( $row["lastname"] ) ) {
			// last name and last 4 match
			return $row["euid"] ;
		}
		elseif ( "0000" == $row["final4"] ){
			//	last 4 of SSAN are unknown
			$check_sql = "SELECT euid, final4, firstname , lastname FROM " . $tablepre . "members WHERE ( username = \"" . $uname  . "\" ) " ;
			$result = mysql_query( $check_sql ) or die( mysql_error() ) ;
			if ( mysql_num_rows( $result )  ) {
			// username already in use by someone else
				return -1  ;
			}
			else {
			// username is available
			// evne though first and last name match
				return 0 ;
			}
		}
		else{
		// first and last name don't match
			return 0 ;
		}
	}
	else {
		return 0 ;
	}

	
	//	return $result ;
}

function new_member_notification( $firstname , $lastname , $username , $passwd , $emailaddress ) {
	// Compose message body for welcoming a new memeber
	$body = "Dear " . $firstname . " " . $lastname . ",\n\n";
	$body .= "Welcome to Experience Unlimited Anaheim Chapter. As a new member, \n";
	$body .= "you can take advantage of EU official website at www.eu-connections.org \n";
	$body .= "for adding/updating your resume profile to be viewed by potential \n";
	$body .= "employers, and for information such as EU events, workshop, and job search tips.\n\n";
	$body .= "Your EU member username and password have been created as:\n";
	$body .= "    username: " . $username . "\n";
	$body .= "    password: " . $passwd . "\n";
	$body .= "Please remember to change your login password after first login.\n\n";
	$body .= "Note this message was automatically generated by the EU website.\n\n";
	$body .= "webmaster@eu-connections.org\n";
	$body .= "http://eu-connections.org\n\n";
	$from = "Experience Unlimited <eudropzone@eu-connections.org>" ;
	$subject = "Your new EU Member Account" ;
	$newmember = array ( $emailaddress => $firstname . " " . $lastname ) ;
	return sendeumail( $newmember , $from, $body, $subject , 1 ) ;
}

function add_member( $_POST , $tablepre = "euconnect_" ){
	// function to process the form results into a SQL query
	// to insert new member data
	// first, create username 
	$uinit = 1 ;
	$uname = strtolower( substr( $_POST["firstname"] , 0 , $uinit ) . ucwords ( $_POST["lastname"] ) ) ;
	$uname = ereg_replace( '^\W' , '' , $uname ) ;
	// check for duplicate username
	$euid = check_member( $uname , $uinit , ucwords ( $_POST["firstname"] ) , ucwords ( $_POST["lastname"] ) , $_POST["final4"] , $tablepre ) ;
	while ( $euid == -1 && $uinit < ( strlen( $_POST["firstname"] ) + 1 ) ) {
		// increase number of characters from first name used in userid
		// until unique userid is created
		++$uinit ;
		$uname = strtolower( substr( $_POST["firstname"] , 0 , $uinit ) . ucwords ( $_POST["lastname"] ) ) ;
		$uname = ereg_replace( '^\W' , '' , $uname ) ;
		$euid = check_member( $uname , $uinit , ucwords ( $_POST["firstname"] ) , ucwords ( $_POST["lastname"] ) , $_POST["final4"] , $tablepre ) ;
	}
	if ( $euid == 0 ) {
		// format enrollment date 
		$enroll_date = $_POST["enroll_year"] . "-" . $_POST["enroll_month"] . "-" . $_POST["enroll_day"] ;
		$jumpstart_date = $_POST["jumpstart_year"] . "-" . $_POST["jumpstart_month"] . "-" . $_POST["jumpstart_day"] ;
		$insert_sql = "INSERT INTO " . $tablepre . "members " ;
		$insert_sql .= "( active , enrolled , jumpstart , userlevel , username , pass , final4 , lastname , firstname " ;
		$insert_sql .= ", email_home , voice_primary , voice_ext , voice_mobile , fascimile " ;
		$insert_sql .= ") VALUES ( " ;
		$insert_sql .= " \"" . $_POST["active"] . "\" "  ;
		$insert_sql .= " , \"" . $enroll_date . "\" "  ;
		$insert_sql .= " , \"" . $jumpstart_date . "\" "  ;
		$insert_sql .= " , \"" . $_POST["userlevel"] . "\" "  ;
		$insert_sql .= " , \"" . $uname . "\" "  ;
		$insert_sql .= " , SHA1( \"" . $uname . "\" ) "  ;
		$insert_sql .= " , \"" . $_POST[ "final4" ] . "\" "  ;
		$insert_sql .= " , \"" . ucwords ( $_POST["lastname"] ) . "\" "  ;
		$insert_sql .= " , \"" . ucwords ( $_POST["firstname"] ) . "\" "  ;
		$insert_sql .= " , \"" . strtolower( $_POST["email_home"] ) . "\" "  ;
		$insert_sql .= " , \"" . $_POST["voice_primary"] . "\" "  ;
		$insert_sql .= " , \"" . $_POST["voice_ext"] . "\" " ;
		$insert_sql .= " , \"" . $_POST["voice_mobile"] . "\" " ;
		$insert_sql .= ", \"" . $_POST["fascimile"] . "\" ) ; " ;
		//	echo $insert_sql ;
		$result = mysql_query( $insert_sql ) or die( mysql_error() . " at member insertion"  ) ;
		$euid = mysql_insert_id() ;
		if ( $euid > 0 ) {
			// add new member profile
			$insert_sql = "INSERT INTO " . $tablepre . "profiles " ;
			$insert_sql .= "( euid , priority , cat_id , posit_descr ) VALUES " ;
			$insert_sql .= "( \"" . $euid . "\" " ;
			$insert_sql .= ", \"1\"" ;
			$insert_sql .= ", \"" . $_POST["category_id_primary"] . "\"" ;
			$insert_sql .= ", \"" . $_POST["posit_descr"] . "\" ) " ;
			//	echo $insert_sql ;
			$result = mysql_query( $insert_sql ) or die( mysql_error() . " at profile insertion" ) ;
			// add new member committee assignment
			$insert_sql = "INSERT INTO " . $tablepre . "committees " ;
			$insert_sql .= "( euid , admin , tech , communication , job_fair , website , train , train_descr ) VALUES " ;
			$insert_sql .= "( \"" . $euid . "\" " ;
			$insert_sql .= ", \"" . $admin_comm . "\"" ;
			$insert_sql .= ", \"" . $tech_comm . "\"" ;
			$insert_sql .= ", \"" . $comm_comm . "\"" ;
			$insert_sql .= ", \"" . $jobfair_comm . "\"" ;
			$insert_sql .= ", \"" . $web_comm . "\"" ;
			$insert_sql .= ", \"" . $train_comm . "\"" ;
			$insert_sql .= ", \"" . $train_descr . "\" ) " ;
			$result = mysql_query( $insert_sql ) or die( mysql_error() . " at committee assignment"  ) ;
		}
		// uncomment the next line to activate the new member email notification
		// notify_new_member( $_POST[ "email_home" ] , $uname , $uname ) ;
		if ( !empty( $_POST[ "email_home" ] ) &&  $_POST[ "email_home" ] != "" ) {
			new_member_notification( ucwords ( $_POST["firstname"] ) , ucwords ( $_POST["lastname"] ) , $uname , $uname , $_POST[ "email_home" ] ) ;
		}
		echo "<div align=\"center\"><h4>Member  " . ucwords ( $_POST["firstname"] ) . " " . ucwords ( $_POST["lastname"] ) . " added successfully.</h4></div>" ;
	}
	else {
		echo "<div align=\"center\"><h4>" . ucwords ( $_POST["firstname"] ) . " " . ucwords ( $_POST["lastname"] ) . " is already listed as a member.</h4></div>" ;
	}
	return $euid ;
	//	return $result ;
}

function update_member( $_POST , $tablepre = "euconnect_" ){
	// function to process the form results into a SQL query
	// to insert new member data
	// first, create username 
	$uname = strtolower( substr( $_POST["firstname"] , 0 , 1 ) . ucwords ( $_POST["lastname"] ) ) ;
	$uname = ereg_replace( '^\W' , '' , $uname ) ;
	// check for duplicate username
	
	// format enrollment date 
	$enroll_date = $_POST["enroll_year"] . "-" . $_POST["enroll_month"] . "-" . $_POST["enroll_day"] ;
	$jumpstart_date = $_POST["jumpstart_year"] . "-" . $_POST["jumpstart_month"] . "-" . $_POST["jumpstart_day"] ;
	$update_sql = "UPDATE " . $tablepre . "members SET " ;
	$update_sql .= "\n active = " . $_POST["active"] . ""  ;
	$update_sql .= " ,\n enrolled = \"" . $enroll_date . "\""  ;
	$update_sql .= " ,\n jumpstart = \"" . $jumpstart_date . "\""  ;
	$update_sql .= " ,\n userlevel = " . $_POST["userlevel"] . ""  ;
	$update_sql .= " ,\n final4= \"" . $_POST["final4"] . "\""  ;
	$update_sql .= " ,\n username= \"" . $uname . "\""  ;
	$update_sql .= " ,\n firstname = \"" . ucwords ( $_POST["firstname"] ) . "\""  ;
	$update_sql .= " ,\n lastname = \"" . ucwords ( $_POST["lastname"] ) . "\""  ;
	$update_sql .= " ,\n email_home = \"" . strtolower( $_POST["email_home"] ) . "\""  ;
	$update_sql .= " ,\n voice_primary = \"" . $_POST["voice_primary"] . "\""  ;
	$update_sql .= " ,\n voice_ext = \"" . $_POST["voice_ext"] . "\" " ;
	$update_sql .= " , \nvoice_mobile = \"" . $_POST["voice_mobile"] . "\"" ;
	$update_sql .= " ,\n fascimile = \"" . $_POST["fascimile"] . "\" " ;
	$update_sql .= "\n WHERE euid = " . $_POST["euid"] . " ;\n" ;
	//	echo $update_sql ;
	$result = mysql_query( $update_sql ) or die( mysql_error() ) ;
	echo $result . "<br />\n" ;
	//	create full set of committee assignment values to allow for unchecked boxes not passing a value
	$admin_comm = $_POST["admin_comm"] ? $_POST["admin_comm"] : "0" ;
	$tech_comm = $_POST["tech_comm"] ? $_POST["tech_comm"] : "0" ;
	$comm_comm = $_POST["comm_comm"] ? $_POST["comm_comm"] : "0" ;
	$jobfair_comm = $_POST["jobfair_comm"] ? $_POST["jobfair_comm"] : "0" ;
	$web_comm = $_POST["web_comm"] ? $_POST["web_comm"] : "0" ;
	$train_comm = $_POST["train_comm"] ? $_POST["train_comm"] : "0" ;
	$train_descr = $_POST["train_descr"] ? $_POST["train_descr"] : "" ;
	if ( $_POST["committees"] != "0" ){
		// updates committee assignments
		$update_sql = "UPDATE " . $tablepre . "committees SET " ;
		$update_sql .= "\n admin = " . $admin_comm . ""  ;
		$update_sql .= " ,\n tech = " . $tech_comm . ""  ;
		$update_sql .= " ,\n communication = " . $comm_comm . ""  ;
		$update_sql .= " ,\n job_fair = " . $jobfair_comm . " "  ;
		$update_sql .= " ,\n website = " . $web_comm . ""  ;
		$update_sql .= " ,\n train = " . $train_comm . ""  ;
		$update_sql .= " ,\n train_descr= \"" . $train_descr . "\""  ;
		$update_sql .= "\n WHERE euid = " . $_POST["euid"] . " ;\n" ;
			echo "<!-- $update_sql  -->" ;
		$result = mysql_query( $update_sql ) or die( mysql_error() ) ;
		echo $result . "<br />\n" ;
	}
	if ( $_POST["committees"] == "0" ){
		// executes if no prior committee table record exists
		$insert_sql = "INSERT INTO " . $tablepre . "committees " ;
		$insert_sql .= "( euid , admin , tech , communication , job_fair , website , train , train_descr ) VALUES " ;
		$insert_sql .= "( \"" . $_POST["euid"] . "\" " ;
		$insert_sql .= ", \"" . $admin_comm . "\"" ;
		$insert_sql .= ", \"" . $tech_comm . "\"" ;
		$insert_sql .= ", \"" . $comm_comm . "\"" ;
		$insert_sql .= ", \"" . $jobfair_comm . "\"" ;
		$insert_sql .= ", \"" . $web_comm . "\"" ;
		$insert_sql .= ", \"" . $train_comm . "\"" ;
		$insert_sql .= ", \"" . $train_descr . "\" ) " ;
		$result = mysql_query( $insert_sql ) or die( mysql_error() ) ;
		echo $result . "<br />\n" ;
	}
	//	echo $update_sql ;
	//
	if ( $_POST["profilecount"] == "0" ) {
		$insert_sql = "INSERT INTO " . $tablepre . "profiles " ;
		$insert_sql .= "( euid , priority , cat_id , posit_descr ) VALUES " ;
		$insert_sql .= "( \"" . $_POST["euid"]  . "\" " ;
		$insert_sql .= ", \"1\"" ;
		$insert_sql .= ", \"" . $_POST["category_id_primary"] . "\"" ;
		$insert_sql .= ", \"" . $_POST["posit_descr"] . "\" ) " ;
		//	echo $insert_sql ;
		// $result = mysql_query( $insert_sql ) or die( mysql_error() ) ;
		echo $result . "<br />\n" ;
	}
	echo "<div align=\"center\"><h4>Member  " . ucwords ( $_POST["firstname"] ) . " " . ucwords ( $_POST["lastname"] ) . " updated successfully.</h4></div>" ;

	return $_POST["euid"] ;
	//	return $result ;
}

function find_member( $_POST , $tablepre = "euconnect_" ){
	// function to process the form results into a SQL query and 
	// display the results
	global $max_cat_num  , $cat_array ;
	$keylist  = $_POST["words"] ;
	$searchlist = "" ;
	$key_sql = "" ;

	// check for key words and include them in the query
	if ( $keylist  != "" ){
		$key_string = "%" ;
		$word_array = explode(" ", $keylist ) ;
		if ( $word_array ) {
			$key_sql = "posit_descr LIKE \"%" . implode("%\" OR posit_descr LIKE \"%", $word_array) . "%\" " ;
			$key_sql .= " OR overview LIKE \"%" . implode("%\" OR overview LIKE \"%", $word_array) . "%\" " ;
			$key_sql .= " OR bullet1 LIKE \"%" . implode("%\" OR bullet1 LIKE \"%", $word_array) . "%\" " ;
			$key_sql .= " OR bullet2 LIKE \"%" . implode("%\" OR bullet2 LIKE \"%", $word_array) . "%\" " ;
			$key_sql .= " OR bullet3 LIKE \"%" . implode("%\" OR bullet3 LIKE \"%", $word_array) . "%\" " ;
			$key_sql .= " OR bullet4 LIKE \"%" . implode("%\" OR bullet4 LIKE \"%", $word_array) . "%\" " ;
			$key_sql .= " OR bullet5 LIKE \"%" . implode("%\" OR bullet5 LIKE \"%", $word_array) . "%\" " ;
		}
	}
	$cat_sql = "" ;
	
	// iterate through the check boxes to see which categories were checked
	for ($i = 1 ; $i <= $max_cat_num ; $i++) {
		$catnum = "C" . $i ;
		if ( $_POST[ $catnum  ] ) {
			$cat_sq_array[] = $_POST[ $catnum ] ;
		}
		if ( $cat_sq_array ) {
			$cat_sql = " cat_id IN ( \"" . implode("\", \"", $cat_sq_array ) . "\" ) "  ; 
		}
	}
	// begin assembling the SQL query string
	$search_sql = "SELECT prof_id, euid, cat_id, posit_descr, overview, bullet1, bullet2, bullet3, bullet4, bullet5 FROM euconnect_profiles " ;
	if ( $cat_sql != "" && $key_sql != "" ){
		$search_sql .= "WHERE ( $cat_sql ) AND ( $key_sql ) " ;
	}
	elseif ( $key_sql != "" ){
		$search_sql .= "WHERE ( $key_sql ) " ;
	}
	elseif ( $cat_sql != "" ){
		$search_sql .= "WHERE ( $cat_sql ) " ;
	}
	$search_sql .= "GROUP BY cat_id ORDER BY euid ASC ;" ;
	$result = mysql_query( $search_sql ) or die( mysql_error() ) ;
	$searchlist = "<p>Your search returned " . mysql_num_rows( $result )  . " record(s).</p>\n" ;
	if ( mysql_num_rows( $result ) ) {
		$currentcat = "" ;
		while ( $row = mysql_fetch_assoc( $result ) ) {
			if ( $currentcat != $row["cat_id"] ) {
				$currentcat = $row["cat_id"] ;
				$searchlist .=  "</dl></dd></dl>" ;
				 $cat_descr_array = $cat_array[ $currentcat ] ;
				$searchlist .=  "<dl><dt class=\"profile_cat\">" . $cat_descr_array[ 1 ] . "</dt><dd><br /><dl>\n" ;
			}
			$searchlist .=  writeprofile( $row ) ;
		}
		$searchlist .=  "</dl></dd></dl>\n" ;
	}
	return $searchlist ;
}

function writeform( $record , $new = "0" )
{
	global $cat_array , $page_name  ;
?>

<!-- 
<?
foreach ( $record as $key => $value) echo $key . " ==> " . $value . "\n" ;
?>
 -->
<form action="member.php" method="post" name="member_record" id="member_record" onKeyUp="highlight(event)" onClick="highlight(event)" onSubmit="return checkFields();" >
	<input type="hidden" name="euid" id="euid" value="<?= $record[ "euid" ] ?>">
	<input type="hidden" name="new" id="new" value="<?= $new ?>">
	<input type="hidden" name="page" id="page" value="<?= str_replace ( "console/", "" , str_replace ( ".inc.php" , "", $page_name ) ) ?>">
<table align="center" class="memberform">
<tr>
	<td><br /><strong>First name</strong></td>
	<td><br /><strong>Last name</strong></td>
</tr>
<tr>
	<td><input type="text" name="firstname" id="firstname" value="<?= $record[ "firstname" ] ?>" size="20" maxlength="25" onFocus="highlight( this ) ;" ></td>
	<td><input type="text" name="lastname" id="lastname"  value="<?= $record[ "lastname" ] ?>" size="20" maxlength="25" onFocus="highlight( this ) ;" ></td>
</tr>
<tr>
	<td><br /><strong>Username</strong></td>
	<td><br /><strong>email</strong></td>
</tr>
<tr>
	<td><strong><font color="#808000"><?= $record[ "username" ] ?></font><br />Last 4</strong><br /><input type="text" name="final4" id="final4" value="<?= $record[ "final4" ] ?>" size="4" maxlength="4" ></strong></td>
	<td><input type="text" name="email_home" id="email_home"  value="<?= $record[ "email_home" ] ?>" size="40" maxlength="45" onBlur="checkEmail( this );"><br /></td>
</tr>
<tr>
	<td valign="top"><strong>Voice</strong>
	<br /><input type="text" name="voice_primary" id="voice_primary" value="<?= $record[ "voice_primary" ] ?>" size="14" maxlength="14" onBlur="checkphone( this );">
	<br />ext. <input type="text" name="voice_ext" id="voice_ext"  value="<?= $record[ "voice_ext" ] ?>" size="6" maxlength="6">
	<br /><strong>Cell</strong>
	<br /><input type="text" name="voice_mobile" id="voice_mobile" value="<?= $record[ "voice_mobile" ] ?>" size="14" maxlength="14" onBlur="checkphone( document.member_record.voice_mobile );">
	<br /><strong>FAX</strong>
	<br /><input type="text" name="fascimile" id="fascimile" value="<?= $record[ "fascimile" ] ?>" size="14" maxlength="14" onBlur="checkphone( this );"><br /></td>
	<td valign="top"><strong>Committee Assignment</strong>
	<input type="hidden" name="committees" id="committees" value="<?= $record[ "committees" ] ?>">
	<br /><input type="checkbox" name="admin_comm" id="admin_comm" value="1" <?php if ( $record[ "admin_comm" ] == 1 ) { ?>checked="checked"<?php } ?>> <strong>Admin Committee</strong>
	<br /><input type="checkbox" name="tech_comm" id="tech_comm" value="1" <?php if ( $record[ "tech_comm" ] == 1 ) { ?>checked="checked"<?php } ?>> <strong>Technical Committee</strong>
	<br /><input type="checkbox" name="comm_comm" id="comm_comm" value="1" <?php if ( $record[ "comm_comm" ] == 1 ) { ?>checked="checked"<?php } ?>> <strong>Communications</strong>
	<br /><input type="checkbox" name="jobfair_comm" id="jobfair_comm" value="1" <?php if ( $record[ "jobfair_comm" ] == 1 ) { ?>checked="checked"<?php } ?>> <strong>Job Fair</strong>
	<br /><input type="checkbox" name="web_comm" id="web_comm" value="1" <?php if ( $record[ "web_comm" ] == 1 ) { ?>checked="checked"<?php } ?>> <strong>Web Site</strong>
	<br /><input type="checkbox" name="train_comm" id="train_comm" value="1" <?php if ( $record[ "train_comm" ] == 1 ) { ?>checked="checked"<?php } ?>> <strong>Training &mdash;  <input type="text" name="train_descr" id="train_descr"  value="<?= $record[ "train_descr" ] ?>" size="20" maxlength="20"></strong>
	<br />
	</td>
</tr>
<input type="hidden" name="profilecount" id="profilecount" value="<?= $record[ "profilecount" ] ?>">
<? 	if ( $record["profilecount"] > 0 ) { ?>
<input type="hidden" name="profilecount" id="category_id_primary" value="<?= $record[ "category_id_primary" ] ?>">
<input type="hidden" name="profilecount" id="posit_descr" value="<?= $record[ "posit_descr" ] ?>">
<? }
else { ?><tr>
	<td><strong>Primary Career Category</strong></td>
	<td><?php echo write_droplist( $cat_array, "cat_id" , $record[ "category_id_primary" ] , 1 )  ; ?></td>
</tr>
<tr>
	<td><strong>Primary Position Description</strong></td>
	<td><input type="text" name="posit_descr" id="posit_descr" value="<?= $record[ "posit_descr" ] ?>" size="25" maxlength="40" ></td>
</tr><? } ?>
<tr>
	<td><input type="radio" name="active" value="1"<?php if ( $record[ "active" ] > 0 ) { ?>checked<?php } ?>> <strong>Active</strong></td>
	<td><strong><strong>User Level</strong></strong></td>
</tr>
<tr>
	<td><input type="radio" name="active" value="0"<?php if ( $record[ "active" ] == 0 ) { ?>checked<?php } ?>> <strong>Inactive</strong><br /></td>
	<td><select name="userlevel" id="userlevel" size="1">
	<option value="0">0</option>
	<option value="1"<? if ( $record[ "userlevel"] == 1 ) { ?> selected<? } ?> >member</option>
	<option value="2"<? if ( $record[ "userlevel"] == 2 ) { ?> selected<? } ?> >administrator</option>
	</select></td>
</tr>
<tr>
	<td><br /><strong>Enrollment Date</strong></td>
	<td><br /><strong>Jumpstart Date</strong></td>
</tr>
<tr>
	<td><?php echo write_datecontrol ( "enroll" , $record[ "enrolled" ] ) ?></td>
	<td><?php echo write_datecontrol ( "jumpstart" , $record[ "jumpstart" ] ) ?></td>
</tr>
<tr>
<?php if ( $new == "1" ) {
?>	<td align="right"><input type="submit" name="s1" id="s1" value="Add Member Record"></td>
<?php }
else {
?>	<td align="right"><input type="submit" name="s1" id="s1" value="Update Member Record"></td>
<?php }
?>	<td><input type="Reset"></td>
</tr>
</table>
</form>
<? 	if ( $record["profilecount"] > 0 ) { 
		$profileformstr .= "<table align=\"center\" class=\"memberform\">\n<tr>\n<td align=\"center\">" ;
		$profileformstr .= "<form action=\"member.php\" method=\"post\" name=\"profile\" id=\"profile\">" ;
		$profileformstr .= "<input type=\"hidden\" name=\"page\" id=\"page\" value=\"update_profiles\">\n" ;
		$profileformstr .= "<input type=\"hidden\" name=\"euid\" id=\"euid\" value=\"" . $record[ "euid" ] . "\">\n" ;
		$profileformstr .= "<input type=\"submit\" value=\"Update Profile(s)\"></form>\n" ;
		$profileformstr .= "</td></tr></table>\n" ;
		echo $profileformstr ;
		}
	if ( $record["username"] != "" ) { 
		$lastupdatestr .= "<table align=\"center\" class=\"memberform\">\n<tr>\n<td align=\"center\">" ;
		$lastupdatestr .= "Contact information last updated at " . timestamp_to_text( $record[ "updated" ], "datetime" ) . "\n" ;
		$lastupdatestr .= "</td></tr></table>\n" ;
		echo $lastupdatestr ;
		}	
}

$new = ( $_POST["new"]  ) ? $_POST["new"] : "1" ;
//$new = ( $_POST["new"]  ) ? $_POST["new"]] : "0" ;
$sql_memberfetch = "SELECT m.euid , m.active, m.enrolled, m.jumpstart, m.updated, m.userlevel " ;
$sql_memberfetch .= ", IFNULL( p.priority, 0 ) AS profilecount , IFNULL( p.cat_id, \"  \" ) AS category_id_primary , IFNULL( p.posit_descr, \"\" ) AS posit_descr " ;
$sql_memberfetch .= ", RTRIM( m.username ) AS username , \"\" AS pass, RTRIM( m.lastname ) AS lastname , m.final4 ,RTRIM( m.firstname ) AS firstname " ;
$sql_memberfetch .= ", RTRIM( m.email_home ) AS email_home, RTRIM( m.voice_primary ) AS voice_primary , RTRIM( m.voice_ext ) AS voice_ext " ;
$sql_memberfetch .= ", RTRIM( m.voice_mobile ) AS voice_mobile , RTRIM( m.fascimile ) AS fascimile " ;
$sql_memberfetch .= ", IFNULL( c.admin , 0  ) AS admin_comm , IFNULL( c.tech , 0  ) AS tech_comm " ;
$sql_memberfetch .= ", IFNULL( c.communication , 0  ) AS comm_comm , IFNULL( c.job_fair , 0  ) AS jobfair_comm " ;
$sql_memberfetch .= ", IFNULL( c.website , 0  ) AS web_comm , IFNULL( c.train , 0  ) AS train_comm " ;
$sql_memberfetch .= ", RTRIM( IFNULL( c.train_descr , \"\"  ) ) AS train_descr, IFNULL( c.euid , 0  ) AS committees " ;
$sql_memberfetch .= ", RTRIM( IFNULL( c.train_descr , \"\"  ) ) AS train_descr, IFNULL( c.euid , 0  ) AS committees " ;
 
 
$sql_memberfetch .= " FROM " . $tablepre . "members AS m LEFT JOIN " . $tablepre . "profiles AS p ON m.euid = p.euid " ;
$sql_memberfetch .= " LEFT JOIN " . $tablepre . "committees AS c ON c.euid = m.euid " ;
$sql_memberfetch .= " WHERE ( p.priority = 1 OR p.priority IS NULL ) AND m.euid = " ;

if ( ( $_GET["euid"] && $user_euid == $_GET["euid"] ) || ( $user_admin == 1 && $_GET["euid"] )  || $_POST["euid"] ) {
	// query database for member info
	$record_euid = ( $_POST["euid"]  ) ? $_POST["euid"] : $_GET["euid"] ;
	if ( $_POST["lastname"] && $_POST["lastname"] != "" && $user_admin > 1 ){
		// form results into a SQL query to insert the member data
		$record_euid = update_member( $_POST ) ;
	}
	$sql_memberfetch .= $record_euid ;
	//	echo $sql_memberfetch . "<br />" ;
	$result = mysql_query( $sql_memberfetch ) or die( mysql_error() ) ;
	// echo "returned " . mysql_num_rows( $result ) . " record(s)<br />" ;
	if ( mysql_num_rows( $result ) ) {
		$member_row = mysql_fetch_assoc( $result ) ;
		$new = "0" ;
		}
	else {
		$newmember = $_POST ;
		add_member ( $newmember ) ;
		$new = "0" ;
		
	}
}
elseif ( $_POST["lastname"] && $_POST["lastname"] != "" && $user_admin > 1 ){
	// no EUID value means this is a new member
	// form results into a SQL query to insert the member data
	$record_euid = add_member( $_POST ) ;
	if ( $record_euid > 0 ) {
		//	$sql_memberfetch = "SELECT * FROM " . $tablepre . "members WHERE euid = " . $record_euid ;
		$sql_memberfetch .= $record_euid ;
		$result = mysql_query( $sql_memberfetch ) or die( mysql_error() ) ;
		if ( mysql_num_rows( $result ) ) {
			$member_row = mysql_fetch_assoc( $result ) ;
			$new = "0" ;
			}
		else {
			$newmember = $_POST ;
			add_member ( $newmember ) ;
			$new = "0" ;
		}
	}
}
else {
		// create default array
		$enrolldate = date("Y-m-d", time( ) ) ;
		// calculate jumpstart date
		$e_day = substr( $enrolldate , 8 , 2 ) ;
		$e_month = substr( $enrolldate , 5 , 2 ) ;
		$e_year = substr( $enrolldate , 0 , 4 ) ;
		$j_day = $e_day ;
		$j_month = strval ( $e_month + 3 ) ;
		$j_year = $e_year ;
		if ( $j_month > 12 ) {
			$j_month = strval ( $j_month - 12 ) ;
			$j_year = strval ($j_year + 1 ) ;
		}
		if ( strlen( $j_month ) == 1 ) {
			$j_month = "0" . $j_month ;
		}
		$member_row = array(
			"euid" => 0 ,
			"active" => 1 ,
			"enrolled" => $enrolldate ,
			"jumpstart" => $j_year . "-" . $j_month . "-" . $j_day ,
			"updated" => date("YmdHis", time( ) ) ,
			"userlevel" => 1 ,
			"final4" => "0000" ,
			"pass" => "" ,
			"username" => "" ,
			"profilecount" => 0 ,
			"category_id_primary" => "  " ,
			"posit_descr" => "",
			"lastname" => "" ,
			"firstname" => "" ,
			"email_home" => "" ,
			"voice_primary" => "" ,
			"voice_ext" => "" ,
			"voice_mobile" => "" ,
			"fascimile" => "" ,
			"admin_comm" => 0 ,
			"tech_comm" => 0 ,
			"comm_comm" => 0 ,
			"jobfair_comm" => 0 ,
			"web_comm" => 0 ,
			"train_comm" => 0 ,
			"train_descr"  => "" ,
			"committees" => 0  ) ;
			
}

writeform( $member_row , $new ) ;


?>
<!-- member.inc.php -->
