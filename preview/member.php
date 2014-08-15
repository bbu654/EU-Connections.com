<?php
require_once( "include/config.inc.php" ) ;

if ( file_exists( "console/header.inc.php"  ) ) {
	include_once("console/header.inc.php") ;
}

// pop up login window if user is not authenticated
if ( !isset( $user_uname ) || $user_uname === "" || $_SESSION[ "uun" ] != $user_uname ) { 
	$action = ( $_GET["function"] ) ? $_GET["function"] : "none" ;
	$action = ( $_POST["function"] ) ? $_POST["function"] : $action ;
	$valid = 0 ;
	if ( $action == "logout" ) {
		session_start();
		header("Location: http://".$_SERVER['HTTP_HOST']
	                      .dirname($_SERVER['PHP_SELF'])
	                      ."index.php" ) ;
		session_destroy();
		exit;
	}
	else{
		$rep = ( $_POST["attempt"] ) ? $_POST["attempt"] + 1 : 1 ;
		$user_uname = ( $_POST["uname"] ) ? $_POST["uname"] : "" ;
		$password = ( $_POST["p"] ) ? $_POST["p"] : "" ;
		if ( $action == "login" ) {
			$valid = authenticate( $user_uname , $password ) ;
		}
		if ( $valid <= 0 ) {
			if ( file_exists( "include/menu.html"  ) ) {
				include_once("include/menu.html") ;
			}
		?>
			<td align="left" valign="top">
			<script language="JavaScript">
			<!--
			function capture_enterkey(field, event) {
				var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
				if (keyCode == 13) {
					var i;
					for (i = 0; i < field.form.elements.length; i++)
						if (field == field.form.elements[i])
							break;
					i = (i + 1) % field.form.elements.length;
					field.form.elements[i].focus();
					return false;
				} 
				else
				return true;
			}
			// -->
			</script>
			<table align="center">
			<form action="<?= $PHP_SELF ?>" method="post" name="login" id="login">
			<input type="hidden" name="function" id="function" value="login">
			<input type="hidden" name="attempt" id="attempt" value="<?= $rep ?>">
					<tr><td colspan="2" align="center"><span class="login_header">Login</span></td><tr>
					<tr>
						<td nowrap valign="top" align="right" nowrap>
						<span class="login_label">Username</span></td>
						<td><input type="text" name="uname" id="uname" value="<?php if( $user_uname != "" && $valid >= 0 ) { echo $user_uname ; } ?>" size="29" maxlength="15" /></td>
					</tr>
					<tr>
						<td nowrap valign="top" align="right" nowrap>
						<span class="login_label">Password</span></td>
						<td><input type="password" name="p" id="p" size="29" maxlength="15" /></td>
					</tr>
					<tr><td colspan="2" align="center"><input type="submit" value="Login"></td></tr>
					<tr><td colspan="2" align="center"><a href="mailto:webmaster@eu-connections.org">If you have problems logging in,<br />click here if to contact the EU webmaster</a> </td></tr>
			</form>
			<!-- </table> -->
	<?php
			if( $user_uname != "" && $valid <= 0 ) { ?>
<script language="JavaScript">
<!--
window.alert("Wrong Username or Password") ;
// -->
</script>
</td>
<?php
			}	
			include("include/footer.html") ;
		}
		else {
			if ( file_exists( "console/menu.inc.php"  ) ) {
				include_once("console/menu.inc.php") ;
				}?><td align="left" valign="top">
			<!-- start include( <?= $page_name ?> ) -->
			<?php 
			if ( file_exists( $page_name ) ) {
				include_once( $page_name ) ;
			}
			else {
				include_once("console/home.inc.php") ;
			}
			?><!-- end include( <?= $page_name ?> ) -->  </td><?php
			include("console/footer.inc.php") ;
		}		
	}
}
else {
	if ( file_exists( "console/menu.inc.php"  ) ) {
		include_once("console/menu.inc.php") ;
	}
	else {
	//	include_once("include/menu.html") ;
	}
	$page_name = str_replace ( "include/", "console/" , $page_name ) ;
	$page_name = str_replace ( ".html", ".inc.php" , $page_name ) ;?><td align="left" valign="top">
	<!-- start include( <?= str_replace ( ".html", ".inc.php" , $page_name ) ; ?> ) -->
	<?php 
	if ( file_exists( $page_name ) ) {
		include_once( $page_name ) ;
	}
	else {
		include_once("console/home.inc.php") ;
	}
	?><!-- end include( <?= str_replace ( ".html", ".inc.php" , $page_name ) ; ?> ) -->  </td><?php
	include("console/footer.inc.php") ;
}
?>
