<?php 
class eumeeting {
    // define properties
    public $meeting_id ;
    public $group ;
    public $start_time ;
    public $duration ;
    public $address ;
    public $room ;
    public $comments ;
    public $creator ;
    public $poc ;
    
    // constructor
    public function __construct() {
        $this->meeting_id = 0 ;
        $this->group = new eugroup() ;
        $this->group->group_id = 1 ;
        $this->group->group_name = "General Membership" ;
        $this->start_time = getdate( time() ) ;
        $this->duration = 1 ;
        $this->address = '' ;
        $this->room = '' ;
        $this->comments = '' ;
        $this->creator = new eumember() ;
        $this->poc = new eumember() ;
    }

    // define methods
    function setdateODBC( $datestring )
    {
    	$date_array = explode( " " , $datestring ) ;
      $day_array = explode( "-" , $date_array[ 0 ] ) ;
      $time_array = explode( ":" , $date_array[ 1 ] ) ;
      $daystring = $day_array[ 1 ] . "/" . $day_array[ 2 ] . "/" . $day_array[ 0 ] ;
      $meetingtime = strtotime( $daystring . " " . $date_array[ 1 ] ) ;
      $this->start_time = getdate( $meetingtime ) ;
/*      print_r( $datestring ) ;
      print_r( $day_array ) ;
      print_r( $time_array ) ;
      print_r( $daystring ) ;
      print_r( $daystring . " " . $date_array[ 1 ] ) ;
      print_r( $this->start_time ) ;
*/    }
   

    function save( $dbhost , $tableprefix , $dbhost , $dbuser , $dbpw )
    {
    	if ( 0 == $this->meeting_id ) {
    			mysql_connect( $dbhost, $dbuser, $dbpw ) or die( mysql_error() ) ;
    			mysql_select_db( $dbname ) or die( mysql_error() ) ;
      		$insert_sql = "INSERT INTO " . $tablepre . "meetings " ;
      		$insert_sql .= "( group_id , start_time , duration , address , room , comments " ;
      		$insert_sql .= ") VALUES ( " ;
      		$insert_sql .= " " . $this->group_id ;
      		$insert_sql .= " , \"" . $this->start_time->format( "Y-m-d" ) . "\" "  ;
      		$insert_sql .= " , " . $this->duration ;
      		$insert_sql .= " , \"" . $this->address . "\" "  ;
      		$insert_sql .= " , \"" . $this->room . "\" "  ;
      		$insert_sql .= " , \"" . $this->comments . "\" ) ; " ;
      		//	echo $insert_sql ;
      		$result = mysql_query( $insert_sql ) or die( mysql_error() . " at member insertion"  ) ;
      		$this->meeting_id = mysql_insert_id() ;
    	}
    	else {
    			mysql_connect( $dbhost, $dbuser, $dbpw ) or die( mysql_error() ) ;
    			mysql_select_db( $dbname ) or die( mysql_error() ) ;
      		$update_sql = "UPDATE " . $tablepre . "members SET " ;
        	$update_sql .= "\n group_id = " . $this->group_id ;
        	$update_sql .= " ,\n start_time = \"" . $this->start_time->format( "Y-m-d H:i" ) . "\""  ;
        	$update_sql .= "\n duration = " . $this->duration ;
        	$update_sql .= " ,\n address = \"" . $this->address . "\""  ;
        	$update_sql .= " ,\n room = \"" . $this->room . "\""  ;
        	$update_sql .= " ,\n comments = \"" . $this->comments . "\" " ;
        	$update_sql .= "\n WHERE meeting_id = " . $this->meeting_id . " ;\n" ;
      		//	echo $update_sql ;
      		$result = mysql_query( $update_sql ) or die( mysql_error() . " at member insertion"  ) ;
    		}
    }
}

?>