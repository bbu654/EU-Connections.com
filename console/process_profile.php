<?php
include_once("../include/config.inc.php") ;
if ( !isset( $user_uname ) || !isset( $user_euid ) || $user_euid == "000000" ) { 
	$action = ( $_GET["function"] ) ? $_GET["function"] : "none" ;
	$action = ( $_POST["function"] ) ? $_POST["function"] : $action ;
	$valid = 0 ;
	session_start();
	header("Location: http://".$_SERVER['HTTP_HOST']
                      .str_replace ( "console", "" , dirname($_SERVER['PHP_SELF']) ) 
                      ."member.php" ) ;
	session_destroy();
	exit;
}
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"><?

mysql_connect( $dbhost, $dbuser, $dbpw ) or die( "line " . __LINE__ . ": " . mysql_error() );
mysql_select_db( $dbname ) or die(mysql_error());
$profile_idnum = $_POST[ "prof_id" ] ;
$page = $_POST[ "page" ] ;
$displayid = $_POST[ "displayid" ] ;
$confirm = $_POST["confirm"] ? $_POST["confirm"] : "0" ;
$approve = $_POST["approve"] ? $_POST["approve"] : "0" ;
$messagebody = $_POST["messagebody"] ? $_POST["messagebody"] : "" ;
include_once("commonfunctions.inc.php") ;

function record_rating( $displayid , $rater_euid , $rating , $rater_message = ""  ) {
	// function to save a copy of profile rating and message sent to member
	global $tablepre ;
	$euid = substr( $displayid , 3 ) ;
	$profile_cat = substr( $displayid , 0 , 2 ) ;
	$membersql = "SELECT euid, lastname, firstname, email_home FROM " . $tablepre . "members WHERE euid = " . $euid ;
	// echo $membersql . "<br />\n" ;
	$result = mysql_query( $membersql ) or die( "line " . __LINE__ . ": " . mysql_error() ) ;
	$memberrow = mysql_fetch_assoc( $result ) ;
	$membermailarray = array ( $memberrow[ "email_home" ] => $memberrow[ "firstname" ] . " " . $memberrow[ "lastname" ] ) ;
	// $membermailarray = array ( "william@clardy.org" => $memberrow[ "firstname" ] . " " . $memberrow[ "lastname" ] ) ;
	$insert_sql = "INSERT INTO " . $tablepre . "profilereviews " ;
	$insert_sql .= "( messagedate , profile_cat , euid , rater_euid , rating , rater_message ) VALUES " ;
	$insert_sql .= "( now() " ;
	$insert_sql .= ", \"" . $profile_cat . "\" " ;
	$insert_sql .= ", " . $euid . " " ;
	$insert_sql .= ", " . $rater_euid . " " ;
	$insert_sql .= ", \"" . $rating . "\" " ;
	$insert_sql .= ", \"" . addslashes ( $rater_message )  . "\" ) " ;
	// echo $insert_sql ;
	$result = mysql_query( $insert_sql ) or die( "line " . __LINE__ . ": " . mysql_error() ) ;
	if ( $rating > 0 ) {
		sendeumail( $membermailarray , "euredirect@eu-connections.org" , $rater_message , "EU Profile " . $displayid . " has been released to the EU search engine" , 1 ) ;
	}
	elseif ( $rating < 0 ) {
		sendeumail( $membermailarray , "euredirect@eu-connections.org" , $rater_message , "EU Profile " . $displayid . " requires rewriting " , 1 ) ;
	}
}

function release_profile( $profnum , $revieweridnum , $message = "" ) {
	// function to process the form results into a SQL query
	// to update the member data
	global $displayid , $tablepre ;
	$update_sql = "UPDATE " . $tablepre . "profiles SET " ;
	$update_sql .= " released = 1 " ;
	$update_sql .= " WHERE prof_id = \"" . $profnum . "\" " ;
	//	echo $update_sql ;
	$result = mysql_query( $update_sql ) or die( "line " . __LINE__ . ": " . mysql_error() ) ;
	$result = record_rating( $displayid , $revieweridnum , 1 , $message  ) ;
	$returnstring = "<body bgcolor=\"#FFFFFF\" onblur=\"self.focus();\" onload=\"self.focus();setTimeout(window.close, 5000)\">\n" ;
	$returnstring .= "<div align=\"center\"><h4>Profile " . $displayid . " has been released to the search engine.</h4>" ;
	$returnstring .= "<form>\n\t<input type=\"button\" value=\"Close Window\" onClick=\"javascript:window.close();\">\n</form>\n</div>\n" ;
	return $returnstring ;
}

