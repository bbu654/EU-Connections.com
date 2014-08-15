 <?

mysql_connect( $dbhost, $dbuser, $dbpw ) or die( mysql_error() );
mysql_select_db( $dbname ) or die(mysql_error());
$sql_count_cat = "SELECT cat_id, cat_descr FROM " . $tablepre . "profile_cat ORDER BY cat_descr ASC" ;
$result = mysql_query( $sql_count_cat ) or die( mysql_error() ) ;
if ( mysql_num_rows( $result ) ) {
	while ( $row = mysql_fetch_assoc( $result ) ) {
		$cat_array[ $row["cat_id"] ] = $row["cat_descr"] ;
	}
}

$row = mysql_fetch_assoc( $result ) ;

function write_profile( $catlabel_str  , $profile ) {
	// function to write out the member's profile
	global $cat_array ;
	$profilestr = "" ;
	$pad_id = str_pad( $euid , 5, "0", STR_PAD_LEFT);
	$cat_id = $profile["cat_id"] ;
	$posit_descr = $profile["posit_descr"] ;
	$overview = $profile["overview"] ;
	$bullet1 = $profile["bullet1"] ;
	$bullet2 = $profile["bullet2"] ;
	$bullet3 = $profile["bullet3"] ;
	$bullet4 = $profile["bullet4"] ;
	$bullet5 = $profile["bullet5"] ;
	$profilestr .= "<dt class=\"profname\">" . $cat_id . $pad_id ;
	if ( $posit_descr  != "" ){
		$profilestr .= "&nbsp;&mdash;&nbsp;<font color=\"#FF0000\">$posit_descr</font>" ;
	}
	$profilestr .= "</dt>\n<dd class=\"profile\">\n" ;
	if ( $overview  != "" ){
		$profilestr .= $overview . "<br />\n" ;
	}
	if ( $bullet1 . $bullet2 . $bullet3 . $bullet4 . $bullet5 != "" ) {
		$profilestr .= "<ul>\n" ;
		if ( $bullet1  != "" ){
			$profilestr .= "<li>" . $bullet1 . "</li>\n" ;
		}
		if ( $bullet2  != "" ){
			$profilestr .= "<li>" . $bullet2 . "</li>\n" ;
		}
		if ( $bullet3  != "" ){
			$profilestr .= "<li>" . $bullet3 . "</li>\n" ;
		}
		if ( $bullet4  != "" ){
			$profilestr .= "<li>" . $bullet4 . "</li>\n" ;
		}
		if ( $bullet5  != "" ){
			$profilestr .= "<li>" . $bullet5 . "</li>\n" ;
		}
		$profilestr .= "</ul></dd>\n" ;
	}
	if( str_replace( "<dt class=\"profname\">" . $cat_id . $pad_id , "" , $profilestr  ) == "" ) {
		$profilestr .= "<font size=\"-1\" color=\"#FF0000\">EMPTY PROFILE</font></dd>\n" ;
	}
	$profilestr = "<tr>\n<td colspan=\"2\"><dt class=\"profname\">" . $catlabel_str . " Category: <font color=\"#008000\">" .  $cat_array[ $cat_id ] . "</font></dt>\n<dd class=\"profname\">" . $profilestr ;
	$profilestr .= "</blockquote></dd>\n" ;
	return $profilestr ;
}

function write_committee( $committeename , &$column , $ismember = 1 , $fieldname = "" , $isfield = 0 , $guttercol = 1 , $endcolumn = 2 ) {
	$committeestr = "" ;
	if ( $isfield == 0  && $ismember == 1 ) {
		$committeestr .= "<td>$committeename</td>\n" ;
		$column++ ;
	}
	elseif ( $isfield == 1  && $ismember == 1 ) {
		$committeestr .= "<td><input type=\"checkbox\" name=\"$fieldname\" id=\"$fieldname\" value=\"1\" checked><font color=\"#FF0000\">$committeename</font></td>\n" ;
		$column++ ;
	}
	elseif ( $isfield == 1  && $ismember == 0 ) {
		$committeestr .= "<td><input type=\"checkbox\" name=\"$fieldname\" id=\"$fieldname\" value=\"1\">$committeename</td>\n" ;
		$column++ ;
	}
	else {
		$committeestr .= "" ;
	}
	$rowend = "" ;
	if ( $column % $endcolumn == 0 && $committeestr != "" ) {
		for ($i = 1; $i <= $guttercol ; $rowend .= "<td></td>\n" , $i++) ;
		$rowend .= "</tr>\n<tr>\n" ;
		for ($i = 1; $i <= $guttercol ; $rowend .= "<td></td>\n" , $i++) ;
	}
	return $committeestr .$rowend  ;
}

