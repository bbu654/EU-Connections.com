<?php

mysql_connect( $dbhost, $dbuser, $dbpw ) or die( mysql_error() );
mysql_select_db( $dbname ) or die(mysql_error());

echo "<h3 align=\"center\">Delete Useful Link</h3>" ;

$linkid = ( $_POST["linkid"]  ) ? $_POST["linkid"] : $_GET["linkid"] ;

$sql_delete = "DELETE FROM " . $tablepre . "links WHERE linkid=" . $linkid ;
$result = mysql_query( $sql_delete ) or die( mysql_error() ) ;
if ( $result ) {
?>
	<p><b>Link deleted!<b></p>
	<p>To edit more links, click <a href="member.php?page=maint_links">here</a>.</p>
<?php
}
else {
	echo "<p><b>Error deleting link!</b></p>" ;
}

?>
<!-- delete_link.inc.php -->
