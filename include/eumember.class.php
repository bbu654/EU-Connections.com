<?php 
class eumember {
    // define properties
    public $euid ;
    public $auth ;
    public $active ;
    public $enrolled ;
    public $jumpstart ;
    public $userlevel ;
    public $updated ;
    public $final4 ;
    public $username ;
    public $lastname ;
    public $firstname ;
    public $email_home ;
    public $voice_primary ;
    public $voice_ext ;
    public $voice_mobile ;
    public $fascimile ;
    public $memberships ;
    private $pass ;
    // constructor
    public function __construct() {
        $this->euid = 0 ;
        $this->active = 0 ;
        $this->enrolled = getdate ( time() ) ;
        $this->jumpstart = getdate ( time() ) ;
        $this->userlevel = 0 ;
        $this->updated = getdate ( time() ) ;
        $this->final4 = "0000" ;
        $this->username = "guest" ;
        $this->lastname = "" ;
        $this->firstname = "" ;
        $this->email_home = "" ;
        $this->voice_primary = "" ;
        $this->voice_ext = "" ;
        $this->voice_mobile = "" ;
        $this->fascimile = "" ;
        $this->membership = array( 1 => "General Membership" );
    }

    // define methods
    public function setpassword( $pw ) {
        $pass = $pw ;
    }
    public function resetpassword( $dbhost , $tableprefix , $dbhost , $dbuser , $dbpw ) {
    	mysql_connect( $dbhost, $dbuser, $dbpw ) or die( mysql_error() );
    	mysql_select_db( $dbname ) or die(mysql_error() ) ;
      $update_sql = "UPDATE " . $tablepre . "members SET " ;
      $update_sql .= "\n pass = SHA1( username ) "  ;
      $update_sql .= "\n WHERE euid = " . $this->euid . " ;" ;
      //	echo $update_sql ;
      $result = mysql_query( $update_sql ) or die( mysql_error() ) ;
    }
    public function changepassword( $oldpassword , $newpassword , $dbhost , $tableprefix , $dbhost , $dbuser , $dbpw ) {
    	mysql_connect( $dbhost, $dbuser, $dbpw ) or die( mysql_error() );
    	mysql_select_db( $dbname ) or die(mysql_error() ) ;
      $update_sql = "UPDATE " . $tablepre . "members SET " ;
      $update_sql .= "\n pass = SHA1( \"" . $newpassword . "\" ) "  ;
      $update_sql .= "\n WHERE euid = " . $this->euid ;
      $update_sql .= "\n AND pass = SHA1(\"" . $oldpassword . "\") ;" ;
      //	echo $update_sql ;
      $result = mysql_query( $update_sql ) or die( mysql_error() ) ;
      $pass = $newpassword ;
    }
    

    function authenticate( $dbhost , $tableprefix , $dbhost , $dbuser , $dbpw )
    {
    	mysql_connect( $dbhost, $dbuser, $dbpw ) or die( mysql_error() );
    	mysql_select_db( $dbname ) or die(mysql_error());
    	$sql_checkpass = "SELECT euid , active , enrolled , jumpstart , userlevel , updated , final4 , username , lastname , firstname , email_home , voice_primary , voice_ext , voice_mobile , fascimile from " . $tableprefix . "members WHERE username = \"$this->username\" AND pass = SHA1(\"$pass\")";
    	// $sql_checkpass = $sql_checkuname ;	// dummy check for username only (remove after debugging)
    	$result = mysql_query( $sql_checkpass ) or die( mysql_error() ) ;
    	// if row exists -> user/pass combination is correct
    	if ( mysql_num_rows( $result ) == 1 ) {
    		$user_row = mysql_fetch_assoc( $result ) ;
    		$this->euid = $user_row[ "euid" ] ;
        $this->active = $user_row[ "active" ] ;
        $this->enrolled = new DateTime( $user_row[ "enrolled" ] ) ;
        $this->jumpstart = new DateTime( $user_row[ "jumpstart" ] ) ;
        $this->userlevel = $user_row[ "userlevel" ] ;
        $this->updated = new DateTime( $user_row[ "updated" ] ) ;
        $this->final4 = $user_row[ "final4" ] ;
        $this->username = $user_row[ "username" ] ;
        $this->lastname = $user_row[ "lastname" ] ;
        $this->firstname = $user_row[ "firstname" ] ;
        $this->email_home = $user_row[ "email_home" ] ;
        $this->voice_primary = $user_row[ "voice_primary" ] ;
        $this->voice_ext = $user_row[ "voice_ext" ] ;
        $this->voice_mobile = $user_row[ "voice_mobile" ] ;
        $this->fascimile = $user_row[ "fascimile" ] ;
    		return TRUE ;
    	}
    	else {
    		return FALSE ;
    	}
    }

