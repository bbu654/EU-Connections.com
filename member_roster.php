<?php
	session_start();
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
	$page_name = str_replace ( "include/", "console/" , $page_name ) ;
	$page_name = str_replace ( ".xls", ".php" , $page_name ) ;
	$user_euid = ( $_GET["ueuid"] ) ? $_GET["ueuid"] : $user_euid ;
	$user_euid = ( $_POST["ueuid"] ) ? $_POST["ueuid"] : $user_euid ;
	$user_admin = ( $_POST["user_admin"] ) ? $_POST["user_admin"] : $user_admin ;


//	MySQL database settings

	$dbhost = "localhost";			// database server
	$tablepre = "euconnect_";		//  Prefix added to data table names.
	$dbname = "eu-connections_org_-_main";		// database name
//	if ( strpos ( strtolower( $current_path ) , "preview" ) ) { 
//		$dbname = "eu-conne_preview";		// database name
//	}

	$dbuser = "euconnect";   	// database username
	$dbpw = "tango25";   			// database password 			// database password

//  Language selection value (currently unused)
	$language = "en";

/* authenticate username/password
	returns: -1 if username and password is incorrect
          0 if username exists and password is incorrect
          1 if username and password are correct
*/
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
			$mon_str = "JAN" ;
			break ;
		case "02" :
			$mon_str = "FEB" ;
			break ;
		case "03" :
			$mon_str = "MAR" ;
			break ;
		case "04" :
			$mon_str = "APR" ;
			break ;
		case "05" :
			$mon_str = "MAY" ;
			break ;
		case "06" :
			$mon_str = "JUN" ;
			break ;
		case "07" :
			$mon_str = "JUL" ;
			break ;
		case "08" :
			$mon_str = "AUG" ;
			break ;
		case "09" :
			$mon_str = "SEP" ;
			break ;
		case "10" :
			$mon_str = "OCT" ;
			break ;
		case "11" :
			$mon_str = "NOV" ;
			break ;
		case "12" :
			$mon_str = "DEC" ;
			break ;
	}
	return $day_str . " " . $mon_str . " " . $year_str ;
}


function date_DMY_to_YMD( $date_str ) {
	$date_array = explode( " " , $date_str ) ;
	return $date_array[ 2 ] . " " . $date_array[ 1 ] . " " . $date_array[ 0 ] ;
}


function timestamp_to_cal( $date_str ) {
	// converts mySQL date format (yyyy-mm-dd) to U.S. date format ( day month year)
	$date_array = explode( "-" , $date_str ) ;
	$year_str = substr( $date_str , 0 , 4 ) ;
	$day_str = substr( $date_str , 6 , 2 ) ;
	switch( substr( $date_str , 4 , 2 ) ) {
		case "01" :
			$mon_str = "JAN" ;
			break ;
		case "02" :
			$mon_str = "FEB" ;
			break ;
		case "03" :
			$mon_str = "MAR" ;
			break ;
		case "04" :
			$mon_str = "APR" ;
			break ;
		case "05" :
			$mon_str = "MAY" ;
			break ;
		case "06" :
			$mon_str = "JUN" ;
			break ;
		case "07" :
			$mon_str = "JUL" ;
			break ;
		case "08" :
			$mon_str = "AUG" ;
			break ;
		case "09" :
			$mon_str = "SEP" ;
			break ;
		case "10" :
			$mon_str = "OCT" ;
			break ;
		case "11" :
			$mon_str = "NOV" ;
			break ;
		case "12" :
			$mon_str = "DEC" ;
			break ;
	}
	return $day_str . " " . $mon_str . " " . $year_str ;
}


// test for authenticated
if ( !isset( $user_uname ) || $user_uname == "" || $_SESSION[ "uun" ] != $user_uname || !isset( $user_admin ) || $user_admin < 2 ) { 
	session_start();
	header("Location: http://".$_SERVER['HTTP_HOST']
                      .dirname($_SERVER['PHP_SELF'])
                      ."index.php" ) ;
	session_destroy();
	exit;
}
else {

header('Pragma: private');
header('Cache-control: private, must-revalidate');
//	header('Content-type: text/xml');
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=\"EU_Roster_" . str_replace ( " " , "" , date_DMY_to_YMD( sql_to_cal( date ( "Y-m-d" ) ) ) ) . ".xls\"") ;

$timestring = date ( "H:j:s" ) ;
$datestring = date ( "Y-m-d" ) ;

