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

function writeoption( $current_val, $opt_val = "  " , $opt_descr = "  " ) {
	// function to write out each categry's checkbox
	if ( $opt_val == $current_val ) {
		return "<option value=\"$opt_val\" selected>$opt_descr</option>\n" ;
	}
	else {
		return "<option value=\"$opt_val\">$opt_descr</option>\n" ;
	}
}

function write_catlist( $cat_array, $seq , $current_val = "  " ){
	global $max_cat_num ;
	// function to write out the checkbox list of categories
	$droplist = "<select name=\"cat$seq\" id=\"cat$seq\" size=\"1\">\n" ;
	$i = 1 ;
	$droplist .= writeoption( $current_val , "  " , "none selected" ) ;
	foreach( $cat_array as $key => $value ){
		$droplist .= writeoption( $current_val , $key , $value ) ;
		$i++ ;
		if ( i == intval( $max_cat_num / 2 ) ) {
		$droplist .= writeoption( $current_val ) ;
		}
	}
	$droplist .= "</select>\n" ;
	return  $droplist  ;
}

function add_member( $_POST , $tablepre = "euconnect_" ){
	// function to process the form results into a SQL query
	// to insert the member data
	$insert_sql = "INSERT INTO " . $tablepre . "members SET " ;
	$insert_sql .= "( firstname , lastname , email_home , voice_primary , " ;
	$insert_sql .= "voice_ext , voice_mobile , fascimile " ;
	$insert_sql .= ", category_id_primary , category_id_secondary , category_id_tertiary " ;
	$insert_sql .= ") VALUES " ;
	$insert_sql .= "( firstname = \"" . $_POST["firstname"] . "\", " ;
	$insert_sql .= " \"" . $_POST["lastname"] . "\" " ;
	$insert_sql .= "\", " . $_POST["email_home"] . "\" " ;
	$insert_sql .= "\", " . $_POST["voice_primary"] . "\" " ;
	$insert_sql .= "\", " . $_POST["voice_ext"] . "\" " ;
	$insert_sql .= "\", " . $_POST["voice_mobile"] . "\" " ;
	$insert_sql .= "\", " . $_POST["fascimile"] . "\" " ;
	$result = mysql_query( $insert_sql ) or die( mysql_error() ) ;
	return $result ;
}

function update_member( $_POST , $tablepre = "euconnect_" ){
	// function to process the form results into a SQL query
	// to update the member data
	$update_sql = "UPDATE " . $tablepre . "members SET " ;
	$update_sql .= "firstname = \"" . $_POST["firstname"] . "\" " ;
	$update_sql .= ", lastname = \"" . $_POST["lastname"] . "\" " ;
	$update_sql .= ", email_home = \"" . $_POST["email_home"] . "\" " ;
	$update_sql .= ", voice_primary = \"" . $_POST["voice_primary"] . "\" " ;
	$update_sql .= ", voice_ext = \"" . $_POST["voice_ext"] . "\" " ;
	$update_sql .= ", voice_mobile = \"" . $_POST["voice_mobile"] . "\" " ;
	$update_sql .= ", fascimile = \"" . $_POST["fascimile"] . "\" " ;
	$update_sql .= " WHERE euid = \"" . $_POST["euid"] . "\" " ;
	$result = mysql_query( $update_sql ) or die( mysql_error() ) ;
	return $result ;
}

