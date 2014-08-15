<?
require_once( "include/config.inc.php" ) ;
include_once("console/commonfunctions.inc.php") ;
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
  <head><?
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
	$profilestr .= "<dt class=\"profname\">" . $cat_id . "-" . $pad_id ;
	if ( $posit_descr  != "" ){
		$profilestr .= "<br /><br /><font color=\"#FF0000\">" . stripslashes( $posit_descr ) . "</font>\n</dt>\n" ;
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
	return $profilestr ;

}

$display_id = ( $_POST[ "profileid" ] ) ? $_POST["profileid"] : $_GET["profileid"] ;
$windowmessage = "Send Message to Candidate " . $display_id ;
if ( $_POST[ "sendmessage" ] == "1" ) {
	$candidate_euid = substr( $display_id , 3 ) ;
	$messagedate = date( "Y-m-d H:i:s" ) ;
	$profile_cat = substr( $display_id , 0 , 2 ) ;
	$messagebody = $_POST["messagebody"] ;
	$returnaddress = $_POST["returnaddress"] ;
	mysql_connect( $dbhost, $dbuser, $dbpw ) or die( "line " . __LINE__ . ": " . mysql_error() ) ;
	mysql_select_db( $dbname ) or die(mysql_error()) ;
	$membersql = "SELECT euid, lastname, firstname, email_home FROM " . $tablepre . "members WHERE euid = " . $candidate_euid ;
	$result = mysql_query( $membersql ) or die( "line " . __LINE__ . ": " . mysql_error() . "br />" . $membersql ) ;
	$memberrow = mysql_fetch_assoc( $result ) ;
	$candidate = array ( $memberrow[ "email_home" ] => $memberrow[ "firstname" ] . " " . $memberrow[ "lastname" ] , "eudropzone@eu-connections.org" => "EU Employer Contact Coordinator" ) ;
	// $candidate = array ( "william@clardy.org" => $memberrow[ "firstname" ] . " " . $memberrow[ "lastname" ] ) ;
	$profilesql = "SELECT euid, cat_id , posit_descr , overview , bullet1 , bullet2 , bullet3 , bullet4 , bullet5 FROM " . $tablepre . "profiles WHERE euid = " . $candidate_euid . " AND cat_id like '" . $profile_cat . "' ;";
	$result = mysql_query( $profilesql ) or die( mysql_error() ) ;
	$profilerow = mysql_fetch_assoc( $result ) ;
	$posit_descr = $profilerow["posit_descr"] ;
	if ( $messagebody != "" ){
		$insert_sql = "INSERT INTO " . $tablepre . "searchmessages " ;
		$insert_sql .= "( euid , messagedate , profile_cat , employer_email , posit_descr , employer_message , profile_snapshot ) VALUES " ;
		$insert_sql .= "( " . $candidate_euid . " " ;
		$insert_sql .= ", \"" . date( "Y-m-d H:i:s" ) . "\" " ;
		$insert_sql .= ", \"" . $profile_cat . "\" " ;
		$insert_sql .= ", \"" . addslashes ( $returnaddress )  . "\" " ;
		$insert_sql .= ", \"" . addslashes ( $posit_descr )  . "\" " ;
		$insert_sql .= ", \"" . addslashes ( $messagebody )  . "\" " ;
		$insert_sql .= ", \"" . addslashes ( writeprofile( $profilerow ) ) . "\" ) " ;
		// echo "<p>$insert_sql</p>" ;
		$result = mysql_query( $insert_sql ) or die( "line " . __LINE__ . ": " . mysql_error()  ) ;
	//	if ( $result )
		if ( sendeumail( $candidate , "euredirect@eu-connections.org" , $messagebody . "\n\nReply to: " . $returnaddress , "Response to EU profile " . $display_id , 1 ) )
		{
		  $windowmessage = "Message sent!" ;
		}
		else
		{
		  $windowmessage = "Message delivery failed." ;
		}
		
	}
?>    <title><?= $windowmessage ?></title>
	<script language="JavaScript">
<!--
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
//-->
</script>
<?
	$returnstring = "</head>\n<body bgcolor=\"#FFFFFF\" onblur=\"self.focus();\" onload=\"self.focus();setTimeout(window.close, 5000)\">\n" ;
	$returnstring .= "<div align=\"center\"><h4>Your message has been sent to EU member " . $display_id . ".</h4>" ;
	$returnstring .= "<form>\n\t<input type=\"button\" value=\"Close Window\" onClick=\"javascript:window.close();\">\n</form>\n</div>\n" ;
	echo $returnstring ;

}
else {
?>
    <title><?= $windowmessage ?></title>
	<script language="JavaScript">
<!--
function validEmail(email) {
	email = email.replace(/^\s*|\s*$/g, "");
	if ( email.length < 5 ) 
	{
		return false ;
	}
	/*	validate email address syntax and remove any emails that don't
	match the basic syntax pattern below.	*/
	re = /^(\w|[^_]\.|[\-])+((\@){1}([^_]))(([a-z]|[\d]|[\-]|\.)+|([^_]\.[^_])*)+\.[a-z]{2,3}$/i
	if (!re.test(email)) 
	{
		return false ;
	}
	/*	test for spaces before checking syntax. spaces are illegal in an email address.
		if a space is found, it's definitely an illegal address: */
	re = /\s+/g
	if (re.test(email)) 
	{
		return false ;
	}
	//fix the @@ problem...
	re = /\@\@/
	return(!re.test(email)) ;
}

function checkEmail( emailItem )
{     //runs the validate function and returns error box or nonerror

	if (emailItem.value == "")
	{
		return true;
	}

	if(!validEmail(emailItem.value))
	{
		alert("You have entered an invalid email address (" + emailItem.value + ")!\n Please re-enter it.");
		emailItem.focus();
		emailItem.select();
		return false;
		}
	return true;		
}				

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
//-->
</script>
  </head>
  <body bgcolor="#FFFFFF" onload="self.focus();">
    <p style="text-align: 'center'; font: large Arial; color: 'blue'">
    <span class="BodyTitleText">Send Message to Candidate <?= $display_id ?></span>
    <!-- Destination:  -->
    <form action="<?= $_SERVER[ "SCRIPT_NAME" ] ?>" method="post" enctype="multipart/form-data" name="sendmessage" id="sendmessage">
      <input type="hidden" name="profileid" value="<?= $display_id ?>">
	  <input type="hidden" name="sendmessage" id="sendmessage" value="1">
      <table border="0" cellspacing="0" cellpadding="4">
        <tr>
          <td align="center" colspan="3" >
          Insert your contact information and text message here:
          </td>
        </tr>
        <tr>
          <td colspan="3"><textarea cols="60" rows="15" name="messagebody" id="messagebody" onBlur="validlength( this , 'messagebody' , 900 );"></textarea></td>
        </tr>
        <tr>
          <td colspan="3"><input type="text" name="returnaddress" id="returnaddress" value="type your email address here" size="45" maxlength="50" onBlur="checkEmail( this ) ;"></td>
        </tr>
        <tr>
          <td align="CENTER" colspan="3"><input type="image" name="Send" src="images/red_send.gif" border="0" ></td>
        </tr>
      </table>
    </form>
    </p>
<? 
}
?>
  </body>
</html>