function member_select_query( $select = "active",  $tablepre = "euconnect_" ) {
	// function to create roster query
	$sql_memberfetch = "SELECT m.euid , m.active, m.enrolled, m.jumpstart, m.updated, m.userlevel " ;
	$sql_memberfetch .= ", IFNULL( p.priority, 0 ) AS profilecount , IFNULL( p.cat_id, \"  \" ) AS category_id_primary , IFNULL( p.posit_descr, \"\" ) AS posit_descr , IFNULL( c.cat_descr, \"\" ) AS career_field " ;
	$sql_memberfetch .= ", RTRIM( m.username ) AS username , \"\" AS pass, RTRIM( m.lastname ) AS lastname , RTRIM( m.firstname ) AS firstname " ;
	$sql_memberfetch .= ", RTRIM( m.email_home ) AS email_home, RTRIM( m.voice_primary ) AS voice_primary , RTRIM( m.voice_ext ) AS voice_ext " ;
	$sql_memberfetch .= ", RTRIM( m.voice_mobile ) AS voice_mobile , RTRIM( m.fascimile ) AS fascimile " ;
	$sql_memberfetch .= " FROM " . $tablepre . "members AS m LEFT JOIN " . $tablepre . "profiles AS p ON m.euid = p.euid " ;
	$sql_memberfetch .= " LEFT JOIN " . $tablepre . "profile_cat AS c ON c.cat_id = p.cat_id " ;
	$sql_memberfetch .= " WHERE ( p.priority = 1 OR p.priority IS NULL )" ;
	//	$sql = "SELECT m.euid , RTRIM( CONCAT(m.lastname,\", \", m.firstname) ) AS fullname , IFNULL( p.posit_descr, \"none\" ) AS posit_descr , RTRIM( m.email_home ) AS email, RTRIM( CONCAT(m.voice_primary,\" \", m.voice_ext) ) AS voice , m.jumpstart FROM " . $tablepre . "members AS m LEFT JOIN " . $tablepre . "profiles AS p ON m.euid = p.euid WHERE ( p.priority = 1 OR p.priority IS NULL ) " ;
	switch( $select ){
		case "active" :
    	    $select_sql =  " AND m.active = 1 " ;
	        break ;
		case "alumni" :
    	    $select_sql =  " AND m.active = 0 " ;
	        break ;
		case "all" :
    	    $select_sql =  " " ;
	        break ;
		default:
    	    $select_sql = " AND m.active = 1 " ;
			break ;
	}
	$sort_sql = " ORDER BY lastname ASC, firstname ASC" ;
	return $sql_memberfetch . $select_sql . $sort_sql ;
}

function profile_select_query( $select = "active",  $tablepre = "euconnect_" ){
	// function to create roster query
	$sql_memberfetch = " SELECT	m.lastname , m.firstname , m.active " ;
	$sql_memberfetch .= ", prof_id , p.euid , released , p.updated " ;
	$sql_memberfetch .= ", cat_id , posit_descr , overview " ;
	$sql_memberfetch .= ", bullet1 , bullet2 , bullet3 , bullet4 , bullet5 " ;
	$sql_memberfetch .= "FROM euconnect_profiles p " ;
	$sql_memberfetch .= " INNER JOIN euconnect_members m " ;
	$sql_memberfetch .= " ON p.euid = m.euid " ;
	$sql_memberfetch .= " WHERE overview NOT LIKE '' " ;
	//	$sql = "SELECT m.euid , RTRIM( CONCAT(m.lastname,\", \", m.firstname) ) AS fullname , IFNULL( p.posit_descr, \"none\" ) AS posit_descr , RTRIM( m.email_home ) AS email, RTRIM( CONCAT(m.voice_primary,\" \", m.voice_ext) ) AS voice , m.jumpstart FROM " . $tablepre . "members AS m LEFT JOIN " . $tablepre . "profiles AS p ON m.euid = p.euid WHERE ( p.priority = 1 OR p.priority IS NULL ) " ;
	switch( $select ){
		case "active" :
    	    $select_sql =  " AND m.active = 1 " ;
	        break ;
		case "alumni" :
    	    $select_sql =  " AND m.active = 0 " ;
	        break ;
		case "all" :
    	    $select_sql =  " " ;
	        break ;
		default:
    	    $select_sql = " AND m.active = 1 " ;
			break ;
	}
	$sort_sql = " ORDER BY released DESC , cat_id ASC , lastname ASC , firstname ASC " ;
	return $sql_memberfetch . $select_sql . $sort_sql ;

}
function writemember( $row , $greenbar, $iscurrent , $user_adminlist = 0 )
{
	$memberstr = "<tr" ;
	if ( $row["userlevel"] > 1 ) {
   	$memberstr = "   <Row ss:StyleID=\"EUadminaccess\" ss:Height=\"20\">\n" ;
	}
	elseif ( $greenbar != 0 ) {
   	$memberstr = "   <Row ss:StyleID=\"EUgreenbar\" ss:Height=\"20\">\n" ;
	}
	else {
   	$memberstr = "   <Row ss:Height=\"20\">\n" ;
	}
	$euid = $row["euid"] ;
	$fullname = $row["fullname"] ;
	$posit_descr = $row["posit_descr"] ;
	$email = $row["email"] ;
	$voice = $row["voice"] ;
	$jumpstart = $row["jumpstart"] ;
//	Last Name
   	$memberstr .= "    <Cell ss:StyleID=\"EUDataLeftJust\"><Data ss:Type=\"String\"><![CDATA[" .  $row["lastname"] . "]]></Data></Cell>\n" ;
//	First Name
   	$memberstr .= "    <Cell ss:StyleID=\"EUDataLeftJust\"><Data ss:Type=\"String\"><![CDATA[" .  $row["firstname"] . "]]></Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
//	E-Mail
   	$memberstr .= "    <Cell ss:StyleID=\"EUDataLeftJust\"><Data ss:Type=\"String\"><![CDATA[" .  $row["email_home"] . "]]></Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
//	Voice ' voice_ext
	if ( $voice_ext != "" ) {
	   	$memberstr .= "    <Cell ss:StyleID=\"EUDataLeftJust\"><Data ss:Type=\"String\"><![CDATA[" .  $row["voice_primary"] . "ext. " . $row["voice_ext"] . "]]></Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
	}
	else {
	   	$memberstr .= "    <Cell ss:StyleID=\"EUDataLeftJust\"><Data ss:Type=\"String\"><![CDATA[" .  $row["voice_primary"] . "]]></Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
	}
//	Cell
   	$memberstr .= "    <Cell ss:StyleID=\"EUDataLeftJust\"><Data ss:Type=\"String\"><![CDATA[" .  $row["voice_mobile"] . "]]></Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
//	FAX
   	$memberstr .= "    <Cell ss:StyleID=\"EUDataLeftJust\"><Data ss:Type=\"String\"><![CDATA[" .  $row["fascimile"] . "]]></Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
//	Job Title
   	$memberstr .= "    <Cell ss:StyleID=\"EUDataLeftJust\"><Data ss:Type=\"String\"><![CDATA[" .  $row["posit_descr"] . "]]></Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
//	Career Field
	if ( $iscurrent == 1 ){
		$memberstr .= "    <Cell ss:StyleID=\"EUDataLeftJust\"><Data ss:Type=\"String\"><![CDATA[" .  $row["career_field"] . "]]></Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
	}
//	Web Site User ID
   	$memberstr .= "    <Cell ss:StyleID=\"EUDataLeftJust\"><Data ss:Type=\"String\"><![CDATA[" .  $row["username"] . "]]></Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
//	Web Site ID No.
   	$memberstr .= "    <Cell ss:StyleID=\"EUDataCenterJust\"><Data ss:Type=\"Number\"><![CDATA[" .  $row["euid"] . "]]></Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
//	Enrolled
   	$memberstr .= "    <Cell ss:StyleID=\"EUDataCenterJust\"><Data ss:Type=\"String\"><![CDATA[" .  sql_to_cal( $row["enrolled"] ) . "]]></Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
//	Jump Start
   	
	if ( $iscurrent == 1 ){
		$memberstr .= "    <Cell ss:StyleID=\"EUDataCenterJust\"><Data ss:Type=\"String\"><![CDATA[" .  sql_to_cal( $row["jumpstart"] ) . "]]></Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
	}
//	Contact Info Updated
	if ( $iscurrent == 1 ){
//		$memberstr .= "    <Cell ss:StyleID=\"EUDataCenterJust\"><Data ss:Type=\"String\"><![CDATA[" .  $row["updated"] . "]]></Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
   	}
   	$memberstr .= "   </Row>\n" ;
	return $memberstr ;

}

