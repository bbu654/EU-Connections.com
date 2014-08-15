<?php
include("include/config.inc.php") ;
echo $doctype ;
include("include/header.html") ;
include("include/menu.html") ;
if ( file_exists( str_replace ( ".html", ".inc.php" , $page_name ) ) ) {
	include( str_replace ( ".html", ".inc.php" , $page_name ) ) ;
}
include("include/content_header.html") ;
include( $page_name ) ;
include("include/footer.html") ;
?>