function return_profile( $profnum , $revieweridnum , $message = "" ) {
	// function to process the form results into a SQL query
	// to update the member data
	global $displayid , $tablepre ;
	$update_sql = "UPDATE " . $tablepre . "profiles SET " ;
	$update_sql .= " released = -1 " ;
	$update_sql .= " WHERE prof_id = \"" . $profnum . "\" " ;
	// echo $update_sql ;
	$result = mysql_query( $update_sql ) or die( "line " . __LINE__ . ": " . mysql_error() ) ;
	$result = record_rating( $displayid , $revieweridnum , -1 , $message  ) ;
	$returnstring = "<body bgcolor=\"#FFFFFF\" onblur=\"self.focus();\" onload=\"self.focus();setTimeout(window.close, 5000)\">\n" ;
	$returnstring .= "<div align=\"center\"><br /><br /><br /><h4>Profile " . $displayid . " has been returned to the member.</h4>" ;
	$returnstring .= "<form>\n\t<input type=\"button\" value=\"Close Window\" onClick=\"javascript:window.close();\">\n</form>\n</div>\n" ;
	return $returnstring ;
}

function confirm_release( $profnum , $revieweridnum , $message = "" ) {
	global $displayid , $tablepre ;
	$returnstring = "<body bgcolor=\"#FFFFFF\" onload=\"self.focus();setTimeout(window.close, 600000)\">\n" ;
	$returnstring .= "<div align=\"center\">\n<span class=\"BodyTitleText\">Confirm release of profile " . $displayid . "</span>\n" ;
    $returnstring .= "<form action=\"" . $_SERVER[ "SCRIPT_NAME" ] . "\" method=\"post\" enctype=\"multipart/form-data\" name=\"sendmessage\" id=\"sendmessage\">\n" ;
    $returnstring .= "<input type=\"hidden\" name=\"prof_id\" id=\"prof_id\" value=\"" . $profnum . "\">\n<table>\n<tr>\n" ;
	$returnstring .= "<input name=\"displayid\" type=\"hidden\" value=\"" . $displayid . "\">\n" ;
	$returnstring .= "<input name=\"approve\" type=\"hidden\" value=\"1\">\n" ;
	$returnstring .= "<input name=\"confirm\" type=\"hidden\" value=\"1\">\n" ;
	$returnstring .= "\t<input type=\"hidden\" name=\"profileid\" value=\"" . $profile_id . "\">\n" ;
    $returnstring .= "\t<table border=\"0\" cellspacing=\"0\" cellpadding=\"4\">\n" ;
    $returnstring .= "\t\t<tr>\n\t\t<td align=\"center\" >\n" ;
    $returnstring .= "You may send a brief comment to the member:\n" ;
    $returnstring .= "\t\t</td>\n\t</tr>\n\t<tr>\n" ;
    $returnstring .= "\t\t<td align=\"center\" ><textarea cols=\"40\" rows=\"5\" name=\"messagebody\" id=\"messagebody\" onBlur=\"validlength( this , 'messagebody' , 200 );\"></textarea></td>\n" ;
    $returnstring .= "\t</tr>\n\t<tr>\n\t\t<td align=\"center\" ><input type=\"submit\" name=\"release\" id=\"release\" value=\"Confirm Release\"></td>\n" ;
    $returnstring .= "\t</tr>\n\t</table>\n</form>\n" ;
	$returnstring .= "<form>\n\t<input type=\"button\" value=\"Close Window\" onClick=\"javascript:window.close();\">\n</form>\n</div>\n" ;
	return $returnstring ;
}

