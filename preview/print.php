<?php
require_once( "include/config.inc.php" ) ;
?><html>
<head>
	<title><?= $s_agentname ?></title>
</head>
<body>
<?php 
if ( file_exists( str_replace ( ".html", ".inc.php" , $page_name ) ) ) {
	include( str_replace ( ".html", ".inc.php" , $page_name ) ) ;
}
include( $page_name ) ;
?></body>
</html>