function list_memberships( $euid , $tablepre = "euconnect_" ) {
	$list_sql = "SELECT admin , tech, communication , job_fair , website , train , train_descr FROM " . 
		$tablepre . "committees WHERE euid  = " . $euid ; 
	$committeestr .= "<table width=\"100%\" cellspacing=\"5\" align=\"center\">\n" ;
	$committeestr .= "<tr>\n<th></th><th colspan=\"2\" >Committee Memberships</th><th></th>\n</tr>\n<tr>\n<td></td>\n" ;
	$result = mysql_query( $list_sql ) or die( mysql_error() ) ;
	if ( mysql_num_rows( $result ) ) {
		$committee_row = mysql_fetch_assoc( $result ) ;
		$column = 0 ;
		$committeestr .= write_committee( "Admin Committee" , $column , $committee_row[ "admin" ] ) ;
		$committeestr .= write_committee( "Tech Committee" , $column , $committee_row[ "tech" ] ) ;
		$committeestr .= write_committee( "Communication Committee" , $column , $committee_row[ "communication" ] ) ;
		$committeestr .= write_committee( "Reverse Job Fair" , $column , $committee_row[ "job_fair" ] ) ;
		$committeestr .= write_committee( "Website Committee" , $column , $committee_row[ "website" ] ) ;
		$committeestr .= write_committee( "Training Committee: " . $committee_row[ "train_descr" ]  , $column , $committee_row[ "train" ] ) ;
	//	$committeestr .= write_committee( "Resume Workshop" , $column , $committee_row[ "train_resume_elec" ] ) ;
	//	$committeestr .= write_committee( "Interview Workshop" , $column , $committee_row[ "train_interview" ] ) ;
	//	$committeestr .= write_committee( "Networking Workshop" , $column , $committee_row[ "train_network" ] ) ;
		}
	else {
		$committeestr .= "<td></td>\n<td></td>\n" ;
	}
	$committeestr .= "<td></td>\n</tr>\n</table>\n" ;
	return $committeestr ;
}

function calculate_general_meeting_day( $meetingmonth ) {
	// calculates the date of the next general meeting
	while ( date( "w" , $meetingmonth ) != 3 ) :
		//	add 86,400 seconds to the timestamp (the number of seconds in 1 day)
		$meetingmonth += 86400 ;
	endwhile ;
	
	switch ( date( "m" , $meetingmonth ) ) {
		case "11" :
		//	add 1,209,600 seconds to the timestamp (the number of seconds in 1 week)
		$meetingmonth += 604800 ;
			break ;
		case "12" :
		//	add 1,209,600 seconds to the timestamp (the number of seconds in 1 week)
		$meetingmonth += 604800 ;
			break ;
		case "2" :
		//	add no seconds to the timestamp for February
		$meetingmonth += 0 ;
			break ;
		case "3" :
		//	add 1,209,600 seconds to the timestamp (the number of seconds in 1 week)
		$meetingmonth += 604800 ;
			break ;
		default:
		//	add 1,209,600 seconds to the timestamp (the number of seconds in 2 weeks)
		$meetingmonth += 1209600 ;
			break ;
	}
	return $meetingmonth ;
}
function next_meeting_text(  ) {
	$today = mktime( 0 , 0 , 0 ) ;
	$nextmeetingstring = "" ;
	$thismeeting = calculate_general_meeting_day( mktime( 0 , 0 , 0 , date( "m" , $today ) , 1 ) ) ;
	if ( $thismeeting < $today ) {
		$thismeeting = calculate_general_meeting_day( mktime( 0 , 0 , 0 , date( "m" , $today ) + 1 , 1 ) ) ;
	}
	switch ( ( $thismeeting - $today ) / 86400 ) {
		case 0 :
			if ( mktime() - $today < 47000 ) {
				$nextmeetingstring .= "Our general meeting starts at 1 o'clock today, " . date( "F j" , $thismeeting ) . "."  ;
			} else {
				$nextmeetingstring .= "Our general meeting started at 1 o'clock today, " . date( "F j" , $thismeeting ) . "."  ;
			}
			$nextmeetingstring .= "<br />The next general meeting after that is scheduled for " . date( "l, F j, Y" , calculate_general_meeting_day( mktime( 0 , 0 , 0 , date( "m" , $thismeeting ) + 1 , 1 ) ) ) . "." ;
			break ;
		case 1 :
			$nextmeetingstring .= "Our next general meeting is at 1 o'clock tomorrow, " . date( "l, F j" , $thismeeting ) . "." ;
			break ;
		default:
			$nextmeetingstring .= "Our next general meeting is " . strval( round( ( $thismeeting - $today ) / 86400 , 0 ) ) . " days from today, on " . date( "l, F j" , $thismeeting ) . "." ;
			break ;
	}
	return $nextmeetingstring ;
}