function writeprofile( $row , $user_adminlist = 0 )
{
/*	if ( $row["active"] != 1 ) {
   		$profilestyle = "EUProfileContent" ;
   		$profilestat = "Inactive" ;
	}
	else
*/	if ( $row["released"] == 1 ) {
   		$profilestyle = "EUProfileReleased" ;
   		$profilestat = "Released" ;
	}
	elseif ( $row["released"] == -1 ) {
   		$profilestyle = "EUProfileRejected" ;
   		$profilestat = "Pending Revision" ;
	}
	else {
   		$profilestyle = "EUProfilePending" ;
   		$profilestat = "Pending Review" ;
	}
   	$profilestr = "   <Row>\n" ;
//	Last Name
   	$profilestr .= "    <Cell ss:StyleID=\"" . $profilestyle . "\"><Data ss:Type=\"String\"><![CDATA[" .  $row["lastname"] . "]]></Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
//	First Name
   	$profilestr .= "    <Cell ss:StyleID=\"" . $profilestyle . "\"><Data ss:Type=\"String\"><![CDATA[" .  $row["firstname"] . "]]></Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
//	Profile ID
   	$profilestr .= "    <Cell ss:StyleID=\"" . $profilestyle . "\"><Data ss:Type=\"String\"><![CDATA[" .  $row["cat_id"] . "-" . $row["euid"] . "]]></Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
//	Status
   	$profilestr .= "    <Cell ss:StyleID=\"" . $profilestyle . "\"><Data ss:Type=\"String\">" . $profilestat . "</Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
  	$profilestr .= "    <Cell ss:StyleID=\"EUProfileContent\"><Data ss:Type=\"String\"><![CDATA[" .  timestamp_to_cal( $row["updated"] ) . "]]></Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;

//	Job Title
   	$profilestr .= "    <Cell ss:StyleID=\"EUProfileContent\"><Data ss:Type=\"String\"><![CDATA[" .  $row["posit_descr"] . "]]></Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
   	$profilestr .= "    <Cell ss:StyleID=\"EUProfileContent\"><Data ss:Type=\"String\"><![CDATA[" .  $row["overview"] . "]]></Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
   	$profilestr .= "    <Cell ss:StyleID=\"EUProfileContent\"><Data ss:Type=\"String\"><![CDATA[" .  $row["bullet1"] . "]]></Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
	$profilestr .= "    <Cell ss:StyleID=\"EUProfileContent\"><Data ss:Type=\"String\"><![CDATA[" .  $row["bullet2"] . "]]></Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
   	$profilestr .= "    <Cell ss:StyleID=\"EUProfileContent\"><Data ss:Type=\"String\"><![CDATA[" .  $row["bullet3"] . "]]></Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
   	$profilestr .= "    <Cell ss:StyleID=\"EUProfileContent\"><Data ss:Type=\"String\"><![CDATA[" .  $row["bullet4"] . "]]></Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
   	$profilestr .= "    <Cell ss:StyleID=\"EUProfileContent\"><Data ss:Type=\"String\"><![CDATA[" .  $row["bullet5"] . "]]></Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
   	$profilestr .= "   </Row>\n" ;
	return $profilestr ;

}


