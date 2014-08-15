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
?><SCRIPT LANGUAGE="JavaScript">
<!-- 
function checkAll() {
	for (var j = 1; j <= <?php echo $max_cat_num ; ?>; j++) {
	box = eval("document.profilesearch.C" + j); 
	if (box.checked == false) box.checked = true;
   }
}

function uncheckAll() {
	for (var j = 1; j <= <?php echo $max_cat_num ; ?>; j++) {
	box = eval("document.profilesearch.C" + j); 
	if (box.checked == true) box.checked = false;
   }
}

function switchAll() {
	for (var j = 1; j <= <?php echo $max_cat_num ; ?>; j++) {
	box = eval("document.profilesearch.C" + j); 
	box.checked = !box.checked;
   }
}
//  -->
</script>
<?php

function writeboxlist( $cat_array , $_POST ){
	// function to write out the checkbox list of categories
	$boxlist = "" ;
	foreach( $cat_array as $key => $value ){
		 $boxlist .= writebox( $_POST , "C" . $value[ 0 ], $key , $value[ 1 ] ) ;
	}
	return  $boxlist  ;
}

function writebox( $_POST, $boxnum, $cat_id = "ID" , $cat_descr = "missing description" ) {
	// function to write out each categry's checkbox
	if ( $_POST["B2"] ) {
		if ( $_POST[ $boxnum ] ) {
			return "<input type=\"checkbox\" name=\"$boxnum\" value=\"$cat_id\" checked>$cat_descr<br />\n" ;
		}
		else {
			return "<input type=\"checkbox\" name=\"$boxnum\" value=\"$cat_id\" >$cat_descr<br />\n" ;
		}
	}
	else{
		return "<input type=\"checkbox\" name=\"$boxnum\" value=\"$cat_id\" checked>$cat_descr<br />\n" ;
	}
}

function list_profiles( $_POST , $tablepre = "euconnect_" , $selfreview = false ){
	// function to process the search form results into a SQL query and 
	// display the results
	global $max_cat_num  , $cat_array , $user_euid  ;
	$searchlist = "" ;
	$key_sql = "released = " . $_POST[ "released" ] . " " ;
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
	$search_sql = "SELECT prof_id, p.euid, released, cat_id, posit_descr, overview, bullet1, bullet2, bullet3, bullet4, bullet5 FROM euconnect_profiles p INNER JOIN euconnect_members m ON p.euid = m.euid " ;
	if ( $cat_sql != "" ){
		$search_sql .= "WHERE ( $cat_sql ) AND ( $key_sql ) " ;
	}
	else {
		$search_sql .= "WHERE ( $key_sql ) " ;
	}
	$search_sql .= " AND TRIM( concat( overview, bullet1, bullet2, bullet3, bullet4, bullet5 ) ) NOT LIKE '' " ;
	if ( !$selfreview ) {
		$search_sql .= " AND m.active != 0 AND p.euid != " . $user_euid . " " ;
	}
	$search_sql .= " ORDER BY cat_id ASC , euid ASC ;" ;
	$result = mysql_query( $search_sql ) or die( mysql_error() ) ;
	$searchlist = "<p>Your search matched " . mysql_num_rows( $result ) ;
	if ( mysql_num_rows( $result ) == 1 ){
		$searchlist .= " member's profile.</p>\n" ;
	}
	else {
		$searchlist .= " members' profiles.</p>\n" ;
	}
	if ( mysql_num_rows( $result ) ) {
		$currentcat = "" ;
		$n = 0 ;
		while ( $row = mysql_fetch_assoc( $result ) ) {
			if ( $currentcat != $row["cat_id"] ) {
				$currentcat = $row["cat_id"] ;
				$searchlist .=  "</dl></dd></dl>" ;
				 $cat_descr_array = $cat_array[ $currentcat ] ;
				$searchlist .=  "<dl><dt class=\"profile_cat\">" . $cat_descr_array[ 1 ] . "</dt><dd><br /><dl>\n" ;
			}
			$searchlist .=  writeprofile( $row , $n ) ;
			$n++ ;
		}
		$searchlist .=  "</dl></dd></dl>\n" ;
	}
	return $searchlist ;
}