function write_member( $record ) {
	global $cat_array, $tablepre  ;
	$euid = $record[ "euid" ] ;
	
?>
<table align="center" class="memberform"  cellpadding="8" width="100%">
<tr>
	<td colspan="2" align="center"><font color="#FF0000"><?= next_meeting_text() ; ?></font><br />
	<a href="generalmeeting_icalendar.php">Click here to calendar the next meeting in Outlook</a></td>
</tr>
<tr>
	<td colspan="2" align="center"><h3><?php
	echo $record[ "firstname" ] . " " . $record[ "lastname" ] ;
	if ( $record[ "active" ] == 0 ){
		echo "<font size=\"-1\" color=\"#FF0000\">INACTIVE</font>" ;
	} ?></h3></td>
</tr>
<tr>
	<td align="right" valign="middle">Username:</td>
	<td valign="middle"><?= $record[ "username" ] ?></td>
</tr>
<tr>
	<td align="right" valign="middle">email:</td>
	<td valign="middle"><?= $record[ "email_home" ] ?><br /></td>
</tr>
<tr>
	<td align="right" valign="middle">Voice:</td>
	<td valign="middle"><?= $record[ "voice_primary" ] ?>
	ext. <?= $record[ "voice_ext" ] ?></td>
</tr>
<tr>
	<td align="right" valign="middle">Cell:</td>
	<td valign="middle"><?= $record[ "voice_mobile" ] ?></td>
</tr>
<tr>
	<td align="right" valign="middle">FAX:</td>
	<td valign="middle"><?  $record[ "fascimile" ] ?></td>
</tr>
<tr>
	<td colspan="2"><?php echo list_memberships( $euid , $tablepre ) ; ?></td>
</tr>
<tr>
	<td colspan="2"><h3>Profiles</h3></td>
</tr>
<?php
	$profile_sql = "SELECT prof_id, euid, priority, cat_id, posit_descr, overview, bullet1, bullet2, bullet3, bullet4, bullet5 FROM euconnect_profiles WHERE euid = $euid ORDER BY priority " ;
	$result = mysql_query( $profile_sql ) or die( mysql_error() ) ;
	if ( mysql_num_rows( $result ) ) {
		while ( $profile = mysql_fetch_assoc( $result ) ) {
			$catlabel_str = "" ;
			switch ( $profile[ "priority"] ) {
				case "1" :
					$catlabel_str = "Primary" ;
					break ;
				case "2" :
					$catlabel_str = "Secondary" ;
					break ;
				case "3" :
					$mon_str = "Tertiary" ;
					break ;
				default :
					$catlabel_str = "Wildcard" ;
					break ;
			}
	 	echo write_profile( $catlabel_str , $profile )   ;
		}
	}
	else { ?>
<tr>
	<td colspan="2"><dt class="profname"><font color="#FF0000">No professional categories selected</font></dt>
		<dd class="profname">(no profiles recorded)</dd>
	</td>
</tr>
<?php
	}
?>	</td>
</tr>
</table>
<?
}



if ( isset( $user_euid ) && $user_euid != "000000" ) {
	// query database for member info
	$sql_memberfetch = "SELECT * FROM " . $tablepre . "members WHERE euid = " . $user_euid ;
	// echo $sql_memberfetch . "<br />" ;
	$result = mysql_query( $sql_memberfetch ) or die( mysql_error() ) ;
	// echo "returned " . mysql_num_rows( $result ) . " record(s)<br />" ;
	if ( mysql_num_rows( $result ) ) {
		$member_row = mysql_fetch_assoc( $result ) ;
		write_member( $member_row ) ;
		}
}



?>
<!-- home_console.inc.php -->