function write_profilesheet( &$result , $select = "active" ,  $sheetname = "EU Active Member Profiles" , $user_adminlist = 0 ) {
	switch( $select ){
		case "active" :
    	    $selectext =  "Active Member" ;
	        break ;
		case "alumni" :
    	    $selectext =  "Alumni" ;
	        break ;
		case "all" :
    	    $selectext =  "" ;
	        break ;
		default:
    	    $selectext = "Active Member" ;
			break ;
	}
   	$worksheet = " <Worksheet ss:Name=\"" . $sheetname . "\">\n" ; 
	$printrows = ( mysql_num_rows( $result ) ) ? mysql_num_rows( $result ) + 5 : 5 ;  	
   	$worksheet .= "  <Names>\n" ;
   	$worksheet .= "   <NamedRange ss:Name=\"Print_Area\" ss:RefersTo=\"='" . $sheetname . "'!R1C1:R" . $printrows . "C12\"/>\n" ;
   	$worksheet .= "   <NamedRange ss:Name=\"Print_Titles\" ss:RefersTo=\"='" . $sheetname . "'!R1:R3\"/>\n" ;
   	$worksheet .= "  </Names>\n" ;
   	$worksheet .= "  <Table x:FullColumns=\"1\" x:FullRows=\"1\">\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"125\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"95\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"60\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"85\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"75\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"170\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"205\" ss:Hidden=\"0\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"205\" ss:Hidden=\"0\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"205\" ss:Hidden=\"0\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"205\" ss:Hidden=\"0\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"205\" ss:Hidden=\"0\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"205\" ss:Hidden=\"0\"/>\n" ;
//   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"75\"/>\n" ;
   	$worksheet .= "   <Row>\n" ;
   	$worksheet .= "    <Cell ss:MergeAcross=\"11\" ss:StyleID=\"HeaderTitle\"><Data ss:Type=\"String\">Experience Unlimited " . $selectext . " Profiles</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "   </Row>\n" ;
   	$worksheet .= "   <Row>\n" ;
   	$worksheet .= "    <Cell ss:MergeAcross=\"11\" ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">" . sql_to_cal( date ( "Y-m-d" ) ) . "</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "   </Row>\n" ;
   	$worksheet .= "   <Row>\n" ;
   	/*
	   m.lastname, m.firstname , m.active ,
	   prof_id, p.euid, released,
	   cat_id, posit_descr, overview,
	   bullet1, bullet2, bullet3,
	bullet4, bullet5
   	*/
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">Last Name</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">First Name</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">Profile ID</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">Released?</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">Updated</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;

 	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">Title</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">Overview</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">Bullet 1</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">Bullet 2</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">Bullet 3</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">Bullet 4</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">Bullet 5</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;

   	$worksheet .= "   </Row>\n" ;
	$i = 2 ;
	while ( $row = mysql_fetch_assoc( $result ) ) {
		$i++ ;
		$worksheet .= writeprofile( $row , $user_adminlist )  ;
	}
   	$worksheet .= "   <Row>\n" ;
   	$worksheet .= "    <Cell ss:MergeAcross=\"11\" ss:StyleID=\"EUfootnotespacer\"><Data ss:Type=\"String\"></Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
   	$worksheet .= "   </Row>\n" ;
   	$worksheet .= "   <Row>\n" ;
   	$worksheet .= "    <Cell ss:MergeAcross=\"11\" ss:StyleID=\"EUfootnote\"><Data ss:Type=\"String\">Property of the Anaheim, CA chapter of Experience Unlimited. For EU use only.</Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
   	$worksheet .= "   </Row>\n</Table>\n" ;
   	$worksheet .= "  <WorksheetOptions xmlns=\"urn:schemas-microsoft-com:office:excel\">\n" ;
   	$worksheet .= "   <PageSetup>\n" ;
   	$worksheet .= "    <Layout x:Orientation=\"Landscape\"/>\n" ;
   	$worksheet .= "    <Header x:Margin=\"0.27\"/>\n" ;
   	$worksheet .= "    <Footer x:Margin=\"0.28\" x:Data=\"Page &amp;P of &amp;N\"/>\n" ;
   	$worksheet .= "    <PageMargins x:Bottom=\"0.49\" x:Left=\"0.25\" x:Right=\"0.25\" x:Top=\"0.54\"/>\n" ;
   	$worksheet .= "   </PageSetup>\n" ;
   	$worksheet .= "   <FitToPage/>\n" ;
   	$worksheet .= "   <Selected/>\n" ;
   	$worksheet .= "   <FreezePanes/>\n" ;
   	$worksheet .= "   <FrozenNoSplit/>\n" ;
   	$worksheet .= "   <SplitHorizontal>3</SplitHorizontal>\n" ;
   	$worksheet .= "   <TopRowBottomPane>3</TopRowBottomPane>\n" ;
   	$worksheet .= "   <ActivePane>2</ActivePane>\n" ;
   	$worksheet .= "   <Panes>\n" ;
   	$worksheet .= "    <Pane>\n" ;
   	$worksheet .= "     <Number>3</Number>\n" ;
   	$worksheet .= "    </Pane>\n" ;
   	$worksheet .= "    <Pane>\n" ;
   	$worksheet .= "     <Number>2</Number>\n" ;
   	$worksheet .= "     <ActiveRow>20</ActiveRow>\n" ;
   	$worksheet .= "     <RangeSelection>R4C1:R4C1</RangeSelection>\n" ;
   	$worksheet .= "    </Pane>\n" ;
   	$worksheet .= "   </Panes>\n" ;
   	$worksheet .= "   <ProtectObjects>False</ProtectObjects>\n" ;
   	$worksheet .= "   <ProtectScenarios>False</ProtectScenarios>\n" ;
   	$worksheet .= "  </WorksheetOptions>\n" ;
   	$worksheet .= " </Worksheet>\n" ;
	return 	$worksheet ;
}


