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

function openMsgWindow(theURL,winName,features) { //v2.0
    //Print theURL;
	window.open(theURL,winName,features);
  	return false;
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

function search_profiles( $_POST , $tablepre = "euconnect_" ){
	// function to process the search form results into a SQL query and
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
	$search_sql = "SELECT prof_id, p.euid, cat_id, posit_descr, overview, bullet1, bullet2, bullet3, bullet4, bullet5 FROM " . $tablepre . "profiles as p INNER JOIN " . $tablepre . "members as m ON p.euid = m.euid " ;
	if ( $cat_sql != "" && $key_sql != "" ){
		$search_sql .= "WHERE ( $cat_sql ) AND ( $key_sql ) " ;
	}
	elseif ( $key_sql != "" ){
		$search_sql .= "WHERE ( $key_sql ) " ;
	}
	elseif ( $cat_sql != "" ){
		$search_sql .= "WHERE ( $cat_sql ) " ;
	}
	$search_sql .= " AND TRIM( concat( overview, bullet1, bullet2, bullet3, bullet4, bullet5 ) ) NOT LIKE '' AND m.active > 0 AND p.released > 0 GROUP BY prof_id ORDER BY cat_id ASC ,  euid ASC ;" ;
	$result = mysql_query( $search_sql ) or die( mysql_error() ) ;
	$searchlist = "<p>Your search matched " . mysql_num_rows( $result ) ;
	if ( mysql_num_rows( $result ) == 1 ){

		$searchlist .= " prospective employee's profile.</p>\n" ;
	}
	else {
		$searchlist .= " prospective employees' profiles.</p>\n" ;
	}
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

function writeprofile( $profile )
{
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
	//$profilestr .= "<dt class=\"profname\">" . $cat_id . "-" . $pad_id ;
	$profile_id = $cat_id . "-" . $pad_id;

	$profilestr .= "<TABLE ALIGN=LEFT WIDTH=100><TR><TD>" . $profile_id . "</TD></TR></TABLE>";


	if ( $posit_descr  != "" ){

		$url = "contactcandidate.php?profileid=" . $profile_id;
		$profilestr .= "<TABLE ALIGN=RIGHT WIDTH=200><TR><TD>";
		$profilestr .= "<a href=\"". $url . "\" onclick=\"window.open('$url','contact_candidate','width=600,height=400');return false\">";
		$profilestr .= "CONTACT " . $profile_id . "</a></TD></TR></TABLE>";

		$profilestr .= "<br /><br /><font color=\"#FF0000\">" . stripslashes( $posit_descr ) . "</font><br />\n</dt>\n" ;
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
	$profilestr .= "</ul></dd>\n<br />\n<br />\n" ;
	return $profilestr ;

}


?>