function confirm_return( $profnum , $revieweridnum , $message = "" , $needcomment = false ) {
	global $displayid , $tablepre ;
	$returnstring = "<body bgcolor=\"#FFFFFF\" onload=\"self.focus();setTimeout(window.close, 600000)\">\n" ;
	$returnstring .= "<div align=\"center\">\n<span class=\"BodyTitleText\">Confirm return of profile " . $displayid . " to member</span>\n" ;
    $returnstring .= "<form action=\"" . $_SERVER[ "SCRIPT_NAME" ] . "\" method=\"post\" enctype=\"multipart/form-data\" name=\"sendmessage\" id=\"sendmessage\">\n" ;
    $returnstring .= "<input type=\"hidden\" name=\"prof_id\" id=\"prof_id\" value=\"" . $profnum . "\">\n<table>\n<tr>\n" ;
	$returnstring .= "<input name=\"displayid\" type=\"hidden\" value=\"" . $displayid . "\">\n" ;
	$returnstring .= "<input name=\"approve\" type=\"hidden\" value=\"0\">\n" ;
	$returnstring .= "<input name=\"confirm\" type=\"hidden\" value=\"1\">\n" ;
	$returnstring .= "\t<input type=\"hidden\" name=\"profileid\" value=\"" . $profile_id . "\">\n" ;
    $returnstring .= "\t<table border=\"0\" cellspacing=\"0\" cellpadding=\"4\">\n" ;
    $returnstring .= "\t\t<tr>\n\t\t<td align=\"center\" >\n" ;
	if ( $needcomment ) {
    	$returnstring .= "<span style=\"background-color: #FFFF00; color: #FF0000;\">You <strong>must</strong> send a comment to the member <strong>briefly</strong> <br />explaining why this profile was considered substandard:</span>\n" ;
	}
	else {
    	$returnstring .= "You <strong>must</strong> send a comment to the member <strong>briefly</strong> <br />explaining why this profile was considered substandard:\n" ;
	}
    $returnstring .= "\t\t</td>\n\t</tr>\n\t<tr>\n" ;
    $returnstring .= "\t\t<td align=\"center\" ><textarea cols=\"40\" rows=\"5\" name=\"messagebody\" id=\"messagebody\" onBlur=\"validlength( this , 'messagebody' , 200 );\"></textarea></td>\n" ;
    $returnstring .= "\t</tr>\n\t<tr>\n\t\t<td align=\"center\" ><input type=\"submit\" name=\"return\" id=\"return\" value=\"Confirm Return\"></td>\n" ;
    $returnstring .= "\t</tr>\n\t</table>\n</form>\n" ;
	$returnstring .= "<form>\n\t<input type=\"button\" value=\"Close Window\" onClick=\"javascript:window.close();\">\n</form>\n</div>\n" ;
	return $returnstring ;
}
echo "\n<!-- \$_SERVER[ \"HTTP_REFERER\" ] == " . $_SERVER[ "HTTP_REFERER" ] . " at " . __LINE__ . " -->\n" ;
echo "<!-- \$_SERVER[ \"HTTP_HOST\" ] . \$_SERVER[ \"SCRIPT_NAME\" ] == " . $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "SCRIPT_NAME" ]  . " at " . __LINE__ . " -->\n" ;
if ( $confirm == "1" ) {
	if ( $approve == "1" ) {
		$status = release_profile( $profile_idnum , $user_euid , $messagebody ) ;
	//	$status = echo_parameters() ;
	}
	elseif ( $approve == "0" && $messagebody != "" ) {
	//	$status = echo_parameters() ;
		$status = return_profile( $profile_idnum , $user_euid , $messagebody ) ;
	}
	else {
		$status = confirm_return( $profile_idnum , $user_euid , "" , true ) ;
	}
}
else {
	if ( $approve == "1" ) {
		$status = confirm_release( $profile_idnum , $user_euid ) ;
	}
	elseif ( $approve == "0" ) {
		$status = confirm_return( $profile_idnum , $user_euid ) ;
	}
	else {
		$status = echo_parameters() ;
	}

}
?><html>
  <head>
    <title>Processing Profile <?= $displayid ?></title>
	<script language="JavaScript">
<!--
function validlength( formField , fieldLabel , fieldlength )
{
	var result = true;
	while (formField.value.length > fieldlength )
	{
		var tempref=formField.value ;
		var trimlength=formField.value.lastIndexOf(' ') ;
		formField.value=tempref.substr( 0, trimlength ) 
		formField.focus();
		result = false;
	}
	if ( !result ){
		alert('The "' + fieldLabel +'" field is limited to ' + fieldlength + ' characters.' );
	}
	
	return result;
}
//-->
</script>
</head>
<?= $status ?>
<!-- <?= $_SERVER[ "HTTP_REFERER" ] . "<br />" ."http://" . $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "SCRIPT_NAME" ] ?> -->
  </body>
</html>
