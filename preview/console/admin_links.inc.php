<?

mysql_connect( $dbhost, $dbuser, $dbpw ) or die( mysql_error() );
mysql_select_db( $dbname ) or die(mysql_error());

// create droplist of distinct categories
$sql_count_cat = "SSELECT DISTINCT site_category AS site_category FROM " . $tablepre . "links ORDER BY site_category ASC" ;
$result = mysql_query( $sql_count_cat ) or die( mysql_error() ) ;
if ( mysql_num_rows( $result ) ) {
	while ( $row = mysql_fetch_assoc( $result ) ) {
		$cat_array[ $row["site_category"] ] = $row["site_category"]   ;
	}
}

function writeoption( $current_val, $opt_val = "  " , $opt_descr = "  " ) {
	// function to write out each categry's checkbox
	if ( $opt_val == $current_val ) {
		return "<option value=\"$opt_val\" selected>$opt_descr</option>\n" ;
	}
	else {
		return "<option value=\"$opt_val\">$opt_descr</option>\n" ;
	}
}

function write_catlist( $cat_array , $current_val = "  " , $formname = "" ){
	global $max_cat_num ;
	// function to write out the checkbox list of categories
	if ( $formname != "" ) {
		// make auto-submitting on change if form name is given
		$droplist = "<select name=\"cat_id\" id=\"cat_id\" size=\"1\">\n" ;
	}
	else {
		$droplist = "<select name=\"cat_id\" id=\"cat_id\" size=\"1\">\n" ;
	}
	$i = 1 ;
	$droplist .= writeoption( $current_val , "  " , "New Category" ) ;
	foreach( $cat_array as $key => $value ){
		$droplist .= writeoption( $current_val , $key , $value ) ;
	}
	$droplist .= "</select>\n" ;
	return  $droplist  ;
}

function writeindex( $tablepre   )
{
	$listindex = "<ul>\n";
	$sqlindex = "SELECT DISTINCT site_category AS site_category FROM " . $tablepre . "links ORDER BY site_category ASC" ;
	$result = mysql_query( $sqlindex ) or die( mysql_error() ) ;

	if ( mysql_num_rows( $result ) ) {
		while ( $row = mysql_fetch_assoc( $result ) ) {
			$listindex .=  "<li class=\"linkindex\"><a href=\"#" ;
			$listindex .=  $row["site_category"] . "\">" ;
			$listindex .=  $row["site_category"] . "</a></li>\n" ;
		}
		$listindex .=  "</ul>\n" ;
	}
	return  $listindex  ;
}

function writelist( $tablepre   )
{
	$linklist = "";
	$sqlmain = "SELECT site_category, TRIM( site_descrip ) AS site_descrip, site_url, TRIM( site_comment ) AS site_comment FROM " . $tablepre . "links ORDER BY site_category ASC, site_descrip ASC" ;
	$result = mysql_query( $sqlmain ) or die( mysql_error() ) ;

	if ( mysql_num_rows( $result ) ) {
		$currentcat = "" ;
		while ( $row = mysql_fetch_assoc( $result ) ) {
			if ( $currentcat != $row["site_category"] ) {
				$currentcat = $row["site_category"] ;
				$linklist .=  "</dl></dd></dl>" ;
				$linklist .=  "<dl><dt class=\"linkcat\"><a name=\"" . $currentcat  . "\">" . $currentcat . "</dt><dd><dl>\n" ;
			}
			$linklist .=  writelink( $row ) ;
		}
		$linklist .=  "</dl></dd></dl>\n" ;
	}
	return  $linklist  ;
}

function writelink( $row )
{
	$linkstr = "" ;
	$site_descr = $row["site_descrip"] ;
	$site_url = $row["site_url"] ;
	$site_com = $row["site_comment"] ;
	$linkstr .= "<dt class=\"linkname\">" . $site_descr . "</dt>\n" ;
	$linkstr .= "<dd class=\"linkurl\"><a href=\"" . $site_url . "\">" . $site_url . "</a></dd>\n" ;
	if ( $site_com  != "" ){
		$linkstr .= "<dd class=\"linkcom\">" . $site_com . "</dd>\n" ;
	}
	return $linkstr ;

}
?>
