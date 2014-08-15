<?php


function removecrlf($string) {
	return strtr($string, "\015\012", "  ");
}

function mailsend( $fromaddress, $body, $headers ) {
	// send mail directly using sendmail
	$fp = popen("/usr/sbin/sendmail -t -f" . $fromaddress, "w");
	fputs($fp, $headers);
	fputs($fp, $body);
	fputs($fp, "\r\n");
	pclose($fp);
}

mysql_connect( $dbhost, $dbuser, $dbpw ) or die( mysql_error() );
mysql_select_db( $dbname ) or die(mysql_error());
$sql = "SELECT RTRIM( CONCAT( lastname, \", \", firstname) ) AS listname , RTRIM( CONCAT( firstname, \" \", lastname, \" <\", RTRIM( email_home ) , \">\" ) ) AS email FROM " . $tablepre . "members " ;
//																					", firstname, lastname,  ><arellano58@msn.com>
$select_sql =  " WHERE active = 1 and email_home LIKE \"%@%.%\" " ;
$sort_sql = " ORDER BY listname ASC" ;
$sql_email_list = $sql . $select_sql . $sort_sql ;
$result = mysql_query( $sql_email_list ) or die( mysql_error() ) ;
$max_cat_num =   mysql_num_rows( $result ) ;
if ( mysql_num_rows( $result ) ) {
	while ( $row = mysql_fetch_assoc( $result ) ) {
		$email_array[ $row["email"] ] = $row["listname"] ;
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

function write_maillist( $email_array , $current_val = "  " ){
	global $max_cat_num ;
	// function to write out the droplist of addressees
	$droplist = "<select name=\"to[]\" id=\"to[]\" size=\"5\" multiple=\"yes\">\n" ;
	$i = 1 ;
	$droplist .= writeoption( $current_val , "  " , "none selected" ) ;
	$droplist .= writeoption( $current_val , "allactive" , "all active members" ) ;
	//	$droplist .= writeoption( $current_val , "30day" , "new members" ) ;
	$droplist .= writeoption( $current_val , "website" , "Web Site Committee" ) ;
	$droplist .= writeoption( $current_val , "administration" , "Admin Committee" ) ;
	$droplist .= writeoption( $current_val , "technicals" , "Technical Committee" ) ;
	$droplist .= writeoption( $current_val , "trainers" , "Trainers" ) ;
	//	$droplist .= writeoption( $current_val , "chairs" , "Committee Chairs" ) ;
	foreach( $email_array as $key => $value ){
		$droplist .= writeoption( $current_val , $key , $value ) ;
	}
	$droplist .= "</select>\n" ;
	return  $droplist  ;
}

if ( $_POST["send"] == "Send" && $_POST["messagebody"] != "" ){
	// build the email
	/* recipients */
	//	$to = "William Clardy <wclardy@certifiednetsolutions.com>" ;
	//	$to = ( $_POST["to"] ) ? $_POST["to"] : $to ;	
	/* subject */
	$subject = $_POST["subject"] ;
	$tolist = $_POST["to"] ;

	// save from and start mailheaders with it
	$from_user = $_POST["from"] ;
	$mailheaders = "From:" . $from_user . "\n";

	$advisory = "" ;

	//	$mailheaders = "To: ". removecrlf($to)."\n";
	// Put To: as Cc: receipient later
	//$mailheaders = "To:" . removecrlf( $_POST["from"] ) . "\n" ;
	$mailheaders .= "To:" . removecrlf( $from_user ) . "\n" ;
	$mailheaders .= "Bcc: jhaugen@edd.ca.gov\n" ;

	switch ( $tolist[ 0 ] ) {
		case "allactive":
			$advisory = $tolist[ 0 ] . " option is not yet functional\r\n" ;
			$sql = "SELECT RTRIM( CONCAT( lastname, \", \", firstname) ) AS listname , RTRIM( CONCAT( firstname, \" \", lastname, \" <\", RTRIM( email_home ) , \">\" ) ) AS email FROM " . $tablepre . "members " ;
			$select_sql =  " WHERE active = 1 AND email_home LIKE \"%@%.%\" " ;
			$sort_sql = " ORDER BY listname ASC" ;
			$sql_email_list = $sql . $select_sql . $sort_sql ;
			$result = mysql_query( $sql_email_list ) or die( mysql_error() ) ;
			$max_cat_num =   mysql_num_rows( $result ) ;
			if ( mysql_num_rows( $result ) ) {
				while ( $row = mysql_fetch_assoc( $result ) ) {
					$mailheaders .= "Bcc:" . removecrlf( $row["email"] ) . "\n" ;
				}
			}
			break ;
		case "30day":
			$advisory = $tolist[ 0 ] . " option is not yet functional\r\n" ;
			break ;
		case "website":
			$advisory = $tolist[ 0 ] . " option is not yet functional\r\n" ;
			$sql = "SELECT RTRIM( CONCAT( lastname, \", \", firstname) ) AS listname , RTRIM( CONCAT( firstname, \" \", lastname, \" <\", RTRIM( email_home ) , \">\" ) ) AS email FROM " . $tablepre . "members AS m LEFT JOIN " . $tablepre . "committees AS c ON m.euid = c.euid " ;
			$select_sql =  " WHERE active = 1 AND email_home LIKE \"%@%.%\" AND website = 1 " ;
			$sort_sql = " ORDER BY listname ASC" ;
			$sql_email_list = $sql . $select_sql . $sort_sql ;
			$result = mysql_query( $sql_email_list ) or die( mysql_error() ) ;
			$max_cat_num =   mysql_num_rows( $result ) ;
			if ( mysql_num_rows( $result ) ) {
				while ( $row = mysql_fetch_assoc( $result ) ) {
					$mailheaders .= "Bcc:" . removecrlf( $row["email"] ) . "\n" ;
				}
			}
			break ;
		case "administration":
			$advisory = $tolist[ 0 ] . " option is not yet functional\r\n" ;
			$sql = "SELECT RTRIM( CONCAT( lastname, \", \", firstname) ) AS listname , RTRIM( CONCAT( firstname, \" \", lastname, \" <\", RTRIM( email_home ) , \">\" ) ) AS email FROM " . $tablepre . "members AS m LEFT JOIN " . $tablepre . "committees AS c ON m.euid = c.euid " ;
			$select_sql =  " WHERE active = 1 AND email_home LIKE \"%@%.%\" AND admin = 1 " ;
			$sort_sql = " ORDER BY listname ASC" ;
			$sql_email_list = $sql . $select_sql . $sort_sql ;
			$result = mysql_query( $sql_email_list ) or die( mysql_error() ) ;
			$max_cat_num =   mysql_num_rows( $result ) ;
			if ( mysql_num_rows( $result ) ) {
				while ( $row = mysql_fetch_assoc( $result ) ) {
					$mailheaders .= "Bcc:" . removecrlf( $row["email"] ) . "\n" ;
				}
			}
			break ;
		case "technicals":
			$advisory = $tolist[ 0 ] . " option is not yet functional\r\n" ;
			$sql = "SELECT RTRIM( CONCAT( lastname, \", \", firstname) ) AS listname , RTRIM( CONCAT( firstname, \" \", lastname, \" <\", RTRIM( email_home ) , \">\" ) ) AS email FROM " . $tablepre . "members AS m LEFT JOIN " . $tablepre . "committees AS c ON m.euid = c.euid " ;
			$select_sql =  " WHERE active = 1 AND email_home LIKE \"%@%.%\" AND tech = 1 " ;
			$sort_sql = " ORDER BY listname ASC" ;
			$sql_email_list = $sql . $select_sql . $sort_sql ;
			$result = mysql_query( $sql_email_list ) or die( mysql_error() ) ;
			$max_cat_num =   mysql_num_rows( $result ) ;
			if ( mysql_num_rows( $result ) ) {
				while ( $row = mysql_fetch_assoc( $result ) ) {
					$mailheaders .= "Bcc:" . removecrlf( $row["email"] ) . "\n" ;
				}
			}
			break ;
		case "trainers":
			$advisory = $tolist[ 0 ] . " option is not yet functional\r\n" ;
			break ;
		case "chairs":
			$advisory = $tolist[ 0 ] . " option is not yet functional\r\n" ;
			break ;
		case "  ": 
		default:
			foreach( $tolist as $key => $mailaddress ){
				$mailheaders .= "Bcc:" . removecrlf( $mailaddress ) . "\n" ;
			}
	}
	$mailheaders .= "Reply-To:" . removecrlf( $_POST["from"] ) . "\n" ;

	$mailheaders .= "X-Mailer: EU-Mail (http://www.eu-connections.org/)\r\n";

	$messagebody = stripslashes( $_POST["messagebody"] );

	if (( $attach != "none" ) && ( $attach != "" ) and (is_uploaded_file($attach)))
	{
		$file = fopen($attach, "r");
		$contents = fread($file, $attach_size);
		$encoded_attach = chunk_split(base64_encode($contents));
		fclose($file);
		
		$mailheaders .= "MIME-version: 1.0\r\n";
		$mailheaders .= "Content-type: multipart/mixed; ";
		$mailheaders .= "boundary=\"Message-Boundary\"\r\n";
		$mailheaders .= "Content-transfer-encoding: 7BIT\r\n";
		$mailheaders .= "X-attachments: $attach_name\r\n";

		$body_top = "--Message-Boundary\r\n";
		$body_top .= "Content-type: text/plain; charset=US-ASCII\r\n";
		$body_top .= "Content-transfer-encoding: 7BIT\r\n";
		$body_top .= "Content-description: Mail message body\r\n\r\n";

		$messagebody = $body_top . $messagebody;

		$messagebody .= "\r\n\r\n--Message-Boundary\r\n";
		$messagebody .= "Content-type: $attach_type; name=\"$attach_name\"\r\n";		
		$messagebody .= "Content-Transfer-Encoding: BASE64\r\n";
		$messagebody .= "Content-disposition: attachment; filename=\"$attach_name\"\r\n\r\n";
		$messagebody .= "$encoded_attach\r\n";
		$messagebody .= "--Message-Boundary--\r\n";
	}

	// $saveheaders = $mailheaders;
	// php mail command needs subject field
	//$mailheaders .= "Subject: " . removecrlf( stripslashes( $subject ) ) . "\r\n\r\n";

	// $from_user = $FORCE_FROM ? "$user@$IMAP_SERVER" : $user;
	// $from_user = $_POST["from"] ;

	// use php mail command instead
	// mailsend( $from_user, $messagebody, $mailheaders ) ;

	// $phpmailheaders .= "FROM:" . $from_user . "\n" . $saveheaders;
	mail( "", $subject, $messagebody, $mailheaders); 

	// echo "<pre>\r\n$advisory\r\nFrom: " . htmlspecialchars( $from_user ) . "\r\n" . htmlspecialchars( $mailheaders) . "\r\n$messagebody</pre>\r\n" ;
	echo "<pre>\r\n$advisory\r\n" . htmlspecialchars( $mailheaders) . "\r\nSubject: " . htmlspecialchars( $subject ) . "\r\n$messagebody</pre>\r\n" ;
	echo "<p>Message sent!</p>\r\n" ;
}
else { 
	$servername =  explode ( "." , $_SERVER["SERVER_NAME"] ) ;
	if ( $servername[ 0 ] == "www" ) {
		$domainname = array_shift( $servername ) ;
	}
	$domainname = implode( ".", $servername ) ;
?>
<h2 align="left">Send Email To Members</h2>
<form action="member.php" method="post" name="email" target="_self">
<input type="hidden" name="page" id="page" value="<?= str_replace ( "console/", "" , str_replace ( ".inc.php" , "", $page_name ) ) ?>">
<table>
<tr>
	<td align="right"><STRONG>To: </STRONG></td>
	<td><?php 	echo write_maillist( $email_array, "  " ) . "<br />\n"  ; ?></td>
	<!-- <td><input type="text" name="to" size="68" maxlength="80" value=""></td> -->
</tr>
<tr>
	<td align="right"><STRONG>From: </STRONG></td>
	<td><input type="text" name="from" size="68" maxlength="80" value="eudropzone@<?= $domainname ?>"></td>
</tr>
<tr>
	<td align="right"><STRONG>Subject: </STRONG></td>
	<td><input type="text" name="subject" size="68" maxlength="80" value=""></td>
</tr>
<tr>
	<td colspan="2"><STRONG>Message:<br /><textarea name="messagebody" rows=20 cols=70 wrap="hard"></textarea></STRONG></td>
</tr>
<tr>
	<td align="right"><STRONG>Attachment: </STRONG> (not functional yet)<br /></td>
	<td><input type=file name=attach size=68></td>
</tr>
</table>
<br /><br />
<input type="submit" name="send" value="Send">
<!-- input type="submit" name="submit" value="Preview" -->
<input type="submit" name="cancel" value="Cancel">
<br /><br />
</form>
<?php
}
?>