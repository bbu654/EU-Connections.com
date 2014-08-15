<?php
mysql_connect( $dbhost, $dbuser, $dbpw ) or die( mysql_error() );
mysql_select_db( $dbname ) or die(mysql_error());

// create droplist of distinct categories
$sql_count_cat = "SELECT DISTINCT site_category AS site_category FROM " . $tablepre . "links ORDER BY site_category ASC" ;
$result = mysql_query( $sql_count_cat ) or die( mysql_error() ) ;
if ( mysql_num_rows( $result ) ) {
	while ( $row = mysql_fetch_assoc( $result ) ) {
		$cat_array[ $row["site_category"] ] = $row["site_category"] ;
	}
}

function writeoption( $current_val, $opt_val = "  " , $opt_descr = "  " ) {
	// function to write out each categry's checkbox
	if ( $opt_val == $current_val ) {
		return "<option value=\"$opt_val\" selected>$opt_descr</option>\n" ;
	}
	else {
		return "<option value=\"$opt_val\">$opt_descr</option>\n" ;
	}
}

function write_catlist( $cat_array , $current_val = "  " ){
	// function to write out the checkbox list of categories
	$droplist = "<select name=\"site_category\" id=\"site_category\" size=\"1\">\n" ;
	$droplist .= writeoption( $current_val , "  " , "  " ) ;
	foreach( $cat_array as $key => $value ){
		$droplist .= writeoption( $current_val , $key , $value ) ;
	}
	$droplist .= "</select>\n" ;
	return  $droplist  ;
}

function clean_url( $url ){
	$url_array = parse_url ( $url ) ;
	if ( !is_array( $url_array ) ){
		return false;
	}
	// scheme
	$uri = ( !empty( $url_array["scheme"] ) ) ? $url_array["scheme"] . "://" : "http://" ;
	// user & pass
	if ( !empty( $url_array["user"])){
		$uri .= $url_array["user"].':'.$url_array["pass"].'@';
	}
	// host
	$uri .= urlencode( $url_array["host"] ) ;
	// port
	$port = ( !empty( $url_array["port"] ) ) ? ":" . $url_array["port"] : "" ;
	$uri .= $port ;
	// path
	$uri .= $url_array["path"] ;
	// fragment or query
	if (isset( $url_array["fragment"] ) ) {
		$uri .= "#" . $url_array["fragment"] ;
	} elseif ( isset( $url_array["query"] ) ) {
	  $uri .= "?" . $url_array["query"] ;
	}
	return $uri;
}

function add_link( $_POST , $tablepre = "euconnect_" ){
	// function to process the form results into a SQL query
	// to insert new link data
	
	$insert_sql = "INSERT INTO " . $tablepre . "links " ;
	$insert_sql .= "( site_category , site_descrip , site_url , site_comment " ;
	$insert_sql .= ") VALUES ( " ;
	$insert_sql .= " \"" . $_POST["site_category"] . "\" "  ;
	$insert_sql .= " , \"" . $_POST["site_descrip"] . "\" "  ;
	$insert_sql .= " , \"" . clean_url( $_POST["site_url"] ) . "\" "  ;
	$insert_sql .= " , \"" . $_POST["site_comment"] . "\" ) ; " ;

	$result = mysql_query( $insert_sql ) or die( mysql_error() ) ;
	$linkid = mysql_insert_id() ;
	return $linkid ;
	//	return $result ;
}

function update_link( $_POST , $tablepre = "euconnect_" ){
	// function to process the form results into a SQL query
	// to update link data
	
	$update_sql = "UPDATE " . $tablepre . "links SET " ;
	$update_sql .= "\n site_category = \"" . $_POST["site_category"] . "\" "  ;
	$update_sql .= " ,\n site_descrip = \"" . $_POST["site_descrip"] . "\" "  ;
	$update_sql .= " ,\n site_url = \"" . clean_url( $_POST["site_url"] ) . "\" " ;
	$update_sql .= " ,\n site_comment = \"" . $_POST["site_comment"] . "\" " ;
	$update_sql .= "\n WHERE linkid = " . $_POST["linkid"] . " ;\n" ;

	$result = mysql_query( $update_sql ) or die( mysql_error() ) ;
	return $_POST["linkid"] ;
	//	return $result ;
}

