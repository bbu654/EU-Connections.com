<style type="text/css">
td.fieldname{
	font-weight : bold;
}
input.formtype {
	font-family : "Courier New", Courier, monospace;
	font-weight : bold;
}
span.formtype {
	font-family : "Courier New", Courier, monospace;
}</style>
 <?

mysql_connect( $dbhost, $dbuser, $dbpw ) or die( mysql_error() );
mysql_select_db( $dbname ) or die(mysql_error());
$sql_count_cat = "SELECT cat_num, cat_id, cat_descr FROM " . $tablepre . "profile_cat ORDER BY cat_descr ASC" ;
$result = mysql_query( $sql_count_cat ) or die( mysql_error() ) ;
$max_cat_num =   mysql_num_rows( $result ) ;
if ( mysql_num_rows( $result ) ) {
	while ( $row = mysql_fetch_assoc( $result ) ) {
		$cat_array[ $row["cat_id"] ] =  $row["cat_descr"] ;
	}
}

$row = mysql_fetch_assoc( $result ) ;

function update_pass( $_POST , $tablepre = "euconnect_" ){
	// function to process the form results into a SQL query
	// to update the member data
	$sql_checkpass = "SELECT euid, userlevel from " . $tablepre . "members WHERE euid = \"". $_POST["euid"] . "\" AND pass = SHA1( \"". $_POST["p"] . "\" )";
	// $sql_checkpass = $sql_checkuname ;	// dummy check for username only (remove after debugging)
	$result = mysql_query( $sql_checkpass ) or die( mysql_error() ) ;
	// if row exists -> user/pass combination is correct
	if ( mysql_num_rows( $result ) == 1 ) {
		$update_sql = "UPDATE " . $tablepre . "members SET " ;
		$update_sql .= "pass = SHA1( \"" . $_POST["p1"] . "\" ) " ;
		$update_sql .= " WHERE euid = \"" . $_POST["euid"] . "\" " ;
		$result = mysql_query( $update_sql ) or die( mysql_error() ) ;
		return "Password updated successfully" ;
	}
	else {
		return "Incorrect current password." ;
	}
}

function writeform( $record , $success = "" )
{
	global $cat_array , $page_name;
	$caption = "Change password" ;
?>
<form action="member.php" method="post" name="memberinfo" id="memberinfo">
	<input type="hidden" name="euid" id="euid" value="<?= $record[ "euid" ] ?>">
	<input type="hidden" name="page" id="page" value="<?= str_replace ( ".html", "" , str_replace ( ".inc.php" , "", $page_name ) ) ?>">
	<input type="hidden" name="sql" id="sql" value="<?= $caption ?>">
	<input type="hidden" name="page" id="page" value="<?= str_replace ( "console/", "" , str_replace ( ".inc.php" , "", $page_name ) ) ?>">
<table width="100%" cellspacing="5" align="center" class="memberform">
<? if ( $success != "" ) { ?><tr>
	<td colspan="2" align="center" valign="middle"><?= $success ?></td>
</tr>
<? } ?><tr>
	<td align="right" valign="middle" class="fieldname">Type old password</td>
	<td valign="middle"><input type="password" name="p" id="p" size="25" maxlength="25" class="formtype" onFocus="this.value = '' ;"></td>
</tr>
<tr>
	<td align="right" valign="middle" class="fieldname">Type new password</td>
	<td valign="middle"><input type="password" name="p1" id="p1" size="10" maxlength="10" class="formtype" onFocus="this.value = '' ;"></td>
</tr>
<tr>
	<td align="right" valign="middle" class="fieldname">Confirm password</td>
	<td valign="middle"><input type="password" name="p2" id="p2" size="10" maxlength="10" class="formtype" onFocus="this.value = '' ;"></td>
</tr>
<tr>
	<td colspan="2" align="center" valign="middle"><input type="submit" name="submit" id="submit" value="<?= $caption ?>"></td>
</tr>
</table>
</form>
<?
}
$response = "" ;
if ( $_POST["euid"]  ) {
	if ( $_POST["p1"] == $_POST["p2"] ) {
		$response = update_pass( $_POST ) ;
	}
	else {
		$response = "New passwords did not match" ;
	}
	// query database for member info
	$sql_memberfetch = "SELECT * FROM " . $tablepre . "members WHERE euid = " . $_POST["euid"] ;
	$result = mysql_query( $sql_memberfetch ) or die( mysql_error() ) ;
	if ( mysql_num_rows( $result ) ) {
		$member_row = mysql_fetch_assoc( $result ) ;
		}
}
//elseif ( $_GET["euid"] && $user_euid == $_GET["euid"] ) {
else {	// query database for member info
	$sql_memberfetch = "SELECT * FROM " . $tablepre . "members WHERE euid = \"" . $user_euid . "\"" ;
	// echo $sql_memberfetch . "<br />" ;
	$result = mysql_query( $sql_memberfetch ) or die( mysql_error() ) ;
	// echo "returned " . mysql_num_rows( $result ) . " record(s)<br />" ;
	if ( mysql_num_rows( $result ) ) {
		$member_row = mysql_fetch_assoc( $result ) ;
		}
}

writeform( $member_row , $response ) ;


?>

