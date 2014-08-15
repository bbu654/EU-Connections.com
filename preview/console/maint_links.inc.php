<?php

function writelink( $row )
{
	$linkid = $row["linkid"] ;
	$site_descrip = $row["site_descrip"] ;
	$site_url = $row["site_url"] ;
	$site_comment = $row["site_comment"] ;

/* submit buttons don't display nicely
	$linkstr = "<tr><td><form action=\"member.php\" method=\"post\" name=\"editlink\" id=\"editlink\">\n" ;
	$linkstr .= "<input type=\"hidden\" name=\"page\" id=\"page\" value=\"add_link\">\n<input type=\"hidden\" name=\"linkid\" id=\"linkid\" value=\"$linkid\">\n";
	$linkstr .= "<input type=\"submit\" value=\"Edit\"></form></td><td>&nbsp;</td>\n" ;
*/
	// using links instead
	$linkstr = "<tr><td><font size=\"-1\"><a href=\"member.php?page=add_link&linkid=" .$linkid . "\">Edit</a></font></td><td>&nbsp;</td>\n" ;
	$linkstr .= "<td><b>" . $site_descrip . "</b></td></tr>\n" ;

	//$linkstr .= "<tr><td><input type=\"submit\" value=\"Delete\"></td><td>&nbsp;</td>\n" ;

	$linkstr .= "<tr><td><font size=\"-1\"><a href=\"member.php?page=delete_link&linkid=" .$linkid . "\">Delete</a></font></td><td>&nbsp;</td>\n" ;
	$linkstr .= "<td><a href=\"" . $site_url . "\" target=\"_blank\">" . $site_url . "</a></td></tr>\n" ;

	if ($site_comment != "") {
		$linkstr .= "<tr><td colspan=\"2\"></td><td>" . $site_comment . "</td></tr>\n" ;
	}

	$linkstr .= "<tr><td><br></td></tr>\n" ;

	return $linkstr ;
}

function write_list( &$result ) {
	$liststr = "<table>\n" ;
	$linklist = "";

	$currentcat = "" ;
	while ( $row = mysql_fetch_assoc( $result ) ) {
		if ( $currentcat != $row["site_category"] ) {
			$currentcat = $row["site_category"] ;

			if ($linklist != "") {
				$linklist .= "<tr><td colspan=\"3\"><font size=\"-1\"><a href=\"#top\">Return to top</a></font></td></tr>\n" ;
			}

			$linklist .= "<tr><td colspan=\"3\"><hr></td></tr>" ;
			$linklist .= "<tr><td colspan=\"3\"><br><b><a name=\"" . $currentcat  . "\">" . $currentcat . "</td></tr>\n" ;
		}

		$linklist .= writelink( $row ) ;
	}
	$linklist .= "<tr><td colspan=\"3\"><font size=\"-1\"><a href=\"#top\">Return to top</a></font></td></tr>\n" ;

	$liststr .= $linklist ;
	$liststr .= "</table>\n" ;
	return $liststr ;
}

function write_index( &$result )
{
	$listindex = "<h3 align=\"center\">Edit Useful Links</h3>\n" ;
	$listindex .= "<p>Links are listed by category</p>\n" ;
	$listindex .= "<ul>\n";

	while ( $row = mysql_fetch_assoc( $result ) ) {
		$listindex .=  "<li class=\"linkindex\"><a href=\"#" ;
		$listindex .=  $row["site_category"] ;
		$listindex .=  "\">" ;
		$listindex .=  $row["site_category"] ;
		$listindex .=  "</a></li>\n" ;
	}
	$listindex .=  "</ul>\n" ;
	return $listindex  ;
}

mysql_connect( $dbhost, $dbuser, $dbpw ) or die( mysql_error() );
mysql_select_db( $dbname ) or die(mysql_error());

$sql_index = "SELECT DISTINCT site_category AS site_category FROM " . $tablepre . "links ORDER BY site_category ASC" ;
$result = mysql_query( $sql_index ) or die( mysql_error() ) ;
if ( mysql_num_rows( $result ) ) {
	echo write_index( $result ) ;
}
else {
	echo "<h2>Error creating Link Category list!</h2>" ;
}

$sql_list = "SELECT linkid, TRIM( site_category ) AS site_category, TRIM( site_descrip ) AS site_descrip, TRIM( site_url ) AS site_url, TRIM( site_comment ) AS site_comment FROM " . $tablepre . "links ORDER BY site_category ASC, site_descrip ASC" ;
$result = mysql_query( $sql_list ) or die( mysql_error() ) ;
if ( mysql_num_rows( $result ) ) {
	echo write_list( $result ) ;
}
else {
	echo "<h2>Error creating list of links!</h2>" ;
}

?>
<!-- maint_links.inc.php -->
