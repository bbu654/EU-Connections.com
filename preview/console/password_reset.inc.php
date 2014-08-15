<?php

function select_query( $tablepre = "euconnect_" ) {
	// function to create roster query
	$select_sql =  " " ;
	$sort_sql = " ORDER BY fullname ASC" ;
	$sql = "SELECT euid , RTRIM( CONCAT( lastname,\", \", firstname) ) AS fullname , username FROM " . $tablepre . "members WHERE active = 1 " ;
	return $sql . $select_sql . $sort_sql ;
}

function writemember( $row , $greenbar, $user_adminlist = 0 )
{
	$memberstr = "<tr" ;
	if ( $greenbar != 0 ) {
		$memberstr .= ">" ;
	}
	else {
		$memberstr .= " bgcolor=\"#9AFE98\">" ;
	}
	$euid = $row["euid"] ;
	$fullname = $row["fullname"] ;
	$username = $row["username"] ;
	$memberstr .= "<td class=\"rostername\">"  . $fullname . "</td>\n" ;
	$memberstr .= "<td class=\"rosterphone\">( " . $username . " )</td>\n" ;
	if ( $user_adminlist > 1 ) {
		$memberstr .= "<td class=\"rostername\">" ;
		$memberstr .= "<form action=\"member.php\" method=\"post\" name=\"contact_$fullname\" id=\"contact_$fullname\">" ;
		$memberstr .= "<input type=\"hidden\" name=\"page\" id=\"page\" value=\"password_reset\"><input type=\"hidden\" name=\"euid\" id=\"euid\" value=\"$euid\"><input type=\"submit\" value=\"Reset Password\"></form>" ;
		$memberstr .= "</td>\n" ;
	}
	$memberstr .= "</tr>\n" ;
	return $memberstr ;
}

function write_roster( &$result , $user_adminlist = 0 , $select_val = "active" ) {
	$rosterstr = "<h3 align=\"center\">" . ucwords ( $select_val ) . " Members</h3>\n" ; 
	$rosterstr .= "<table class=\"roster\" cellspacing=\"0\" cellpadding=\"5\" width=\"100%\">\n<tr><th>Member</th><th>Username</th>" ;
	if ( $user_adminlist > 1 ) {
		$rosterstr .= "<th>Action</th>" ;
	}
	$rosterstr .= "</tr>\n" ;
	$i = 2 ;
	while ( $row = mysql_fetch_assoc( $result ) ) {
		$i++ ;
		$rosterstr .= writemember( $row , ( $i % 2 ) , $user_adminlist )  ;
	}
	$rosterstr .= "</table>\n" ;
	return $rosterstr ;
}

mysql_connect( $dbhost, $dbuser, $dbpw ) or die( mysql_error() );
mysql_select_db( $dbname ) or die(mysql_error());

if ( $_POST["euid"] && $user_admin > 1 ){
	// posted EUID value means this member needs his or her password reset
	// form results into a SQL query to insert the member data
	$update_sql = "UPDATE " . $tablepre . "members " ;
	$update_sql .= " \n SET pass = SHA1( username ) "  ;
	$update_sql .= "\n WHERE euid = " . $_POST["euid"] . " ;\n" ;
	//	echo $update_sql ;
	$result = mysql_query( $update_sql ) or die( mysql_error() ) ;
	$select_sql = "SELECT euid, username , lastname , firstname  FROM " . $tablepre . "members WHERE euid = " . $_POST["euid"] . " ;\n" ;
	$result = mysql_query( $select_sql ) or die( mysql_error() ) ;
	if ( mysql_num_rows( $result ) ) {
		while ( $row = mysql_fetch_assoc( $result ) ) {
			$euid = $row["euid"] ;
			$username = $row["username"] ;
			$lastname = $row["lastname"] ;
			$firstname = $row["firstname"] ;
			echo "<p>The password for $firstname $lastname (userid $username) has been reset.</p>\n" ;
		}
	}
}

$sort_val = ( $_GET["sort_val"] ) ? $_GET["sort_val"] : "none" ;
$select_val = ( $_GET["select_val"] ) ? $_GET["select_val"] : "active" ;
$sort_val = ( $_POST["sort_val"] ) ? $_POST["sort_val"] : $sort_val ;
$group_val = ( $_GET["group_val"] ) ? $_GET["group_val"] : "none" ;
$select_val = ( $_POST["select_val"] ) ? $_POST["select_val"] : $select_val ;
$group_val = ( $_POST["group_val"] ) ? $_POST["group_val"] : $group_val ;
$sql_list = select_query( $tablepre ) ;
$result = mysql_query( $sql_list ) or die( mysql_error() ) ;
if ( mysql_num_rows( $result ) ) {
	echo write_roster( $result , $user_admin , $select_val ) ;
}
else {
	echo "<h2>We have an oops!</h2>" ;
}

?>
<!-- password_reset.inc.php -->