function write_membersheet( &$result , $sheetname = "Active Members" , $user_adminlist = 0 ) {
   	$worksheet = " <Worksheet ss:Name=\"" . $sheetname . "\">\n" ; 
	$printrows = ( mysql_num_rows( $result ) ) ? mysql_num_rows( $result ) + 5 : 5 ;  	
   	$worksheet .= "  <Names>\n" ;
   	$worksheet .= "   <NamedRange ss:Name=\"Print_Area\" ss:RefersTo=\"='" . $sheetname . "'!R1C1:R" . $printrows . "C12\"/>\n" ;
   	$worksheet .= "   <NamedRange ss:Name=\"Print_Titles\" ss:RefersTo=\"='" . $sheetname . "'!R1:R3\"/>\n" ;
   	$worksheet .= "  </Names>\n" ;
   	$worksheet .= "  <Table x:FullColumns=\"1\" x:FullRows=\"1\">\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"125\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"95\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"220\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"85\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"85\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"85\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"205\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"175\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"75\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"70\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"75\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"75\"/>\n" ;
//   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"75\"/>\n" ;
   	$worksheet .= "   <Row>\n" ;
   	$worksheet .= "    <Cell ss:MergeAcross=\"11\" ss:StyleID=\"HeaderTitle\"><Data ss:Type=\"String\">Experience Unlimited Active Member Roster</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "   </Row>\n" ;
   	$worksheet .= "   <Row>\n" ;
   	$worksheet .= "    <Cell ss:MergeAcross=\"11\" ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">" . sql_to_cal( date ( "Y-m-d" ) ) . "</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "   </Row>\n" ;
   	$worksheet .= "   <Row>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">Last Name</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">First Name</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">E-Mail</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">Voice</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">Cell</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">FAX</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">Title</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">Career Field</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">Web Site UserName</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">EUID (Web Site No.)</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">Enrolled</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">Jump Start</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
//   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">Roster Updated</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "   </Row>\n" ;
	$i = 2 ;
	while ( $row = mysql_fetch_assoc( $result ) ) {
		$i++ ;
		$worksheet .= writemember( $row , ( $i % 2 ) , 1 , $user_adminlist )  ;
	}
   	$worksheet .= "   <Row>\n" ;
   	$worksheet .= "    <Cell ss:MergeAcross=\"11\" ss:StyleID=\"EUfootnotespacer\"><Data ss:Type=\"String\"></Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
   	$worksheet .= "   </Row>\n" ;
   	$worksheet .= "   <Row>\n" ;
   	$worksheet .= "    <Cell ss:MergeAcross=\"11\" ss:StyleID=\"EUfootnote\"><Data ss:Type=\"String\">Property of the Anaheim, CA chapter of Experience Unlimited. For EU use only.</Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
   	$worksheet .= "   </Row>\n</Table>\n" ;
   	$worksheet .= "  <WorksheetOptions xmlns=\"urn:schemas-microsoft-com:office:excel\">\n" ;
   	$worksheet .= "   <PageSetup>\n" ;
   	$worksheet .= "    <Layout x:Orientation=\"Landscape\"/>\n" ;
   	$worksheet .= "    <Header x:Margin=\"0.27\"/>\n" ;
   	$worksheet .= "    <Footer x:Margin=\"0.28\" x:Data=\"Page &amp;P of &amp;N\"/>\n" ;
   	$worksheet .= "    <PageMargins x:Bottom=\"0.49\" x:Left=\"0.25\" x:Right=\"0.25\" x:Top=\"0.54\"/>\n" ;
   	$worksheet .= "   </PageSetup>\n" ;
   	$worksheet .= "   <FitToPage/>\n" ;
   	$worksheet .= "   <Selected/>\n" ;
   	$worksheet .= "   <FreezePanes/>\n" ;
   	$worksheet .= "   <FrozenNoSplit/>\n" ;
   	$worksheet .= "   <SplitHorizontal>3</SplitHorizontal>\n" ;
   	$worksheet .= "   <TopRowBottomPane>3</TopRowBottomPane>\n" ;
   	$worksheet .= "   <ActivePane>2</ActivePane>\n" ;
   	$worksheet .= "   <Panes>\n" ;
   	$worksheet .= "    <Pane>\n" ;
   	$worksheet .= "     <Number>3</Number>\n" ;
   	$worksheet .= "    </Pane>\n" ;
   	$worksheet .= "    <Pane>\n" ;
   	$worksheet .= "     <Number>2</Number>\n" ;
   	$worksheet .= "     <ActiveRow>20</ActiveRow>\n" ;
   	$worksheet .= "     <RangeSelection>R4C1:R4C1</RangeSelection>\n" ;
   	$worksheet .= "    </Pane>\n" ;
   	$worksheet .= "   </Panes>\n" ;
   	$worksheet .= "   <ProtectObjects>False</ProtectObjects>\n" ;
   	$worksheet .= "   <ProtectScenarios>False</ProtectScenarios>\n" ;
   	$worksheet .= "  </WorksheetOptions>\n" ;
   	$worksheet .= " </Worksheet>\n" ;
	return 	$worksheet ;
}