function writeprofile( $profile , $seq = 0 )
{
	global $user_admin , $user_euid ;
	$profilestr = "" ;
	$pad_id = str_pad( $profile["euid"] , 5 , "0" , STR_PAD_LEFT ) ;
	$cat_id = $profile["cat_id"] ;
	$posit_descr = $profile["posit_descr"] ;
	$overview = $profile["overview"] ;
	$bullet1 = $profile["bullet1"] ;
	$bullet2 = $profile["bullet2"] ;
	$bullet3 = $profile["bullet3"] ;
	$bullet4 = $profile["bullet4"] ;
	$bullet5 = $profile["bullet5"] ;
	$profilestr .= "<dt class=\"profname\">" . $cat_id . "-" . $pad_id ;
	if ( $posit_descr  != "" ){
		$profilestr .= "<br /><font color=\"#FF0000\">" . stripslashes( $posit_descr ) . "</font><br />\n</dt>\n" ;
	}
	else {
		$profilestr .= "</dt>\n" ;
	}
	$profilestr .= "<dd class=\"profile\">\n" ;
	if ( $overview  != "" ){
		$profilestr .= stripslashes( $overview ) . "<br />\n" ;
	}
	$profilestr .= "<ul>\n" ;
	if ( $bullet1  != "" ){
		$profilestr .= "<li>" . stripslashes( $bullet1 ) . "</li>\n" ;
	}
	if ( $bullet2  != "" ){
		$profilestr .= "<li>" . stripslashes( $bullet2 ) . "</li>\n" ;
	}
	if ( $bullet3  != "" ){
		$profilestr .= "<li>" . stripslashes( $bullet3 ) . "</li>\n" ;
	}
	if ( $bullet4  != "" ){
		$profilestr .= "<li>" . stripslashes( $bullet4 ) . "</li>\n" ;
	}
	if ( $bullet5  != "" ){
		$profilestr .= "<li>" . stripslashes( $bullet5 ) . "</li>\n" ;
	}
	$profilestr .= "</ul></dd>\n" ; 
	$profilestr .= "<form action=\"console/process_profile.php\" method=\"post\" onsubmit=\"window.open('about:blank','release','width=600,height=285');\" "  ;
	$profilestr .= "name=\"release" . $seq  . "\" id=\"release" . $seq  . "\" target=\"release\">\n" ;
	$profilestr .= "<input name=\"page\" type=\"hidden\" value=\"process_profile\">\n" ;
	$profilestr .= "<input name=\"displayid\" type=\"hidden\" value=\"" . $cat_id . "-" . $pad_id . "\">\n" ;
	$profilestr .= "<input name=\"ueuid\" type=\"hidden\" value=\"" . $user_euid . "\">\n" ;
	$profilestr .= "<input name=\"user_admin\" type=\"hidden\" value=\"" . $user_admin . "\">\n" ;
	$profilestr .= "<input type=\"hidden\" name=\"prof_id\" id=\"prof_id\" value=\"" . $profile["prof_id"] . "\">\n<table>\n<tr>\n" ;
	$profilestr .= "	<td style=\"color: #FFFF00;background-color: #FF0000;\"><input type=\"radio\" name=\"approve\" value=\"0\">Return for Rewrite</td>\n" ;
	$profilestr .= "	<td style=\"color: #FFFF00;background-color: #008000;\"><input type=\"radio\" name=\"approve\" value=\"1\">Release to Search</button></td>\n" ;
	$profilestr .= "</tr>\n<tr>\n<td colspan=\"2\" align=\"center\" ><button type=\"submit\" name=\"process\" id=\"process\" value=\"1\">Process Profile</button></td>\n" ;
	$profilestr .= "</tr>\n</table>\n</form>\n" ;
	return $profilestr ;
}

?><font face="verdana, arial, helvetica, sans-serif" color="navy">
      <h2 align="left">Review Profiles</h2>
<?
	if ( $_POST["B2"] ) {
		echo "<p>" . list_profiles( $_POST  ) . "</p>\n" ;
	  	} ?>
<p> You can list new profiles by category. </p><?
$pcount_array = array( "rejected" => "0" , "rejected" => "0", "released" => "0" );
$sql_count_profiles = "SELECT released, COUNT( released ) AS freq FROM " . $tablepre . "profiles p INNER JOIN " . $tablepre . "members m ON p.euid = m.euid WHERE TRIM( concat( overview, bullet1, bullet2, bullet3, bullet4, bullet5 ) ) NOT LIKE '' AND m.active != 0 GROUP BY released" ;
$result = mysql_query( $sql_count_profiles ) or die( mysql_error() ) ;
if ( mysql_num_rows( $result ) ) {
	while ( $row = mysql_fetch_assoc( $result ) ) {		
		switch (  $row["released"] ) {
			case -1 :
				// profile returned for rewrite
				$pcount_array[ "rejected" ] = $row["freq"]  ;
				break ;
			case 0 :
				// profile pending review
				$pcount_array[ "pending" ] = $row["freq"]  ;
				break ;
			case 1:
				// profile cleared for search engine
				$pcount_array[ "released" ] = $row["freq"]  ;
				break ;
			default :
				break ;
		}
	}
}

?><form action="member.php" method="post" name="profilesearch" id="profiles" target="_self">
<input name="page" type="hidden" value="review_profiles" />
<table>
<tr>
	<td><input type="submit" value="List Profiles" name="B2" /></td>
	<td><input type="radio" name="released" value="-1">Rejected (<font color="#FF0000"><? echo $pcount_array[ "rejected" ] ; ?></font>)
	<br /><input type="radio" name="released" value="0" checked>Unreviewed (<? echo $pcount_array[ "pending" ] ; ?>)
	<br /><input type="radio" name="released" value="1">Previously Approved (<font color="#008000"><? echo $pcount_array[ "released" ] ; ?></font>)</td>
</tr>
</table>

<br /><br />
Select one or more profile categories below:<br /><br />
<input type=button value="Check All" onClick="checkAll()">&nbsp;<input type=button value="Uncheck All" onClick="uncheckAll()">&nbsp;
<input type=button value="Switch All" onClick="switchAll()"><br /><br />
<? echo writeboxlist( $cat_array , $_POST ) ; ?><br />
<input type=button value="Check All" onClick="checkAll()">&nbsp;<input type=button value="Uncheck All" onClick="uncheckAll()">&nbsp;
<input type=button value="Switch All" onClick="switchAll()">
<br /><br />
</form>
   </font>
