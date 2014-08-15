<html>
<head>
<title>Anaheim EDD Experience Unlimited Networking Group  -- <?php $page_val ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">@import url(include/anaheimeu.css);</style>
<script language="JavaScript" src="include/eu_anaheim.js" type="text/javascript"></script>
<?php
$jscript_name = str_replace ( "include/", "console/" , $page_name ) ;
$jscript_name = str_replace ( ".html", ".js" , $page_name ) ;
$jscript_name = str_replace ( ".php", ".js" , $page_name ) ;
if ( file_exists( $jscript_name ) ) {
	echo "<script language=\"JavaScript\" src=\"" . $jscript_name  . "\" type=\"text/javascript\"></script>\n" ;
}
?>
</head>
<!-- current directory = "<?= $current_path ?>" -->
<!-- current script = "<?= $current_script ?>" -->
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload="window.focus();<? if ( !isset( $user_uname ) ) { echo "document.login.uname.focus();" ; }?>">
<a name="top"></a>
<table width="100%" cellpadding="8" cellspacing="0" background="images/awtsocal.jpg">

<!--------------------------------------------------------------------->
<!-- Main Table, Row #1 = Standard Page Header                    ----->
<!--    One TD cell, spans 3 columns for full width of the page.  ----->
<!--------------------------------------------------------------------->
 
<tr>
  <td colspan=3 valign="top" bgcolor="#333399">

     <!--------------------------------------------------------------------->
     <!-- Nested Table, one row, two TD cells.                         ----->
     <!--------------------------------------------------------------------->
<table width="100%" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="200" align="left" valign="top"><a href="index.php"><img src="images/eulogoa.gif" width="210" height="120" alt=""></a></td>
    <td colspan="2" align="center"><a href="member.php"><font color="white"><h1>Member Console</h1></font></a></td>
  </tr>
</table>

  </td>
</tr>   	 
