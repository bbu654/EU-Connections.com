<?php

function select_query( $sort = "alpha", $grouping = "none", $select = "all",  $tablepre = "euconnect_" ) {
	// function to create roster query
	$sql = "SELECT m.euid , RTRIM( CONCAT(m.lastname,\", \", m.firstname) ) AS fullname , IFNULL( p.posit_descr, \"none\" ) AS posit_descr , RTRIM( m.email_home ) AS email, RTRIM( CONCAT(m.voice_primary,\" \", m.voice_ext) ) AS voice , m.jumpstart FROM " . $tablepre . "members AS m LEFT JOIN " . $tablepre . "profiles AS p ON m.euid = p.euid AND m.category_id_primary = p.cat_id" ;
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
    	    $sort_sql = " ORDER BY lastname ASC, firstname ASC" ;
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
    	    $select_sql =  " WHERE m.active = 1 " ;
	        break ;
		case "all" :
    	    $select_sql =  " " ;
	        break ;
		default:
    	    $select_sql = " WHERE m.active = 1 " ;
			break ;
	}

	return $sql . $select_sql . $group_sql . $sort_sql ;
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
	switch ( $user_adminlist ){
		case 0 || 1 :
			$memberstr .= "<td class=\"rostername\"><a href=\"mailto:" . $email . "\" >" . $fullname . "</a><br /><font size=\"-1\">(" . $posit_descr . ")</font></td>\n" ;
			break ;
		case 2 :
			$memberstr .= "<td class=\"rostername\">" ;
			$memberstr .= "<form action=\"member.php\" method=\"post\" name=\"$fullname\" id=\"$fullname\">" ;
			$memberstr .= "<input type=\"hidden\" name=\"page\" id=\"page\" value=\"record\"><input type=\"hidden\" name=\"euid\" id=\"euid\" value=\"$euid\"><input type=\"submit\" value=\"$fullname\"><br />" ;
			$memberstr .= "<font size=\"-1\">(" . $posit_descr . ")</font></form></td>\n" ;
			break ;
	}
	$memberstr .= "<td class=\"rosterphone\">" . $voice . "</td>\n" ;
	$memberstr .= "<td class=\"rostermail\"><a href=\"mailto:" . $email . "\" >" . $email . "</a></td>\n" ;
	if ( $user_adminlist > 1 ) {
	$memberstr .= "<td class=\"rosterdate\">" . sql_to_cal( $jumpstart ) . "</td>\n" ;
	}
	$memberstr .= "</tr>\n" ;
	return $memberstr ;

}

function write_roster( &$result , $user_adminlist = 0 ) {
	$rosterstr = "<table class=\"roster\" cellspacing=\"0\" cellpadding=\"5\" width=\"100%\">\n<tr><th>Member</th><th>Voice</th><th>email</th>" ;
	if ( $user_adminlist > 1 ) {
	$rosterstr .= "<th>JumpStart Due</th>" ;
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

$sort_val = ( $_POST["sort_val"] ) ? $_POST["sort_val"] : "none" ;
$group_val = ( $_POST["group_val"] ) ? $_POST["group_val"] : "none" ;
$select_val = ( $_POST["select_val"] ) ? $_POST["select_val"] : "all" ;
$sql_list = select_query( $sort_val , $group_val , $select_val , $tablepre ) ;
$result = mysql_query( $sql_list ) or die( mysql_error() ) ;
if ( mysql_num_rows( $result ) ) {
	echo write_roster( $result , $user_admin ) ;
}
else {
	echo "<h2>We have an oops!</h2>" ;
}

?>
<!-- roster.inc.php -->
