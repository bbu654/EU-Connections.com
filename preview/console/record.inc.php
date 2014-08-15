
 <?

mysql_connect( $dbhost, $dbuser, $dbpw ) or die( mysql_error() );
mysql_select_db( $dbname ) or die(mysql_error());
$sql_count_cat = "SELECT cat_num, cat_id, cat_descr FROM " . $tablepre . "profile_cat ORDER BY cat_descr ASC" ;
$result = mysql_query( $sql_count_cat ) or die( mysql_error() ) ;
$max_cat_num =   mysql_num_rows( $result ) ;
if ( mysql_num_rows( $result ) ) {
	while ( $row = mysql_fetch_assoc( $result ) ) {
		$text_array = array( $row["cat_num"] , $row["cat_descr"] );
		$cat_array[ $row["cat_id"] ] = $text_array  ;
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
	$droplist = "<select name=\"cat$seq id=\"cat$seq\" size=\"1\">\n" ;
	$i = 1 ;
	foreach( $cat_array as $key => $value ){
		$droplist .= writeoption( $current_val , $key , $value[ 1 ] ) ;
		$i++ ;
		if ( i == intval( $max_cat_num / 2 ) ) {
		$droplist .= writeoption( $current_val ) ;
		}
	}
	$droplist .= "</select>\n" ;
	return  $droplist  ;
}

function add_member( $_POST , $tablepre = "euconnect_" ){
	// function to process the form results into a SQL query and 
	// display the results
	global $max_cat_num  , $cat_array ;
	$keylist  = $_POST["words"] ;
	$searchlist = "" ;
	$key_sql = "" ;

	// check for key words and include them in the query
	if ( $keylist  != "" ){
		$key_string = "%" ;
		$word_array = explode(" ", $keylist ) ;
		if ( $word_array ) {
			$key_sql = "posit_descr LIKE \"%" . implode("%\" OR posit_descr LIKE \"%", $word_array) . "%\" " ;
			$key_sql .= " OR overview LIKE \"%" . implode("%\" OR overview LIKE \"%", $word_array) . "%\" " ;
			$key_sql .= " OR bullet1 LIKE \"%" . implode("%\" OR bullet1 LIKE \"%", $word_array) . "%\" " ;
			$key_sql .= " OR bullet2 LIKE \"%" . implode("%\" OR bullet2 LIKE \"%", $word_array) . "%\" " ;
			$key_sql .= " OR bullet3 LIKE \"%" . implode("%\" OR bullet3 LIKE \"%", $word_array) . "%\" " ;
			$key_sql .= " OR bullet4 LIKE \"%" . implode("%\" OR bullet4 LIKE \"%", $word_array) . "%\" " ;
			$key_sql .= " OR bullet5 LIKE \"%" . implode("%\" OR bullet5 LIKE \"%", $word_array) . "%\" " ;
		}
	}
	$cat_sql = "" ;
	
	// iterate through the check boxes to see which categories were checked
	for ($i = 1 ; $i <= $max_cat_num ; $i++) {
		$catnum = "C" . $i ;
		if ( $_POST[ $catnum  ] ) {
			$cat_sq_array[] = $_POST[ $catnum ] ;
		}
		if ( $cat_sq_array ) {
			$cat_sql = " cat_id IN ( \"" . implode("\", \"", $cat_sq_array ) . "\" ) "  ; 
		}
	}
	// begin assembling the SQL query string
	$search_sql = "SELECT prof_id, euid, cat_id, posit_descr, overview, bullet1, bullet2, bullet3, bullet4, bullet5 FROM euconnect_profiles " ;
	if ( $cat_sql != "" && $key_sql != "" ){
		$search_sql .= "WHERE ( $cat_sql ) AND ( $key_sql ) " ;
	}
	elseif ( $key_sql != "" ){
		$search_sql .= "WHERE ( $key_sql ) " ;
	}
	elseif ( $cat_sql != "" ){
		$search_sql .= "WHERE ( $cat_sql ) " ;
	}
	$search_sql .= "GROUP BY cat_id ORDER BY euid ASC ;" ;
	$result = mysql_query( $search_sql ) or die( mysql_error() ) ;
	$searchlist = "<p>Your search returned " . mysql_num_rows( $result )  . " record(s).</p>\n" ;
	if ( mysql_num_rows( $result ) ) {
		$currentcat = "" ;
		while ( $row = mysql_fetch_assoc( $result ) ) {
			if ( $currentcat != $row["cat_id"] ) {
				$currentcat = $row["cat_id"] ;
				$searchlist .=  "</dl></dd></dl>" ;
				 $cat_descr_array = $cat_array[ $currentcat ] ;
				$searchlist .=  "<dl><dt class=\"profile_cat\">" . $cat_descr_array[ 1 ] . "</dt><dd><br /><dl>\n" ;
			}
			$searchlist .=  writeprofile( $row ) ;
		}
		$searchlist .=  "</dl></dd></dl>\n" ;
	}
	return $searchlist ;
}

function writeform( $record )
{
	global $cat_array ;
?>
<form action="addmember" method="post" name="addmember" id="addmember">
	<input type="hidden" name="euid" id="euid" value="<?= $record[ "euid" ] ?>">
<input type="hidden" name="active" id="active" value="<?= $record[ "active" ] ?>">

<table align="center" class="memberform">
<tr>
	<td>First name</td>
	<td>Last name</td>
</tr>
<tr>
	<td><input type="text" name="firstname" id="firstname" value="<?= $record[ "firstname" ] ?>" size="20" maxlength="25"></td>
	<td><input type="text" name="lastname" id="lastname"  value="<?= $record[ "lastname" ] ?>" size="20" maxlength="25"></td>
</tr>
<tr>
	<td>Username</td>
	<td>email</td>
</tr>
<tr>
	<td><input type="text" name="username" id="username"  value="<?= $record[ "username" ] ?>" size="25" maxlength="25" readonly></td>
	<td><input type="text" name="email_home" id="email_home"  value="<?= $record[ "email_home" ] ?>" size="40" maxlength="45"><br /></td>
</tr>
<tr>
	<td>Voice</td>
	<td>Cell</td>
</tr>
<tr>
	<td><input type="text" name="voice_primary" id="voice_primary" value="<?= $record[ "voice_primary" ] ?>" size="14" maxlength="14">
	<br />ext. <input type="text" name="voice_ext" id="voice_ext"  value="<?= $record[ "voice_ext" ] ?>" size="6" maxlength="6"></td>
	<td><input type="text" name="voice_mobile" id="voice_mobile" value="<?= $record[ "voice_mobile" ] ?>" size="14" maxlength="14"></td>
</tr>
<tr>
	<td>FAX</td>
	<td>User Level</td>
</tr>
<tr>
	<td><input type="text" name="fascimile" id="fascimile" value="<?= $record[ "fascimile" ] ?>" size="14" maxlength="14"><br /></td>
	<td><select name="userlevel" id="userlevel" size="1">
	<option value="0">0</option>
	<option value="1"<? if ( $record[ "userlevel"] == 1 ) { ?>selected<? } ?>>member</option>
	<option value="2"<? if ( $record[ "userlevel"] == 2 ) { ?>selected<? } ?>>administrator</option>
	</select></td>
</tr>
<tr>
	<td>Primary Category</td>
	<td><?php 	echo write_catlist( $cat_array, 1 , $record[ "category_id_primary" ]  ) . "<br />\n"  ; ?></td>
</tr>
<tr>
	<td>Second Category</td>
	<td><?php 	echo write_catlist( $cat_array, 2 , $record[ "category_id_secondary" ]  ) . "<br />\n"  ; ?></td>
</tr>
<tr>
	<td>Third Category</td>
	<td><?php 	echo write_catlist( $cat_array, 3 , $record[ "category_id_tertiary" ]  ) . "<br />\n"  ; ?></td>
</tr>
<tr>
	<td>Password</td>
	<td>Confirm Password</td>
</tr>
<tr>
	<td><input type="text" name="pass" id="pass"  value="<?= "password" ?>" size="10" maxlength="10"></td>
	<td></td>
</tr>
</table>

<br />
<?

$sw = new SPAW_Wysiwyg('overview' /*name*/,stripslashes($HTTP_POST_VARS['overview']) /*value*/,
                       'en' /*language*/, 'mini' /*toolbar mode*/, '' /*theme*/,
                       '250px' /*width*/, '50px' /*height*/);
// $sw->show();


?>

</form>

<?
}

if ( $_POST["euid"]  ) {
	// query database for member info
	$sql_memberfetch = "SELECT * FROM " . $tablepre . "members WHERE euid = " . $_POST["euid"] ;
	echo $sql_memberfetch . "<br />" ;
	$result = mysql_query( $sql_memberfetch ) or die( mysql_error() ) ;
	echo "returned " . mysql_num_rows( $result ) . " record(s)<br />" ;
	if ( mysql_num_rows( $result ) ) {
		$member_row = mysql_fetch_assoc( $result ) ;
		}
}
elseif ( $_GET["euid"] && $user_euid == $_GET["euid"] ) {
	// query database for member info
	$sql_memberfetch = "SELECT * FROM " . $tablepre . "members WHERE euid = " . $_GET["euid"] ;
	echo $sql_memberfetch . "<br />" ;
	$result = mysql_query( $sql_memberfetch ) or die( mysql_error() ) ;
	echo "returned " . mysql_num_rows( $result ) . " record(s)<br />" ;
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
		"voice_primary" => "555-555-5555",
		"voice_ext" => "",
		"voice_mobile" => "555-555-5555",
		"fascimile" => "555-555-5555" ) ;
}

writeform( $member_row ) ;


?>
<!-- profile_edit.inc.php -->
