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
<script language="JavaScript">

function validlength( formField , fieldLabel , fieldlength )
{
	var result = true;
	while (formField.value.length > fieldlength )
	{
		var tempref=formField.value ;
		var trimlength=formField.value.lastIndexOf(' ') ;
		formField.value=tempref.substr( 0, trimlength ) 
		formField.focus();
		result = false;
	}
	if ( !result ){
		alert('The "' + fieldLabel +'" field is limited to ' + fieldlength + ' characters.' );
	}
	
	return result;
}

function textCounter( field , countfield , maxlimit ) {
	// update 'characters left' counter
	else {
		countfield.value = maxlimit - field.value.length;
	}
}
//var help_overview , help_positdescr , help_summary , help_bullets ;

//help_overview = 'The purpose of the mini-profile is to give a brief summary of who you are and your qualifications for a new position. The objective is to have your profile spark the interest of prospective employers to the point where they will want to see your entire resume and hopefully will want to meet you for an interview. The profile needs to be brief and to the point and should reflect the highlights of your resume. The total length should not exceed 100 words.' ;


</script>
<?

mysql_connect( $dbhost, $dbuser, $dbpw ) or die( mysql_error() );
mysql_select_db( $dbname ) or die(mysql_error());
$sql_count_cat = "SELECT cat_num, cat_id, cat_descr FROM " . $tablepre . "profile_cat ORDER BY cat_descr ASC" ;
$result = mysql_query( $sql_count_cat ) or die( mysql_error() ) ;
$max_cat_num =   mysql_num_rows( $result ) ;
if ( mysql_num_rows( $result ) ) {
	while ( $row = mysql_fetch_assoc( $result ) ) {
	//	$text_array = array( $row["cat_num"] , $text_array );
		$cat_array[ $row["cat_id"] ] = $row["cat_descr"]   ;
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

function write_catlist( $cat_array , $currents , $current_val = "  " ){
	global $max_cat_num ;
	// function to write out the checkbox list of categories
	$droplist = "<select name=\"cat_id\" id=\"cat_id\" size=\"1\">\n" ;
	$i = 1 ;
	$droplist .= writeoption( $current_val , "  " , "none selected" ) ;
	foreach( $cat_array as $key => $value ){
		if ( ( $key != $currents [ 0 ] && $key != $currents [ 1 ] && $key != $currents [ 2 ] ) || $key == $current_val ) {
			$droplist .= writeoption( $current_val , $key , $value ) ;
		}
	}
	$droplist .= "</select>\n" ;
	return  $droplist  ;
}

function write_textfields( $fieldname , $field_caption , $stringlength , $field_comment = "" , $current_value = "" , $fieldclass = "formtype" ) {
	// function to write out either a text field or textbox field
	// with a Javascript-enforced limit on the maximum string length
	$field_html = "\t<td align=\"right\" valign=\"middle\" class=\"fieldname\">" ;
	$field_html .= $field_caption . ":<br />\n" . $field_comment  . "</td>\n\t<td valign=\"middle\">" ;
	$columns = 44 ;
	if ( $stringlength > $columns ) {
		$rows = ceil( $stringlength / $columns ) ;
		$field_html .= "<textarea cols=\"" . $columns . "\" rows=\"" . $rows . "\" name=\"" . $fieldname . "\" id=\"" . $fieldname . "\"" ;
		$field_html .= " class=\"" . $fieldclass . "\" onBlur=\"validlength( this , '" . $fieldname . "' , " . $stringlength . " )\">" ;
		$field_html .= $current_value . "</textarea></td>" ;
	}
	else {
		$field_html .= "<text type=\"text\" class=\"" . $fieldclass . "\"" ;
		$field_html .= " name=\"" . $fieldname . "\" id=\"" . $fieldname . "\"" ;
		$field_html .= " size=\"" . $stringlength > "\" maxlength=\"" . $stringlength . "\" >" ;
		$field_html .= $current_value . "</td>" ;
	}
	return $field_html ;
}

function add_profile( $_POST , $tablepre = "euconnect_" ){
	// function to process the form results into a SQL query
	// to insert the member data
	$insert_sql = "INSERT INTO " . $tablepre . "profiles " ;
	$insert_sql .= "( euid , priority , cat_id , posit_descr , " ;
	$insert_sql .= "overview , bullet1 , bullet2 , bullet3 , " ;
	$insert_sql .= "bullet4 , bullet5 ) VALUES " ;
	$insert_sql .= "( \"" . $_POST["euid"] . "\", " ;
	$insert_sql .= " \"" . $_POST["priority"] . "\", " ;
	$insert_sql .= "\"" . $_POST["cat_id"] . "\", " ;
	$insert_sql .= "\"" . addslashes ( $_POST["posit_descr"] ) . "\", " ;
	$insert_sql .= "\"" . addslashes ( $_POST["overview"] ) . "\", " ;
	$insert_sql .= "\"" . addslashes ( $_POST["bullet1"] ) . "\", " ;
	$insert_sql .= "\"" . addslashes ( $_POST["bullet2"] ) . "\", " ;
	$insert_sql .= "\"" . addslashes ( $_POST["bullet3"] ) . "\", " ;
	$insert_sql .= "\"" . addslashes ( $_POST["bullet4"] ) . "\", " ;
	$insert_sql .= "\"" . addslashes ( $_POST["bullet5"] ) . "\" ) " ;
	//	echo $insert_sql ;
	$result = mysql_query( $insert_sql ) or die( mysql_error() ) ;
	echo "<div align=\"center\"><h4>Profile " . $_POST["priority"] . " added successfully.</h4></div>" ;
	return $result ;
}

function update_profile( $_POST , $tablepre = "euconnect_" ){
	// function to process the form results into a SQL query
	// to update the member data
	$update_sql = "UPDATE " . $tablepre . "profiles SET " ;
	$update_sql .= " euid = \"" . addslashes ( $_POST["euid"] ) . "\" " ;
	$update_sql .= ", released = 0 " ;
	$update_sql .= ", priority = " . addslashes ( $_POST["priority"] ) . " " ;
	$update_sql .= ", cat_id = \"" . addslashes ( $_POST["cat_id"] ) . "\" " ;
	$update_sql .= ", posit_descr = \"" . addslashes ( $_POST["posit_descr"] ) . "\" " ;
	$update_sql .= ", overview = \"" . addslashes ( $_POST["overview"] ) . "\" " ;
	$update_sql .= ", bullet1 = \"" . addslashes ( $_POST["bullet1"] ) . "\" " ;
	$update_sql .= ", bullet2 = \"" . addslashes ( $_POST["bullet2"] ) . "\" " ;
	$update_sql .= ", bullet3 = \"" . addslashes ( $_POST["bullet3"] ) . "\" " ;
	$update_sql .= ", bullet4 = \"" . addslashes ( $_POST["bullet4"] ) . "\" " ;
	$update_sql .= ", bullet5 = \"" . addslashes ( $_POST["bullet5"] ) . "\" " ;
	$update_sql .= " WHERE prof_id = \"" . $_POST["prof_id"] . "\" " ;
	//	echo $update_sql ;
	$result = mysql_query( $update_sql ) or die( mysql_error() ) ;
	echo "<div align=\"center\"><h4>" . priority_label( $_POST["priority"] ) . " profile  updated successfully.</h4></div>" ;
	return $result ;
}

function delete_profile( $_POST , $tablepre = "euconnect_" ){
	// function to process the form results into a SQL query
	// to update the member data
	// delete profile
	$delete_sql = "DELETE FROM " . $tablepre . "profiles WHERE prof_id = \"" . $_POST["prof_id"] . "\" " ;
	// echo $delete_sql  ;
	$result = mysql_query( $delete_sql ) or die( mysql_error() ) ;
	// increment priority settings for lower-priority profiles
	$upgrade_sql = "UPDATE " . $tablepre . "profiles SET priority = ( priority - 1 ) " ;
	$upgrade_sql .= " WHERE euid = \"" . $_POST["euid"] . "\" AND priority >= " . $_POST["priority"]  ;
	// echo "\n<br />\n" .$upgrade_sql ;
	$result = mysql_query( $upgrade_sql ) or die( mysql_error() ) ;
	return $result ;
}

function write_profileform( $priority_str  , $profile , $currents , $user_admin , $blank_profile = 0 ) {
	// function to write out the member's profile in form
	global $cat_array , $page_name ;
	$release_caption  = "Release Profile" ;
	$delete_caption  = "Delete Profile" ;
	if ( $blank_profile == 1 ) {
		$caption = "Add Profile" ;
	}
	else {
		$caption = "Update Profile" ;
	}
?>
<table width="100%" cellspacing="5" align="center" class="memberform">
<form action="member.php" method="post" name="profile<?= $profile[ "priority"] ?>" id="profile<?= $profile[ "priority"] ?>">
	<input type="hidden" name="prof_id" id="prof_id" value="<?= $profile[ "prof_id"] ?>">
	<input type="hidden" name="euid" id="euid" value="<?= $profile[ "euid" ] ?>">
	<input type="hidden" name="priority" id="priority" value="<?= $profile[ "priority"] ?>">
	<input type="hidden" name="sql" id="sql" value="<?= $caption ?>">
	<input type="hidden" name="page" id="page" value="<?= str_replace ( "console/", "" , str_replace ( ".inc.php" , "", $page_name ) ) ?>">
<?php
	$profilestr = "" ;
	$pad_id = str_pad( $profile["euid"] , 5, "0", STR_PAD_LEFT);
	$cat_id = $profile["cat_id"] ;
	$profile_bgcolor = "" ;
	$profile_status = "Not submitted" ;
	switch ( $profile["released"] ) {
		case -1 :
			// color-coded red
			// profile returned for rewrite
			$profile_bgcolor = "#FF0000" ;
			$profile_status = "Returned for rewrite" ;
			break ;
		case 0 :
			// color-coded yellow
			// profile pending review
			$profile_bgcolor = "#FFFF00" ;
			if ( $profile[ "prof_id" ] != 0 ) {
				$profile_status = "Pending review" ;
			}
			break ;
		case 1:
			// color-coded green
			// profile cleared for search engine
			$profile_bgcolor = "#008000" ;
			$profile_status = "Released to search engine" ;
			break ;
		default :
			// color-coded yellow
			// profile pending review
			$profile_bgcolor = "#FFFF00" ;
			break ;
	}
?><tr>
	<td colspan="2" align="center" valign="middle" bgcolor="<?= $profile_bgcolor ?>" class="fieldname"><?= $priority_str ?> Profile<br /><font size="-1">(<?= $cat_id . "-" . $pad_id ?>)<br />(<?= $profile_status ?>)</font></td>
</tr><tr>
	<td align="right" valign="middle" class="fieldname">Career Category:</td>
	<td valign="middle"><?= write_catlist( $cat_array , $currents, $profile["cat_id"] ) ?></td>
</tr>
<tr>
<!-- help_overview( ) -->
	<? echo write_textfields( "posit_descr" , "Desired Position" , 50 , "<a href=\"javascript:onClick=alert( 'This should be an industry term that best describes what you do.\nKeep it brief and to the point.\n' )\">Help</a>" , $profile["posit_descr"] , "formtype" ) ; ?>
</tr>
<tr>
	<? echo write_textfields( "overview" , "Overview" , 200 , "(200 characters or fewer)<br /><a href=\"javascript:onClick=alert( 'This paragraph should be a summary of what you do.\nCan use years of experience, perhaps note a significant employer, and describe your capabilities and responsibilities.\nShould be similar to your \\'60-second me.\\'\\nShould be no longer than 40 to 50 words.\\n' )\">Help</a>" , stripslashes( $profile["overview"] ) , "formtype" ) ; ?>
</tr>
<tr>
	<? echo write_textfields( "bullet1" , "Experience" , 100 , "(100 characters or fewer)<br /><a href=\"javascript:onClick=alert( 'Up to five brief statements with each preceded by a bullet.\nUse action words where possible with emphasis on key skills, qualifications and accomplishments.\nQuantify accomplishments as much as possible (dollars, percentage, rate of improvement).\nTotal section should be no longer than *150 words.\n' )\">Help</a>" , stripslashes( $profile["bullet1"] ) , "formtype" ) ; ?>
</tr>
<tr>
	<? echo write_textfields( "bullet2" , "Bullet 1" , 100 , "(100 characters or fewer)<br /><a href=\"javascript:onClick=alert( 'Up to five brief statements with each preceded by a bullet.\nUse action words where possible with emphasis on key skills, qualifications and accomplishments.\nQuantify accomplishments as much as possible (dollars, percentage, rate of improvement).\nTotal section should be no longer than *150 words.\n' )\">Help</a>" , stripslashes( $profile["bullet2"] ) , "formtype" ) ; ?>
</tr>
<tr>
	<? echo write_textfields( "bullet3" , "Bullet 2" , 100 , "(100 characters or fewer)<br /><a href=\"javascript:onClick=alert( 'Up to five brief statements with each preceded by a bullet.\nUse action words where possible with emphasis on key skills, qualifications and accomplishments.\nQuantify accomplishments as much as possible (dollars, percentage, rate of improvement).\nTotal section should be no longer than *150 words.\n' )\">Help</a>" , stripslashes( $profile["bullet3"] ) , "formtype" ) ; ?>
</tr>
<tr>
	<? echo write_textfields( "bullet4" , "Industry buzz words" , 50 , "(50 characters or fewer)<br /><a href=\"javascript:onClick=alert( 'Up to five brief statements with each preceded by a bullet.\nUse action words where possible with emphasis on key skills, qualifications and accomplishments.\nQuantify accomplishments as much as possible (dollars, percentage, rate of improvement).\nTotal section should be no longer than *150 words.\n' )\">Help</a>" , stripslashes( $profile["bullet4"] ) , "formtype" ) ; ?>
</tr>
<tr>
	<? echo write_textfields( "bullet5" , "Education" , 50 , "(50 characters or fewer)<br /><a href=\"javascript:onClick=alert( 'Up to five brief statements with each preceded by a bullet.\nUse action words where possible with emphasis on key skills, qualifications and accomplishments.\nQuantify accomplishments as much as possible (dollars, percentage, rate of improvement).\nTotal section should be no longer than *150 words.\n' )\">Help</a>" , stripslashes( $profile["bullet5"] ) , "formtype" ) ; ?>
</tr>
<tr>
	<td colspan="2" align="center" valign="middle"><input type="submit" name="submit" id="submit" value="<?= $caption ?>"></td>
</tr>
</form>
<?php  
// don't display Delete Profile button if profile doesn't exist
if( $profile["prof_id"] > 0 ){  ?>
       <form action="member.php" method="post" name="delete_profile<?= $profile[ "priority"] ?>" id="delete_profile<?= $profile[ "priority"] ?>">
	<input type="hidden" name="prof_id" id="prof_id" value="<?= $profile[ "prof_id"] ?>">
	<input type="hidden" name="euid" id="euid" value="<?= $profile[ "euid" ] ?>">
	<input type="hidden" name="cat_id" id="cat_id" value="<?= $profile[ "cat_id" ] ?>">
	<input type="hidden" name="priority" id="priority" value="<?= $profile[ "priority"] ?>">
	<input type="hidden" name="page" id="page" value="<?= str_replace ( "console/", "" , str_replace ( ".inc.php" , "", $page_name ) ) ?>">
	<input type="hidden" name="sql" id="sql" value="<?= $delete_caption ?>">
<tr>
	<td colspan="2" align="center" valign="middle"><input type="submit" name="submit" id="submit" value="<?= $delete_caption ?>"></td>
</tr>
</form>
<?php }?>

</table>
<?php
	return "<hr />" ;
}

function priority_label( $priority ) {
	switch ( $priority ) {
		case "1" :
			$priority_str = "Primary" ;
			break ;
		case "2" :
			$priority_str = "Secondary" ;
			break ;
		case "3" :
			$priority_str = "Tertiary" ;
			break ;
		default :
			$priority_str = "Wildcard" ;
			break ;
		}
	return $priority_str ;
}

function write_member( $record ) {
	global $cat_array, $tablepre  ;
	$euid = $record[ "euid" ] ;
	
	$currents = array( "  ", "  " , "  " ) ;
	$sql_current = "SELECT priority, cat_id FROM euconnect_profiles WHERE euid = $euid ORDER BY priority ASC " ;
	$result = mysql_query( $sql_current ) or die( mysql_error() ) ;
	$max_cat_num =   mysql_num_rows( $result ) ;
	if ( mysql_num_rows( $result ) ) {
		while ( $row = mysql_fetch_assoc( $result ) ) {
		//	$text_array = array( $row["cat_num"] , $text_array );
			$currents[ $row["priority"] - 1 ] = $row["cat_id"]   ;
		}
	}
	
?>
<table align="center" class="memberform"  cellpadding="8" width="100%">
<tr>
	<td colspan="2" align="center"><h3><?php
	echo $record[ "firstname" ] . " " . $record[ "lastname" ] ;
	if ( $record[ "active" ] == 0 ){
		echo "<font size=\"-1\" color=\"#FF0000\">INACTIVE</font>" ;
	} ?></h3></td>
</tr>
<?php
	$prof_int = 1 ;
	$profile_sql = "SELECT prof_id, euid, released, priority, cat_id, posit_descr, overview, bullet1, bullet2, bullet3, bullet4, bullet5 FROM euconnect_profiles WHERE euid = $euid ORDER BY priority ASC " ;
	//echo $profile_sql ;
	$result = mysql_query( $profile_sql ) or die( mysql_error() ) ;
	if ( mysql_num_rows( $result ) ) {
		while ( $profile = mysql_fetch_assoc( $result ) ) {
			$priority = ( $profile[ "priority"] <= $prof_int ) ? $profile[ "priority"] : $prof_int ;
	 	echo write_profileform( priority_label( $priority ) , $profile , $currents, $record["userlevel"] )   ;
		$prof_int++ ;
		}
	}
	if ( $prof_int <= 3 ) {
		// create default array
		//$profile = array(
			$profile[ "prof_id" ] = 0 ;
			$profile[ "euid" ] = $euid ; 
			$profile[ "released" ] = 0 ;
			$profile[ "priority" ] = $prof_int ;
			$profile[ "cat_id" ] = "  " ;
			$profile[ "posit_descr" ] = "" ;
			$profile[ "overview" ] = "" ;
			$profile[ "bullet1" ] = "" ;
			$profile[ "bullet2" ] = "" ;
			$profile[ "bullet3" ] = "" ;
			$profile[ "bullet4" ] = "" ;
			$profile[ "bullet5" ] = "" ;
			$priority = $prof_int ;
	 	echo write_profileform( priority_label( $priority ) , $profile , $currents , $record["userlevel"], 1 )   ;
	}
?>	</td>
</tr>
<?
}

$new = 0 ;
if ( $_POST["euid"] || ( $_GET["euid"] && $user_euid == $_GET["euid"] )  || ( $_SESSION[ "ueuid" ] && $user_euid == $_SESSION[ "ueuid" ] )  ) {
	$current_euid = ( $_SESSION["ueuid"] ) ? $_SESSION["ueuid"] : "000000" ;
	$current_euid = ( $_GET["euid"] ) ? $_GET["euid"] : $current_euid ;
	$current_euid = ( $_POST["euid"] ) ? $_POST["euid"] : $current_euid ;
	switch ( $_POST["sql"] ) {
		case "Update Profile":
			update_profile( $_POST ) ;
			break ;
		case "Add Profile" :
			add_profile( $_POST ) ;
			break ;
		case "Release Profile":
			release_profile( $_POST ) ;
			break ;
		case "Delete Profile" :
			delete_profile( $_POST ) ;
			break ;
		default :
	}
	// query database for member info
	$sql_memberfetch = "SELECT * FROM " . $tablepre . "members WHERE euid = " . $current_euid ;
	$result = mysql_query( $sql_memberfetch ) or die( mysql_error() ) ;
	if ( mysql_num_rows( $result ) ) {
		$member_row = mysql_fetch_assoc( $result ) ;
		}
}
if ( !is_array( $member_row ) ) {
	// create default array
	$member_row = array(
		"euid" => "000000",
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

write_member( $member_row ) ;


?>