function write_alumnisheet( &$result , $sheetname = "EU Alumni" , $user_adminlist = 0 ) {
   	$worksheet = " <Worksheet ss:Name=\"" . $sheetname . "\">\n" ; 
	$printrows = ( mysql_num_rows( $result ) ) ? mysql_num_rows( $result ) + 5 : 5 ;  	
   	$worksheet .= "  <Names>\n" ;
   	$worksheet .= "   <NamedRange ss:Name=\"Print_Area\" ss:RefersTo=\"='" . $sheetname . "'!R1C1:R" . $printrows . "C10\"/>\n" ;
   	$worksheet .= "   <NamedRange ss:Name=\"Print_Titles\" ss:RefersTo=\"='" . $sheetname . "'!R1:R3\"/>\n" ;
   	$worksheet .= "  </Names>\n" ;
   	$worksheet .= "  <Table x:FullColumns=\"1\" x:FullRows=\"1\">\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"125\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"95\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"220\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"85\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"85\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"85\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"205\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"75\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"70\"/>\n" ;
   	$worksheet .= "   <Column ss:AutoFitWidth=\"0\" ss:Width=\"75\"/>\n" ;
   	$worksheet .= "   <Row>\n" ;
   	$worksheet .= "    <Cell ss:MergeAcross=\"9\" ss:StyleID=\"HeaderTitle\"><Data ss:Type=\"String\">Experience Unlimited Alumni Roster</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "   </Row>\n" ;
   	$worksheet .= "   <Row>\n" ;
   	$worksheet .= "    <Cell ss:MergeAcross=\"9\" ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">" . sql_to_cal( date ( "Y-m-d" ) ) . "</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "   </Row>\n" ;
   	$worksheet .= "   <Row>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">Last Name</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">First Name</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">E-Mail</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">Voice</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">Cell</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">FAX</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">Title</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">Web Site UserName</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">EUID (Web Site No.)</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "    <Cell ss:StyleID=\"HeaderColumnName\"><Data ss:Type=\"String\">Enrolled</Data><NamedCell ss:Name=\"Print_Area\"/><NamedCell ss:Name=\"Print_Titles\"/></Cell>\n" ;
   	$worksheet .= "   </Row>\n" ;
	$i = 2 ;
	while ( $row = mysql_fetch_assoc( $result ) ) {
		$i++ ;
		$worksheet .= writemember( $row , ( $i % 2 ) , 0 , $user_adminlist )  ;
	}
   	$worksheet .= "   <Row>\n" ;
   	$worksheet .= "    <Cell ss:MergeAcross=\"9\" ss:StyleID=\"EUfootnotespacer\"><Data ss:Type=\"String\"></Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
   	$worksheet .= "   </Row>\n" ;
   	$worksheet .= "   <Row>\n" ;
   	$worksheet .= "    <Cell ss:MergeAcross=\"9\" ss:StyleID=\"EUfootnote\"><Data ss:Type=\"String\">Property of the Anaheim, CA chapter of Experience Unlimited. For EU use only.</Data><NamedCell ss:Name=\"Print_Area\"/></Cell>\n" ;
   	$worksheet .= "   </Row>\n</Table>\n" ;
   	$worksheet .= "  <WorksheetOptions xmlns=\"urn:schemas-microsoft-com:office:excel\">\n" ;
   	$worksheet .= "   <PageSetup>\n" ;
   	$worksheet .= "    <Layout x:Orientation=\"Landscape\"/>\n" ;
   	$worksheet .= "    <Header x:Margin=\"0.27\"/>\n" ;
   	$worksheet .= "    <Footer x:Margin=\"0.28\" x:Data=\"Page &amp;P of &amp;N\"/>\n" ;
   	$worksheet .= "    <PageMargins x:Bottom=\"0.49\" x:Left=\"0.25\" x:Right=\"0.25\" x:Top=\"0.54\"/>\n" ;
   	$worksheet .= "   </PageSetup>\n" ;
   	$worksheet .= "   <FitToPage/>\n" ;
   	$worksheet .= "   <Selected/>\n" ;
   	$worksheet .= "   <FreezePanes/>\n" ;
   	$worksheet .= "   <FrozenNoSplit/>\n" ;
   	$worksheet .= "   <SplitHorizontal>3</SplitHorizontal>\n" ;
   	$worksheet .= "   <TopRowBottomPane>3</TopRowBottomPane>\n" ;
   	$worksheet .= "   <ActivePane>2</ActivePane>\n" ;
   	$worksheet .= "   <Panes>\n" ;
   	$worksheet .= "    <Pane>\n" ;
   	$worksheet .= "     <Number>3</Number>\n" ;
   	$worksheet .= "    </Pane>\n" ;
   	$worksheet .= "    <Pane>\n" ;
   	$worksheet .= "     <Number>2</Number>\n" ;
   	$worksheet .= "     <ActiveRow>20</ActiveRow>\n" ;
   	$worksheet .= "     <RangeSelection>R4C1:R4C1</RangeSelection>\n" ;
   	$worksheet .= "    </Pane>\n" ;
   	$worksheet .= "   </Panes>\n" ;
   	$worksheet .= "   <ProtectObjects>False</ProtectObjects>\n" ;
   	$worksheet .= "   <ProtectScenarios>False</ProtectScenarios>\n" ;
   	$worksheet .= "  </WorksheetOptions>\n" ;
   	$worksheet .= " </Worksheet>\n" ;
	return 	$worksheet ;
}