function writeform( $record , $newmember = 0 )
{
	global $cat_array , $page_name;
	if ( $newmember == 1 ) {
		$caption = "Add Record" ;
	}
	else {
		$caption = "Update Record" ;
	}
?>
<form action="member.php" method="post" name="memberinfo" id="memberinfo">
	<input type="hidden" name="euid" id="euid" value="<?= $record[ "euid" ] ?>">
	<input type="hidden" name="active" id="active" value="<?= $record[ "active" ] ?>">
	<input type="hidden" name="sql" id="sql" value="<?= $caption ?>">
	<input type="hidden" name="page" id="page" value="<?= str_replace ( "console/", "" , str_replace ( ".inc.php" , "", $page_name ) ) ?>">
<table width="100%" cellspacing="5" align="center" class="memberform">
<tr>
	<td align="right" valign="middle" class="fieldname">EU Number</td>
	<td valign="middle"><span class="formtype"><?= $record[ "euid" ] ?></span></td>
</tr>
<tr>
	<td align="right" valign="middle" class="fieldname">Username</td>
	<td valign="middle"><span class="formtype"><?= $record[ "username" ] ?></span></td>
</tr>
<tr>
	<td align="right" valign="middle" class="fieldname">First name</td>
	<td valign="middle"><input type="text" class="formtype" name="firstname" id="firstname" value="<?= $record[ "firstname" ] ?>" size="20" maxlength="25"></td>
</tr>
<tr>
	<td align="right" valign="middle" class="fieldname">Last name</td>
	<td valign="middle"><input type="text" class="formtype" name="lastname" id="lastname"  value="<?= $record[ "lastname" ] ?>" size="20" maxlength="25"></td>
</tr>
<tr>
	<td align="right" valign="middle" class="fieldname">email</td>
	<td valign="middle"><input type="text" class="formtype" name="email_home" id="email_home"  value="<?= $record[ "email_home" ] ?>" size="40" maxlength="45"><br /></td>
</tr>
<tr>
	<td align="right" valign="middle" class="fieldname">Voice</td>
	<td valign="middle"><input type="text" class="formtype" name="voice_primary" id="voice_primary" value="<?= $record[ "voice_primary" ] ?>" size="14" maxlength="14">
	ext. <input type="text" class="formtype" name="voice_ext" id="voice_ext"  value="<?= $record[ "voice_ext" ] ?>" size="6" maxlength="6"></td>
</tr>
<tr>
	<td align="right" valign="middle" class="fieldname">Cell</td>
	<td valign="middle"><input type="text" class="formtype" name="voice_mobile" id="voice_mobile" value="<?= $record[ "voice_mobile" ] ?>" size="14" maxlength="14"></td>
</tr>
<tr>
	<td align="right" valign="middle" class="fieldname">FAX</td>
	<td valign="middle"><input type="text" class="formtype" name="fascimile" id="fascimile" value="<?= $record[ "fascimile" ] ?>" size="14" maxlength="14"><br /></td>
</tr>
<tr>
	<td align="right" valign="middle" class="fieldname">User Level</td>
	<td valign="middle"><span class="formtype"><?php
	switch( $record[ "userlevel" ] ) {
		case 1 :
			echo "member" ;
			break ;
		case 2 :
			echo "administrator" ;
			break ;
		default :
			break ;
	}
	?></span></td>
	<!-- <td valign="middle"><select name="userlevel" id="userlevel" size="1">
	<option value="0">0</option>
	<option value="1"<? if ( $record[ "userlevel"] == 1 ) { ?>selected<? } ?>>member</option>
	<option value="2"<? if ( $record[ "userlevel"] == 2 ) { ?>selected<? } ?>>administrator</option>
	</select></td> -->
</tr>
<tr>
	<td colspan="2" align="center" valign="middle"><input type="submit" name="submit" id="submit" value="<?= $caption ?>"></td>
</tr>
</table>
</form>
<?
}
$new = 0 ;
if ( $_POST["euid"]  ) {
	if ( $_POST["sql"] == "Update Record" ) {
		update_member( $_POST ) ;
	}
	elseif ( $_POST["sql"] == "Add Record" ) {
		add_member( $_POST ) ;
	}
	// query database for member info
	$sql_memberfetch = "SELECT * FROM " . $tablepre . "members WHERE euid = " . $_POST["euid"] ;
	$result = mysql_query( $sql_memberfetch ) or die( mysql_error() ) ;
	if ( mysql_num_rows( $result ) ) {
		$member_row = mysql_fetch_assoc( $result ) ;
		}
}
elseif ( $_GET["euid"] && $user_euid == $_GET["euid"] ) {
	// query database for member info
	$sql_memberfetch = "SELECT * FROM " . $tablepre . "members WHERE euid = " . $_GET["euid"] ;
	// echo $sql_memberfetch . "<br />" ;
	$result = mysql_query( $sql_memberfetch ) or die( mysql_error() ) ;
	// echo "returned " . mysql_num_rows( $result ) . " record(s)<br />" ;
	if ( mysql_num_rows( $result ) ) {
		$member_row = mysql_fetch_assoc( $result ) ;
		}
}
else {
	// create default array
	$member_row = array(
		"euid" => 0,
		"active" => 1,
		"enrolled" => date("Ymd", time( ) ) ,
		"userlevel" => 0,
		"category_id_primary" => "  ",
		"category_id_secondary" => "  ",
		"category_id_tertiary" => "  ",
		"username" => "",
		"pass" => "",
		"lastname" => "",
		"firstname" => "",
		"email_home" => "",
		"voice_primary" => "",
		"voice_ext" => "",
		"voice_mobile" => "",
		"fascimile" => "" ) ;
		$new = 1 ;
}

writeform( $member_row , $new ) ;


?>