    function save( $dbhost , $tableprefix , $dbhost , $dbuser , $dbpw )
    {
    	if ( 0 == $this->euid ) {
    			mysql_connect( $dbhost, $dbuser, $dbpw ) or die( mysql_error() ) ;
    			mysql_select_db( $dbname ) or die( mysql_error() ) ;
      		$insert_sql = "INSERT INTO " . $tablepre . "members " ;
      		$insert_sql .= "( active , enrolled , jumpstart , userlevel , username , pass , final4 , lastname , firstname " ;
      		$insert_sql .= ", email_home , voice_primary , voice_ext , voice_mobile , fascimile " ;
      		$insert_sql .= ") VALUES ( " ;
      		$insert_sql .= " \"" . $this->active ;
      		$insert_sql .= " , \"" . $this->enrolled->format( "Y-m-d" ) . "\" "  ;
      		$insert_sql .= " , \"" . $this->jumpstart->format( "Y-m-d" ) . "\" "  ;
      		$insert_sql .= " , \"" . $this->userlevel ;
      		$insert_sql .= " , \"" . $this->username . "\" "  ;
      		$insert_sql .= " , SHA1( \"" . $this->username . "\" ) "  ;
      		$insert_sql .= " , \"" . $this->final4 . "\" "  ;
      		$insert_sql .= " , \"" . $this->lastname . "\" "  ;
      		$insert_sql .= " , \"" . $this->firstname . "\" "  ;
      		$insert_sql .= " , \"" . strtolower( $this->email_home ) . "\" "  ;
      		$insert_sql .= " , \"" . $this->voice_primary . "\" "  ;
      		$insert_sql .= " , \"" . $this->voice_ext . "\" " ;
      		$insert_sql .= " , \"" . $this->voice_mobile . "\" " ;
      		$insert_sql .= ", \"" . $this->fascimile . "\" ) ; " ;
      		//	echo $insert_sql ;
      		$result = mysql_query( $insert_sql ) or die( mysql_error() . " at member insertion"  ) ;
      		$this->euid = mysql_insert_id() ;
    	}
    	else {
    			mysql_connect( $dbhost, $dbuser, $dbpw ) or die( mysql_error() ) ;
    			mysql_select_db( $dbname ) or die( mysql_error() ) ;
      		$update_sql = "UPDATE " . $tablepre . "members SET " ;
        	$update_sql .= "\n active = " . $this->active ;
        	$update_sql .= " ,\n enrolled = \"" . $this->enrolled->format( "Y-m-d" ) . "\""  ;
        	$update_sql .= " ,\n jumpstart = \"" . $this->jumpstart->format( "Y-m-d" ) . "\""  ;
        	$update_sql .= " ,\n userlevel = " . $this->userlevel ;
        	$update_sql .= " ,\n final4= \"" . $this->final4 . "\""  ;
        	$update_sql .= " ,\n username= \"" . $this->username . "\""  ;
        	$update_sql .= " ,\n firstname = \"" . $this->firstname . "\""  ;
        	$update_sql .= " ,\n lastname = \"" . $this->lastname . "\""  ;
        	$update_sql .= " ,\n email_home = \"" . strtolower( $this->email_home ) . "\""  ;
        	$update_sql .= " ,\n voice_primary = \"" . $this->voice_primary . "\""  ;
        	$update_sql .= " ,\n voice_ext = \"" . $this->voice_ext . "\" " ;
        	$update_sql .= " , \nvoice_mobile = \"" . $this->voice_mobile . "\"" ;
        	$update_sql .= " ,\n fascimile = \"" . $this->fascimile . "\" " ;
        	$update_sql .= "\n WHERE euid = " . $this->euid . " ;\n" ;
      		//	echo $update_sql ;
      		$result = mysql_query( $update_sql ) or die( mysql_error() . " at member insertion"  ) ;
    		}
    }


}

?>