function write_workbook( $user_adminlist = 0 ) {
   	$workbookheader = "<?xml version=\"1.0\"?>\n" ;
   	$workbookheader .= "<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\"\n" ;
   	$workbookheader .= "				xmlns:o=\"urn:schemas-microsoft-com:office:office\"\n" ;
   	$workbookheader .= "				xmlns:x=\"urn:schemas-microsoft-com:office:excel\"\n" ;
   	$workbookheader .= "				xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\"\n" ;
   	$workbookheader .= "				xmlns:html=\"http://www.w3.org/TR/REC-html40\">\n" ;
   	$workbookheader .= " <DocumentProperties xmlns=\"urn:schemas-microsoft-com:office:office\">\n" ;
   	$workbookheader .= "  <Author>William Clardy</Author>\n" ;
   	$workbookheader .= "  <LastAuthor>William Clardy</LastAuthor>\n" ;
   	$workbookheader .= "  <Created>2006-08-13T14:00:00</Created>\n" ;
   	$workbookheader .= "  <LastSaved>" . date ( "Y-m-d" ) . "T" . date ( "H:j:s" ) . "</LastSaved>\n" ;
   	$workbookheader .= "  <Company>Experience Unlimited, Anaheim, CA</Company>\n" ;
   	$workbookheader .= "  <Version>10.6735</Version>\n" ;
   	$workbookheader .= " </DocumentProperties>\n" ;
   	$workbookheader .= " <OfficeDocumentSettings xmlns=\"urn:schemas-microsoft-com:office:office\">\n" ;
   	$workbookheader .= "  <DownloadComponents/>\n" ;
   	$workbookheader .= "  <LocationOfComponents HRef=\"file:///\\\"/>\n" ;
   	$workbookheader .= " </OfficeDocumentSettings>\n" ;
   	$workbookheader .= " <ExcelWorkbook xmlns=\"urn:schemas-microsoft-com:office:excel\">\n" ;
   	$workbookheader .= "  <ProtectContents>True</ProtectContents>\n" ;
   	$workbookheader .= "  <ProtectStructure>False</ProtectStructure>\n" ;
   	$workbookheader .= "  <ProtectWindows>False</ProtectWindows>\n" ;
   	$workbookheader .= " </ExcelWorkbook>\n" ;
   	$workbookheader .= " <Styles>\n" ;
   	$workbookheader .= "  <Style ss:ID=\"Default\" ss:Name=\"Normal\">\n" ;
   	$workbookheader .= "   <Alignment ss:Horizontal=\"Left\" ss:Vertical=\"Top\" ss:WrapText=\"0\"/>\n" ;
   	$workbookheader .= "   <Font ss:FontName=\"Courier New\" ss:Size=\"10\"/>\n" ;
   	$workbookheader .= "   <Interior/>\n" ;
   	$workbookheader .= "   <NumberFormat/>\n" ;
   	$workbookheader .= "   <Protection ss:Protected=\"1\"/>\n" ;
   	$workbookheader .= "  </Style>\n" ;
   	$workbookheader .= "  <Style ss:ID=\"EUProfileReleased\">\n" ;
   	$workbookheader .= "   <Alignment ss:Horizontal=\"Left\" ss:Vertical=\"Center\" ss:WrapText=\"1\"/>\n" ;
   	$workbookheader .= "   <Borders>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Bottom\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Left\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Right\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Top\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "   </Borders>\n" ;
   	$workbookheader .= "   <Font ss:FontName=\"Courier New\" x:Family=\"Modern\"/>\n" ;
   	$workbookheader .= "   <Interior ss:Color=\"#CCFFCC\" ss:Pattern=\"Solid\"/>\n" ;
   	$workbookheader .= "  </Style>\n" ;
   	$workbookheader .= "  <Style ss:ID=\"EUProfilePending\">\n" ;
   	$workbookheader .= "   <Alignment ss:Horizontal=\"Left\" ss:Vertical=\"Center\" ss:WrapText=\"1\"/>\n" ;
   	$workbookheader .= "   <Borders>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Bottom\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Left\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Right\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Top\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "   </Borders>\n" ;
   	$workbookheader .= "   <Font ss:FontName=\"Courier New\" x:Family=\"Modern\"/>\n" ;
   	$workbookheader .= "   <Interior ss:Color=\"#FFFF00\" ss:Pattern=\"Solid\"/>\n" ;
   	$workbookheader .= "  </Style>\n" ;
   	$workbookheader .= "  <Style ss:ID=\"EUProfileRejected\">\n" ;
   	$workbookheader .= "   <Alignment ss:Horizontal=\"Left\" ss:Vertical=\"Center\" ss:WrapText=\"1\"/>\n" ;
   	$workbookheader .= "   <Borders>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Bottom\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Left\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Right\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Top\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "   </Borders>\n" ;
   	$workbookheader .= "   <Font ss:FontName=\"Courier New\" x:Family=\"Modern\"/>\n" ;
   	$workbookheader .= "   <Interior ss:Color=\"#FF6600\" ss:Pattern=\"Solid\"/>\n" ;
   	$workbookheader .= "  </Style>\n" ;
   	$workbookheader .= "  <Style ss:ID=\"EUProfileContent\">\n" ;
   	$workbookheader .= "   <Alignment ss:Horizontal=\"Left\" ss:Vertical=\"Center\" ss:WrapText=\"1\"/>\n" ;
   	$workbookheader .= "   <Borders>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Bottom\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Left\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Right\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Top\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "   </Borders>\n" ;
   	$workbookheader .= "   <Font ss:FontName=\"Courier New\" x:Family=\"Modern\"/>\n" ;
   	$workbookheader .= "  </Style>\n" ;
   	$workbookheader .= "  <Style ss:ID=\"EUDataLeftJust\">\n" ;
   	$workbookheader .= "   <Alignment ss:Horizontal=\"Left\"/>\n" ;
   	$workbookheader .= "   <Borders>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Bottom\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Left\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Right\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Top\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "   </Borders>\n" ;
   	$workbookheader .= "   <Font ss:FontName=\"Courier New\" x:Family=\"Modern\"/>\n" ;
   	$workbookheader .= "  </Style>\n" ;
   	$workbookheader .= "  <Style ss:ID=\"EUDataCenterJust\">\n" ;
   	$workbookheader .= "   <Alignment ss:Horizontal=\"Center\"/>\n" ;
   	$workbookheader .= "   <Borders>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Bottom\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Left\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Right\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Top\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "   </Borders>\n" ;
   	$workbookheader .= "   <Font ss:FontName=\"Courier New\" x:Family=\"Modern\"/>\n" ;
   	$workbookheader .= "  </Style>\n" ;
   	$workbookheader .= "  <Style ss:ID=\"EUDataRightJust\">\n" ;
   	$workbookheader .= "   <Alignment ss:Horizontal=\"Right\"/>\n" ;
   	$workbookheader .= "   <Borders>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Bottom\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Left\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Right\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Top\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "   </Borders>\n" ;
   	$workbookheader .= "   <Font ss:FontName=\"Courier New\" x:Family=\"Modern\"/>\n" ;
   	$workbookheader .= "  </Style>\n" ;
   	$workbookheader .= "  <Style ss:ID=\"HeaderTitle\">\n" ;
   	$workbookheader .= "   <Alignment ss:Horizontal=\"Center\" ss:Vertical=\"Bottom\"/>\n" ;
   	$workbookheader .= "   <Font x:Family=\"Swiss\" ss:Size=\"14\" ss:Color=\"#333399\" ss:Bold=\"1\"/>\n" ;
   	$workbookheader .= "   <Interior ss:Color=\"#FFCC00\" ss:Pattern=\"Solid\"/>\n" ;
   	$workbookheader .= "  </Style>\n" ;
   	$workbookheader .= "  <Style ss:ID=\"HeaderColumnName\">\n" ;
   	$workbookheader .= "   <Alignment ss:Horizontal=\"Center\" ss:Vertical=\"Bottom\" ss:WrapText=\"1\"/>\n" ;
   	$workbookheader .= "   <Font ss:Size=\"11\" ss:Color=\"#333399\" ss:Bold=\"1\"/>\n" ;
   	$workbookheader .= "   <Interior ss:Color=\"#FFCC00\" ss:Pattern=\"Solid\"/>\n" ;
   	$workbookheader .= "  </Style>\n" ;
   	$workbookheader .= "  <Style ss:ID=\"EUfootnote\">\n" ;
   	$workbookheader .= "   <Alignment ss:Horizontal=\"Center\"/>\n" ;
   	$workbookheader .= "   <Borders>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Bottom\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Left\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Right\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "    <Border ss:Position=\"Top\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/>\n" ;
   	$workbookheader .= "   </Borders>\n" ;
   	$workbookheader .= "   <Font ss:FontName=\"Courier New\" ss:Size=\"9\" x:Family=\"Modern\"/>\n" ;
   	$workbookheader .= "   <Interior ss:Color=\"#FFFF00\" ss:Pattern=\"Solid\"/>\n" ;
   	$workbookheader .= "  </Style>\n" ;
   	$workbookheader .= "  <Style ss:ID=\"EUfootnotespacer\">\n" ;
   	$workbookheader .= "   <Alignment ss:Horizontal=\"Center\"/>\n" ;
   	$workbookheader .= "   <Font ss:FontName=\"Courier New\" ss:Size=\"9\" x:Family=\"Modern\"/>\n" ;
   	$workbookheader .= "  </Style>\n" ;
   	$workbookheader .= "  <Style ss:ID=\"EUadminaccess\">\n" ;
   	$workbookheader .= "   <Interior ss:Color=\"#FFFF00\" ss:Pattern=\"Solid\"/>\n" ;
   	$workbookheader .= "  </Style>\n" ;
   	$workbookheader .= "  <Style ss:ID=\"EUgreenbar\">\n" ;
   	$workbookheader .= "   <Interior ss:Color=\"#CCFFCC\" ss:Pattern=\"Solid\"/>\n" ;
   	$workbookheader .= "  </Style>\n" ;
   	$workbookheader .= " </Styles>\n" ;
	return $workbookheader ;
}

mysql_connect( $dbhost, $dbuser, $dbpw ) or die( mysql_error() );
mysql_select_db( $dbname ) or die(mysql_error());

echo write_workbook( $user_admin ) ;

// $select_val = ( $_POST["select_val"] ) ? $_POST["select_val"] : "active" ;
$sql_list = member_select_query( "active" , $tablepre ) ;
$result = mysql_query( $sql_list ) or die( mysql_error() ) ;
if ( mysql_num_rows( $result ) ) {
	echo write_membersheet( $result , "Active Members" , $user_admin ) ;
}

$sql_list = profile_select_query( "active" , $tablepre ) ;
$result = mysql_query( $sql_list ) or die( mysql_error() ) ;
if ( mysql_num_rows( $result ) ) {
	echo write_profilesheet( $result , "active" , "EU Active Member Profiles" , $user_admin ) ;
}

$sql_list = member_select_query( "alumni" , $tablepre ) ;
$result = mysql_query( $sql_list ) or die( mysql_error() ) ;
// if ( mysql_num_rows( $result ) ) {
	echo write_alumnisheet( $result , "EU Alumni" , $user_admin ) ;
// }



echo "</Workbook>\n" ;

}
?>