function writeform( $record , $new = "0" )
{
	global $cat_array, $page_name ;
?>
<script language="JavaScript">
<!--

//Highlight form element
var highlightcolor="lightyellow"
var ns6=document.getElementById&&!document.all
var previous=''
var eventobj

//Regular expression to highlight only form elements
var intended=/INPUT|TEXTAREA|SELECT|OPTION/

//Function to check whether element clicked is form element
function checkel(which){
	if (which.style&&intended.test(which.tagName)){
		if (ns6&&eventobj.nodeType==3)
			eventobj=eventobj.parentNode.parentNode
		return true
	}
	else
		return false
}

//Function to highlight form element
function highlight(e){
	eventobj=ns6? e.target : event.srcElement
	if (previous!=''){
		if (checkel(previous))
			previous.style.backgroundColor=''
	previous=eventobj
	if (checkel(eventobj))
		eventobj.style.backgroundColor=highlightcolor
	}
	else{
		if (checkel(eventobj))
			eventobj.style.backgroundColor=highlightcolor
		previous=eventobj
	}
}
				
function checkFields( formitem ) 
{
	var missinginfo = "";
	re = /[^A-Za-z0-9,\- \.']/
	if (re.test( formitem.site_descrip.value)) {
		missinginfo += "\n     -  Invalid Link Name.";
	}

	if ( formitem.site_descrip.value.length < 2) {
		missinginfo += "\n     -  Missing Link Name.";
	}
	
	if ( re.test(formitem.site_url.value ) {
		missinginfo += "\n     -  Invalid Url.";
	}

	if ( formitem.site_url.value.length < 2) {
		missinginfo += "\n     -  Invalid Url.";
	}
	
	if (missinginfo != "") {
		missinginfo ="_____________________________________________\n" +
		"You failed to fill in the following information correctly:\n" +
		missinginfo + "\n_____________________________________________" +
		"\nPlease re-enter and submit again!";
		alert( missinginfo ) ;
		return false;
	}
	else return true;
}

//-->
</script>
<h3 align="center">Add Useful Link</h3>
<form action="member.php" method="post" name="add_link" id="add_link"  onKeyUp="highlight(event)" onClick="highlight(event)" onSubmit="return checkFields(this);" >
	<input type="hidden" name="linkid" id="linkid" value="<?= $record[ "linkid" ] ?>">
	<input type="hidden" name="new" id="new" value="<?= $new ?>">
	<input type="hidden" name="page" id="page" value="<?= str_replace ( "console/", "" , str_replace ( ".inc.php" , "", $page_name ) ) ?>">
<table align="center" class="linkform">
<tr><td><table>
<tr>
	<td><strong>Category:</strong></td>
<td>
<?php echo write_catlist( $cat_array, $record[ "site_category" ] )  ; ?>
</td>
</tr>
<tr>
	<td><br /><strong>Name: </strong></td>
	<td valign="bottom"><input type="text" name="site_descrip" id="site_descrip" value="<?= $record[ "site_descrip" ] ?>" size="50" maxlength="96" onFocus="highlight(this) ;" ></td>
</tr>
<tr>
	<td><br /><strong>Url: </strong></td>
	<td valign="bottom"><input type="text" name="site_url" id="site_url" value="<?= $record[ "site_url" ] ?>"  size="50" maxlength="128" onFocus="highlight( this ) ;" ></td>
	<td></td>
</tr>
</td></tr></table>
<tr>
	<td colspan="2"><br /><STRONG>Description (optional):</STRONG><br/>
	<textarea name="site_comment" rows="4" cols="65" maxlength="128" wrap="hard"><?= $record[ "site_comment" ] ?></textarea></td>
	<td></td>
</tr>
<tr>
<?php if ( $new == "1" ) {
?>	<td align="left"><input type="submit" name="s1" id="s1" value="Add Link">
<?php }
else {
?>	<td align="left"><input type="submit" name="s1" id="s1" value="Update Link">
<?php }
?>	&nbsp;<input type="Reset"></td>
</tr>	
</table>
</form>

<?php 
} // end writeform

$new = ( $_POST["new"] ) ? $_POST["new"] : "1" ;
$sql_linkfetch = "SELECT linkid " ;
$sql_linkfetch .= ", RTRIM( site_category ) AS site_category , RTRIM( site_descrip ) AS site_descrip , RTRIM( site_url ) AS site_url " ;
$sql_linkfetch .= ", RTRIM( site_comment ) AS site_comment " ;
$sql_linkfetch .= " FROM " . $tablepre . "links " ;
$sql_linkfetch .= " WHERE linkid = " ;

if ( ( $_GET["linkid"] && $user_linkid == $_GET["linkid"] ) || ( $user_admin >= 1 && $_GET["linkid"] )  || $_POST["linkid"] ) {
	// query database for link info
	$record_linkid = ( $_POST["linkid"]  ) ? $_POST["linkid"] : $_GET["linkid"] ;
	if ( $_POST["site_url"] && $_POST["site_url"] != "" && $user_admin >= 1 ){
		// form results into a SQL query to insert the link data
		$record_linkid = update_link( $_POST ) ;
	}
	$sql_linkfetch .= $record_linkid ;
	$result = mysql_query( $sql_linkfetch ) or die( mysql_error() ) ;

	if ( mysql_num_rows( $result ) ) {
		$link_row = mysql_fetch_assoc( $result ) ;
		$new = "0" ;
	}
	else {
		$newlink = $_POST ;
		add_link ( $newlink ) ;
		$new = "0" ;
	}
}
elseif ( $_POST["site_url"] && $_POST["site_url"] != "" && $user_admin >= 1 ){
	// no linkid value means this is a new link
	// form results into a SQL query to insert the link data
	$record_linkid = add_link( $_POST ) ;
	if ( $record_linkid > 0 ) {
		$sql_linkfetch .= $record_linkid ;
		$result = mysql_query( $sql_linkfetch ) or die( mysql_error() ) ;
		if ( mysql_num_rows( $result ) ) {
			$link_row = mysql_fetch_assoc( $result ) ;
			$new = "0" ;
		}
		else {
			$newlink = $_POST ;
			add_link ( $newlink ) ;
			$new = "0" ;
		}
	}
}
else {
		// create default array
		$link_row = array(
			"linkid" => 0 ,
			"site_category" => "" ,
			"site_descrip" => "" ,
			"site_url" => "" ,
			"site_comment" => "" ) ;		
}

writeform( $link_row , $new ) ;

?>
<!-- add_link.inc.php -->
