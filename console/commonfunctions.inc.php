<?php
// repository for commonly used functions

function removecrlf($string) {
	return strtr($string, "\015\012", "  ");
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

function sendeumail( $tolist , $sender, $body, $subject = "E.U. Anaheim e-mail notice" , $blindcopies = 1 ) {
	// build the email
	$mailheaders = "From:" . $sender . "\n";
	/* address recipients 
	the $tolist parameter should be an array with 
	the key name set to the user's actual email address and
	the value set to the recipients' display name
	*/
	if ( count( $tolist ) == 1 ) {
			foreach( $tolist as $key => $displayname ){
					$mailheaders .= "To: \"" . removecrlf( $displayname ) . "\" <" . removecrlf( $key ) . ">\n" ;
			}
	}
	else {
		$mailheaders .= "To: \"" . removecrlf( $sender ) . "\"\n" ;
		// $mailheaders .= "Bcc: jhaugen@edd.ca.gov\n" ;
		if ( 0 == $blindcopies ) {
			foreach( $tolist as $key => $displayname ){
					$mailheaders .= "Cc: \"" . removecrlf( $displayname ) . "\" <" . removecrlf( $key ) . ">\n" ;
			}
		}
		else {
			foreach( $tolist as $key => $displayname ){
					$mailheaders .= "Bcc:" . removecrlf( $displayname ) . "<" . removecrlf( $key ) . ">\n" ;
			}
		}
	}
	$mailheaders .= "Reply-To:" . removecrlf( $sender ) . "\n" ;
	$mailheaders .= "X-Mailer: EU-Mail (http://www.eu-connections.org/)\r\n";
	$messagebody = stripslashes( $body );
	return mail( "", $subject, $messagebody, $mailheaders); 

	// echo "<pre>\r\n$advisory\r\nFrom: " . htmlspecialchars( $sender ) . "\r\n" . htmlspecialchars( $mailheaders) . "\r\n$messagebody</pre>\r\n" ;
	// $returnstring = htmlspecialchars( $mailheaders) . "\r\nSubject: " . htmlspecialchars( $subject ) . "\r\n$messagebody</pre>\r\n" ;
	// $returnstring .= "<p>Message sent!</p>\r\n" ;
	// return $returnstring ;
}

function echo_parameters() {
	$post_result = "<table align=\"center\" bgcolor=\"#FFFF00\" cellspacing=\"2\" cellpadding=\"2\" border=\"0\">\n" ;
	foreach( $_POST as $FormFieldName => $FormFieldValue ) {
		$post_result .= "<tr>\n<td align=\"right\">\$_POST[ " . $FormFieldName . " ]</td>\n<td>" . $FormFieldValue . "</td>\n</tr>\n" ;
	}
	$post_result .= "</table>\n" ;
	
	$get_result .= "<table align=\"center\" bgcolor=\"#FFFF00\" cellspacing=\"2\" cellpadding=\"2\" border=\"0\">\n" ;
	foreach( $_GET as $ParamName => $ParamValue ) {
		$get_result .= "<tr>\n<td align=\"right\">\$_GET[ " . $ParamName . " ]</td>\n<td>" . $ParamValue . "</td>\n</tr>\n" ;
	}
	$get_result .= "</table>\n" ;
	
	$session_result .= "<table align=\"center\" bgcolor=\"#FFFF00\" cellspacing=\"2\" cellpadding=\"2\" border=\"0\">\n" ;
	foreach( $_SESSION as $ParamName => $ParamValue ) {
		$session_result .= "<tr>\n<td align=\"right\">\$_SESSION[ " . $ParamName . " ]</td>\n<td>" . $ParamValue . "</td>\n</tr>\n" ;
	}
	$session_result .= "</table>\n" ;
	
	$server_result .= "<table align=\"center\" bgcolor=\"#FFFF00\" cellspacing=\"2\" cellpadding=\"2\" border=\"0\">\n" ;
	foreach( $_SERVER as $ParamName => $ParamValue ) {
		$server_result .= "<tr>\n<td align=\"right\">\$_SESSION[ " . $ParamName . " ]</td>\n<td>" . $ParamValue . "</td>\n</tr>\n" ;
	}
	$server_result .= "</table>\n" ;

	return $post_result . $get_result . $session_result . $server_result ;
}

?>