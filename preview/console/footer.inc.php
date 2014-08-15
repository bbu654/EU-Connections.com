</tr>
<?php
// Standard Page Footer (slightly different on Home Page)
?><tr>
    <td align="center" valign="center"> <font size="-1"> 
         <font color="#CC9933">
         <b>Script Last Updated: <?php
	 // generates the last-modified date for the content file 
	if ( file_exists( $page_name ) ) {
		 echo date ("F j, Y ", filemtime( $page_name ) ) ; 
	}
	elseif( file_exists( str_replace ( ".html", ".inc.php" , $page_name ) ) )  {
		 echo date ("F j, Y ", filemtime( str_replace ( ".html", ".inc.php" , $page_name ) ) ) ; 
	}
	else {
		echo date ("F j, Y ", filemtime( $current_script ) ) ; 
	} ?>
         </b></font>
         <br><br>
      <a href="member.php?page=home">Console Home</a> |
      <a href="member.php?page=info">View My Info</a>
<?php
if( $user_euid && $user_euid != "000000" ){ 
?> | <a href="logoff.php">Logoff</a>
<?php 
}
?>
<!--
<?php
if( $user_euid && $user_euid != "000000" && $admin >= 0 ){ 
?>      <a href="logoff.php">Logoff</a>
      <a href="admin.php?page=email">Email Members</a> |
      <a href="admin.php?page=testlinks">Check Links</a> 
<?php 
}
else {
?>      <a href="member.php">Members Access</a> |
<?php 
} ?>
-->
      </font>
      <hr>
      <font size="-1.5">
      <cite>
         &#169; <? echo date("Y" ) ; ?> Experience Unlimited &#149; Anaheim, California<br>
         Phone: (714) 518-2365 &#149; FAX: (714) 518-2387<br>
         email: <a href="mailto:info@eu-connections.org">info@eu-connections.org</a>
      </cite>
      </font>
   </td>
</tr>
<?php 
if ( $test_env ) {
	$post_result = "<table align=\"center\" bgcolor=\"#FFFF00\" cellspacing=\"2\" cellpadding=\"2\" border=\"0\">\n" ;
	foreach( $_POST as $FormFieldName => $FormFieldValue ) {
		$post_result .= "<tr>\n<td align=\"right\">\$_POST[ " . $FormFieldName . " ]</td>\n<td>" . $FormFieldValue . "</td>\n</tr>\n" ;
	}
	$post_result .= "</table>\n" ;
	echo $post_result ;
	
	$get_result = "<table align=\"center\" bgcolor=\"#FFFF00\" cellspacing=\"2\" cellpadding=\"2\" border=\"0\">\n" ;
	foreach( $_GET as $ParamName => $ParamValue ) {
		$get_result .= "<tr>\n<td align=\"right\">\$_GET[ " . $ParamName . " ]</td>\n<td>" . $ParamValue . "</td>\n</tr>\n" ;
	}
	$get_result .= "</table>\n" ;
	echo $get_result ;
	
	$session_result = "<table align=\"center\" bgcolor=\"#FFFF00\" cellspacing=\"2\" cellpadding=\"2\" border=\"0\">\n" ;
	foreach( $_SESSION as $ParamName => $ParamValue ) {
		$session_result .= "<tr>\n<td align=\"right\">\$_SESSION[ " . $ParamName . " ]</td>\n<td>" . $ParamValue . "</td>\n</tr>\n" ;
	}
	$session_result .= "</table>\n" ;
	echo $session_result ;
	
	
} ?>
<!-- End Main Table = Bottom of Page ------------------------------->
</table>
<!------------------------------------------------------------------>

</body>
</html>