<?

mysql_connect( $dbhost, $dbuser, $dbpw ) or die( mysql_error() );
mysql_select_db( $dbname ) or die(mysql_error());

function writeindex( $tablepre   )
{
	$listindex = "<ul>\n";
	$sqlindex = "SELECT DISTINCT site_category AS site_category FROM " . $tablepre . "links ORDER BY site_category ASC" ;
	$result = mysql_query( $sqlindex ) or die( mysql_error() ) ;

	if ( mysql_num_rows( $result ) ) {
		while ( $row = mysql_fetch_assoc( $result ) ) {
			$listindex .=  "<li class=\"linkindex\"><a href=\"#" ;
			$listindex .=  $row["site_category"] ;
			$listindex .=  "\">" ;
			$listindex .=  $row["site_category"] ;
			$listindex .=  "</a></li>\n" ;
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
				if ($linklist != "") {
					$linklist .=  "</dl></dd></dl>" ;
					$linklist .=  "<font size=\"-1\"><a href=\"#top\">Return to top</a></font>\n<br>" ;
				}
				$linklist .=  "<hr><br>\n" ;
				$linklist .=  "<dl><dt class=\"linkcat\"><a name=\"" . $currentcat  . "\">" . $currentcat . "</dt><dd><dl>\n" ;
			}
			$linklist .=  writelink( $row ) ;
		}
		$linklist .=  "</dl></dd></dl>\n" ;
	}

	$linklist .=  "<font size=\"-1\"><a href=\"#top\">Return to top</a></font>\n" ;

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