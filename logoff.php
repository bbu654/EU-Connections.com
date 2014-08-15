<?
session_start();
header("Location: http://".$_SERVER['HTTP_HOST']
                          .dirname($_SERVER['PHP_SELF'])
                          ."/index.php" ) ;
session_destroy();
	exit;
?>
