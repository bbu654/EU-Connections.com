<?php

function select_query( $sort = "alpha", $select = "active",  $grouping = "none", $tablepre = "euconnect_" ) {
	// function to create roster query
	$select_sql =  " " ;
	$sort_sql = " ORDER BY fullname ASC" ;
	$group_sql = " " ;
	$sql = "SELECT m.euid , RTRIM( CONCAT(m.lastname,\", \", m.firstname) ) AS fullname , IFNULL( p.posit_descr, \"no profile\" ) AS posit_descr , RTRIM( m.email_home ) AS email, RTRIM( CONCAT(m.voice_primary,\" \", m.voice_ext) ) AS voice , m.jumpstart FROM " . $tablepre . "members AS m LEFT JOIN " . $tablepre . "profiles AS p ON m.euid = p.euid WHERE ( p.priority = 1 OR p.priority IS NULL ) " ;
	switch( $sort ){
		case "alpha" :
    	    $sort_sql = " ORDER BY fullname ASC" ;
			break ;
		case "numeric" :
    	    $sort_sql = " ORDER BY euid ASC" ;
			break ;
		case "enroll" :
    	    $sort_sql = " ORDER BY m.enrolled ASC, fullname ASC" ;
			break ;
		case "enroll_new" :
    	    $sort_sql = " ORDER BY m.enrolled DES, fullname ASC" ;
			break ;
		case "jumpstart" :
    	    $sort_sql = " ORDER BY m.jumpstart ASC, fullname ASC" ;
			break ;
		default:
    	    $sort_sql = " ORDER BY fullname ASC" ;
			break ;
	}
	switch( $group ){
		case "none" :
    	    $group_sql = " " ;
	        break ;
		default:
    	    $group_sql = " " ;
			break ;
	}
	switch( $select ){
		case "active" :
    	    $select_sql =  " AND m.active = 1 " ;
	        break ;
		case "inactive" :
    	    $select_sql =  " AND m.active = 0 " ;
	        break ;
		case "all" :
    	    $select_sql =  " " ;
	        break ;
		default:
    	    $select_sql = " AND m.active = 1 " ;
			break ;
	}

	return $sql . $select_sql . $group_sql . $sort_sql ;
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
	$posit_descr = $row["posit_descr"] ;
	$email = $row["email"] ;
	$voice = $row["voice"] ;
	$jumpstart = $row["jumpstart"] ;
	$memberstr .= "<td class=\"rostername\">"  . $fullname . "<br /><font size=\"-1\">(" . $posit_descr . ")</font></td>\n" ;
	//	$memberstr .= "<td class=\"rostername\"><a href=\"mailto:" . $email . "\" >" . $fullname . "</a><br /><font size=\"-1\">(" . $posit_descr . ")</font></td>\n" ;
	$memberstr .= "<td class=\"rosterphone\">" . $voice . "<br />\n" ;
	$memberstr .= "<a href=\"mailto:" . $email . "\" >" . $email . "</a></td>\n" ;
	switch ( $user_adminlist ){
		case 2 :
			$memberstr .= "<td class=\"rostername\">" ;
			$memberstr .= "<form action=\"member.php\" method=\"post\" name=\"contact_$fullname\" id=\"contact_$fullname\">" ;
			$memberstr .= "<input type=\"hidden\" name=\"page\" id=\"page\" value=\"member\"><input type=\"hidden\" name=\"euid\" id=\"euid\" value=\"$euid\"><input type=\"submit\" value=\"Update Contact Info\"></form>\n" ;
			$memberstr .= "<form action=\"member.php\" method=\"post\" name=\"profile_$fullname\" id=\"profile_$fullname\">" ;
			$memberstr .= "<input type=\"hidden\" name=\"page\" id=\"page\" value=\"update_profiles\"><input type=\"hidden\" name=\"euid\" id=\"euid\" value=\"$euid\"><input type=\"submit\" value=\"Update Profile(s)\"></form></td>\n" ;
			$memberstr .= "<td class=\"rosterdate\">" . sql_to_cal( $jumpstart ) . "</td>\n" ;
			break ;
		case 0 || 1 :
			break ;
		default :
			break ;
	}
	$memberstr .= "</tr>\n" ;
	return $memberstr ;
}

function write_roster( &$result , $user_adminlist = 0 , $select_val = "active" ) {
	$rosterstr = "<h3 align=\"center\">" . ucwords ( $select_val ) . " Members</h3>\n" ; 
	$rosterstr .= "<table class=\"roster\" cellspacing=\"0\" cellpadding=\"5\" width=\"100%\">\n<tr><th>Member</th><th>Voice<br />email</th>" ;
	if ( $user_adminlist > 1 ) {
	$rosterstr .= "<th>Action</th><th>JumpStart Due</th>" ;
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

$sort_val = ( $_GET["sort_val"] ) ? $_GET["sort_val"] : "none" ;
$select_val = ( $_GET["select_val"] ) ? $_GET["select_val"] : "active" ;
$sort_val = ( $_POST["sort_val"] ) ? $_POST["sort_val"] : $sort_val ;
$group_val = ( $_GET["group_val"] ) ? $_GET["group_val"] : "none" ;
$select_val = ( $_POST["select_val"] ) ? $_POST["select_val"] : $select_val ;
$group_val = ( $_POST["group_val"] ) ? $_POST["group_val"] : $group_val ;
$sql_list = select_query( $sort_val , $select_val , $group_val , $tablepre ) ;
$result = mysql_query( $sql_list ) or die( mysql_error() ) ;
if ( mysql_num_rows( $result ) ) {
	echo write_roster( $result , $user_admin , $select_val ) ;
}
else {
	echo "<h2>We have an oops!</h2>" ;
}

?>
<!-- roster.inc.php -->
