<?
//any require libraries...include them here
$rootPath = $_SERVER["DOCUMENT_ROOT"];
include($rootPath."/lib/JSON.php");


class RPLfunctions
{
	// RPL DATA-------------
	
	//for laser cutters
	
	public $DATA = array(
		"dynamics" => false, //Turns on or off the site dynamics..i.e. the calendar display and sign-up sheets (true or false)
		"timeslot_table" => "timeslots", //This is the tablename where all the timeslots are stored...
		"admin_prefs_file" => "/admin/txt/prefs.txt", //This is the name and path of admin prefs....
		"webmaster_email" => "cmalcolm@mit.edu", //this is current active webmasters email...for sending bug reports and such too...
		"machines" => array(
			"3DPrint-ABS-1" => array(
				"name" => "ABS Dimension 3D Printer", 
				"id" => "3DPrint-ABS-1"
			),
			"3DPrint-ZCorp-1" => array(
				"name" => "3D Printer - ZCorp", 
				"id" => "3DPrint-ZCorp-1"
			),
			"3DScan-ZScan-1" => array(
				"name" => "ZScan 3d Scanner", 
				"id" => "3DScan-ZScan-1"
			),
			"CNC-Shopbot-1" => array(
				"name" => "Shopbot CNC Router", 
				"id" => "CNC-Shopbot-1"
			),
			"CNC-Techno-1" => array(
				"name" => "[N51] Techno CNC Router", 
				"id" => "CNC-Techno-1"
			),
			"Laser-Epilog-1" => array(
				"name" => "[N51] Epilog Laser Cutter", 
				"id" => "Laser-Epilog-1"
			),
			"Laser-Univ-1" => array(
				"name" => "[Steam] Universal Laser Cutter", 
				"id" => "Laser-Univ-1"
			),
			"Laser-Univ-2" => array(
				"name" => "[Studio 7] Universal Laser Cutter", 
				"id" => "Laser-Univ-2"
			),
			"Waterjet-OMax-1" => array(
				"name" => "[FabLab] OMAX Waterjet", 
				"id" => "Waterjet-OMax-1"
			),
			"Modela-1" => array(
				   "name" => "[FabLab] Modela CNC",
				   "id" => "Modela-1"
			),
			"Vinyl-1" => array(
				   "name" => "[FabLab] Vinyl Cutter",
				   "id" => "Vinyl-1"
			),
		)
	);
	
	
	// SQL DATA-------------
	
	
	private $mysql_server = "sql.mit.edu";
	private $mysql_db = "rpl+db";
	private $mysql_user = "rpl";
	private $mysql_pw = "rpl2011";
	private $db;
	


	 function connect(){
		@$this->db=mysql_connect($this->mysql_server, $this->mysql_user, $this->mysql_pw) or die('<p style="color: red; font-weight: bold">SERVER ERROR: </b>Your database information is incorrect. Please check to make sure everything is correct and try again.</p>');
		@mysql_select_db($this->mysql_db, $this->db) or die('<p style="color: red; font-weight: bold">SERVER ERROR: </b>Unable to connect to specified database. Make sure it is correct and try again.</p>');
		}
		
		//check if a certian table exists in database
		function checkForTables($table){
			$q = "SHOW TABLES LIKE '$table'";
			$result = $this->query($q);
			if (mysql_num_rows($result)!=0)
				return true;
			else
				return false;
		}
		
		
	function getHeaders(){
	print_r($_SERVER);
	}
	
	
		
//validate_email() checks for valid email...returns true on valid email
function validate_email($email){
$email = trim(strip_tags($email)); 

if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) { 
  return true;
} 
else { 
  return false;
} 

}

	//empty_fields() returns false if a value given to it is blank...accepts arrays and non-arrays
	function empty_fields($arr){
		if (is_array($arr)){
			foreach ($arr as $value){
				if (strip_tags(trim($value))==""){
					return false;
				}
			}
		}
		else 
		{
			if (strip_tags(trim($arr))==""){
				return false;
			}
		}
		return true;
	}
	
	//send_email() sends an email to somebody
	function send_email($to, $from, $subject, $message){
	
	$headers = "From: RPLBot <rpl@mit.edu>" . "\r\n" .
	"Reply-To: $from" . "\r\n" .
	    'X-Mailer: PHP/' . phpversion();
	
	if (mail($to, $subject, $message, $headers)){
	return true;
	}
	else{
	return false;
	}
	}
	
	//scans directory and organizes them by timestamp desc
	function scandir_by_mtime($folder) {
		  $dircontent = scandir($folder);
		  $arr = array();
		  foreach($dircontent as $filename) {
		    if ($filename != '.' && $filename != '..') {
		      if (filemtime($folder.$filename) === false) return false;
		      $dat = date("YmdHis", filemtime($folder.$filename));
		      $arr[$dat] = $filename;
		    }
		  }
		  if (!krsort($arr)) return false;
		  return $arr;
	}

	
	//clean up incoming forms and such
	function clean_up($arr){
	if (is_array($arr)){
			foreach ($arr as $key=>$value){
				$arr[$key] = strip_tags(trim(addslashes(stripslashes($value))));
			}
			return $arr;
		}
		else 
		{
			$arr = strip_tags(trim(addslashes(stripslashes($value))));
		}
		return $arr;
	}
	
	//preparing data for form value attributes
	function form_prepare($input){
	return htmlentities(stripslashes($input));
	}
	
	
	//gets announcement if there is one..if not returns false
	function getAnnouncement($myFile){
			
			//read all data from specified apple
			$fh = fopen($myFile, 'r+');
			if (filesize($myFile)>0)
				$announcement = fread($fh, filesize($myFile));
			fclose($fh);
			
			//clean it up
			$announcement = trim(stripslashes(str_replace("\n", "<br>", $announcement)));
			
			//if not blank, return announcement
			if ($announcement!=""){
				return $announcement;
			}
			else
				return false;
	}
	function machine_exists($mach){
		if (trim($mach)=="")
			return false;
		$q =  "SELECT `id` FROM `tools` WHERE `id_name` = '$mach'";
		$result = $this->query($q);
		if ($result){
			$count = mysql_num_rows($result);
			if ($count>0)
				return true;
			else
				return false;
		}
		else{
			return false;
		}

	}
	//gets announcement if there is one..if not returns false
	function getAdminPrefs(){
			
			$rootPath = $_SERVER["DOCUMENT_ROOT"];
			$myFile = $rootPath.$this->DATA["admin_prefs_file"];
			
			//read all data from specified file
			$fh = fopen($myFile, 'r+');
			if (filesize($myFile)>0)
				$prefs_json = fread($fh, filesize($myFile));
			fclose($fh);
			
			//convert to array
			$prefs = (array)json_decode($prefs_json);
			
			return $prefs;
	}
	
	function getWeek($time, $weekCount=0){
		$aDay=60 * 60 * 24;
		$aWeek=60 * 60 * 24 * 7;
		if ($weekCount==0)
			$st = (date("l", $time)=="Sunday") ? strtotime("Today")  + 60 * 60 * 6 : strtotime("Last Sunday") + 60 * 60 * 6;
		else
			$st = $time + ($weekCount*$aWeek);
		$today = date("l");
		$end = $st + 7 * (60 * 60 * 24);
		$week = array( "Sunday"=> $st, 
			"Monday" => $st + $aDay, 
			"Tuesday" => $st + $aDay*2, 
			"Wednesday" => $st + $aDay*3,
			"Thursday" => $st + $aDay*4,
			"Friday" => $st + $aDay*5,
			"Saturday" => $st + $aDay*6
		);
		$now = time() - (40 * 60);
		return array("start" => $st, "timestamp" => $time, "now" => $now, "end" => $end, "week" => $week, "today" => $today, "month" => date('F',$time), "monthday" => date('j'), "date" => date("m/j/y"));
	}
	
	function getDay($time)
	{
		$aDay=60 * 60 * 24;
		$start = strtotime(date("F j, Y", $time));
		$rightnow = strtotime(date("F j, Y", time()));
		$end = $start + $aDay;
		$now = time() - (40 * 60);
		$isToday = ($start==$rightnow) ? 1 : 0;
		return array("start" => $start, "end" => $end, "now" => $now, "istoday" => $isToday);
	}
	
	function getUser()
	{
		
      	//get the certificate information
      	
      	//email
      	$email = ($_SERVER['SSL_CLIENT_S_DN_Email']) ? $this->cleanUp($_SERVER['SSL_CLIENT_S_DN_Email']) : "unknown";
		//user (email without @mit.edu)
		list($user) = explode("@", $email, 1);		
		//name
      	$name = ( $_SERVER['SSL_CLIENT_S_DN_CN']) ? $this->cleanUp( $_SERVER['SSL_CLIENT_S_DN_CN']) : "unknown_name";
      	//issuer
      	$issuer = ($_SERVER['SSL_CLIENT_I_DN_O']) ? $this->cleanUp( $_SERVER['SSL_CLIENT_I_DN_O']) : "unknown_issuer";
      	$admin = 0;
		return array("user"=> $user, "email" => $email, "issuer" => $issuer,  "name" => $name, "admin" => 1);
	}
	
	function getFilename()
	{
			$currentFile = $_SERVER["SCRIPT_NAME"];
			$parts = Explode('/', $currentFile);
    		return $parts[count($parts) - 1];
	}
	
	function add_reservation($mach, $machine, $email, $name, $start, $end, $hours, $description, $timestamp, $reservedTimestamp, $admin){
		$type = $this->getType($mach);
		//format params
		$machine = $this->cleanUp($machine);
		$email = $this->cleanUp($email);
		$name = $this->cleanUp($name);
		$start = $this->cleanUp($start);
		$end = $this->cleanUp($end);
		$description = $this->cleanUp($description);
		$timestamp = $this->cleanUp($timestamp);
		$reservedTimestamp = $this->cleanUp($reservedTimestamp);
		
		//query DB
		
		$qry="INSERT INTO `$type`
		(`machine`, `email`, `name`, `start`, `end`, `hours`, `description`, `timestamp`, `reservedTimestamp`, `admin`) VALUES ('$machine', '$email', '$name', $start, $end,  $hours, '$description', $timestamp, '$reservedTimestamp', $admin)";
		if ($this->query($qry))
			return true;
		else
			return false;
			
	}
	function quickQueueReformat($pmachine, $pnow, $pstart, $pend, $pdescription){
		
		//check to see if user entered description
		if ($this->cleanUp($pdescription)!="")
				$description = $this->cleanUp($pdescription);
			else
				$error = "Please make sure you enter a short description of the job";
		//check if start and end fields are valid timestamps
		if (strtotime($this->cleanUp($pstart)) === false || strtotime($this->cleanUp($pend)) === false){
			$error =  "Please check your entered times. One or both are invalid. Make sure to use the calander picker to ensure valid entries.";
		}
		//if valid, store timestamps in variables
		else
		{
			$start = strtotime($this->cleanUp($pstart));	
			$end = strtotime($this->cleanUp($pend));
			
			//if end is before start, throw error
			if ($start>$end)
				$error = "Your end time is before your start time. Please try entering your desired times again.";	
			else{
				//calculate amount of hours
				$hours = ($end-$start)/(60*60);
				//calculate  start of day for id (this is beginning of the day + 6 hours)
				$startOfDay = strtotime(date("m/j/y", $start)) + (6*60*60);
				//generate slotid
				$slot_id = (date("G", $start)-6<0) ? ($startOfDay-(24*60*60))."-".((date("G", $start)-6)+23) : ($startOfDay)."-".(date("G", $start)-6);
				//slot is beginning of time;
				$slot = $start;
				
			}
				
		}
		
		if ($error){
			return array("error" => $error);
		}
		else{
			return array("success" => "Success", "reformattedData" => array("mach" => $pmachine, "now" => $pnow,  "start" => $start, "end" => $end, "slot" => $slot,"hours"=> $hours, "description"  => $description, "id" =>$slot_id, "slot" => $slot));
		}

	}
	
	function getMachines(){
		$qry = "SELECT * FROM `tools`";
		$result = $this->query($qry);
		$data = array();
		while ($row = mysql_fetch_assoc($result)){
			$data[$row["id_name"]] = $row;
		}
		return $data;
	}
	
	//for daytable, andreservetable.....grabs all reservations that occur within given time...
	
	//*****what we need is to return 3 arrays...one with keyframes, one with tweens, and one with endframes... all in reservedTimestamp (day-x) format....from there , javascript will simply populate the tables..
	
	function grabReservationIds($start, $end, $mach){
		$type = $this->getType($mach);
		
		//first grab all timeslots that this will occur in
		//$q = "SELECT `reservedTimestamp` FROM `$type` WHERE ((`start`>=$start AND `start`<$end) OR  (`end`>$start AND `end`<=$end)) AND `machine`='$mach'"; 
		$q = "SELECT `reservedTimestamp` FROM `$type` WHERE `start`>=$start AND `start`<=$end  AND `machine`='$mach'";
		$result = $this->query($q);
		$data = array();
		if ($result)
		{
			while ($row= mysql_fetch_assoc($result)){
				$data[$row["reservedTimestamp"]] = $row["reservedTimestamp"];
			}
			$newArray = array();
			foreach ($data as $value){
				$aDay = (60 * 60 * 24);
				$newArray[$value] = $this->getReservationDataById($value, $mach);
				
				//this will add the next timeslots in the list if its more then just 1 hour
				if ($newArray[$value]["hours"]>1){
					$time = explode("-", $value);
					$time = $time[0];
					$hour  = explode("-", $value);
					$hour = $hour[1];
					for ($x=1; $x<$newArray[$value]["hours"]; $x++)
					{
						$hour++;
						if ($hour==24){
							$hour=0;
							$time+=$aDay;
						}
						$newArray[$time."-".$hour]= $newArray[$value];
					}
				}
				
			}
			//returns an array of all timeslots
			return $newArray;
		}
		else 
			return false;
	}
	
		function grabReservationIds2_backup($start, $end, $mach){
		
		//we'
		//first create the 4 arrays (keyframes, allinone, tweens, endframes)
		$keyframes = array();
		$tweens = array();
		$endframes = array();
		$allinones = array();
		$nowdata = "";
		$type = $this->getType($mach);
		
		//$q = "SELECT `reservedTimestamp` FROM `$type` WHERE ((`start`>=$start AND `start`<$end) OR  (`end`>$start AND `end`<=$end)) AND `machine`='$mach'"; 
		//$q = "SELECT `reservedTimestamp` FROM `$type` WHERE (`start`<=$start AND `end`>=$end) OR (`end`<$start AND `start`>$end) AND `machine`='$mach'"; 
		$q = "SELECT `reservedTimestamp` FROM `$type` WHERE ((`end`>$start  AND `start`<$end) OR  (`start`>=$start  AND `end`<=$end) OR (`start`<=$start  AND `end`>$start) OR  (`start`<$end  AND `end`>=$end)) AND `machine`='$mach'"; 
		$result = $this->query($q);
		$data = array();
		if ($result)
		{
			while ($row= mysql_fetch_assoc($result)){
				$data[$row["reservedTimestamp"]] = $row["reservedTimestamp"];
			}
			$newArray = array();
			foreach ($data as $value){
				$aDay = (60 * 60 * 24);
				$temp = $this->getReservationDataById($value, $mach);
				//identify user from email
				$emailParts = explode ("@", $temp['email']);
				$temp["user"] = ($temp["admin"]==0) ? $emailParts[0] : "Admin";
				//check to see if this timeslot is now
				if ($temp["start"]<=time() && $temp["end"]>=time())
					$nowdata =  array("user" =>$temp["user"], "admin" =>$temp["admin"], "reservedTimestamp" =>$temp["reservedTimestamp"], "machine" =>$temp["machine"]);
				//this will determime the next timeslots in the list if its more then just 1 hour
				if ($temp["hours"]>1){
					$keyframes[$value] = array("user" =>$temp["user"], "admin" =>$temp["admin"], "reservedTimestamp" =>$temp["reservedTimestamp"], "machine" =>$temp["machine"]);
					$time = explode("-", $value);
					$time = $time[0];
					$hour  = explode("-", $value);
					$hour = $hour[1];
					for ($x=1; $x<$temp["hours"]; $x++)
					{
						$hour++;
						if ($hour==24){
							$hour=0;
							$time+=$aDay;
						}
						//if last timeslot in total reserved time, put in end frame
						if (($temp["hours"]-1)==$x){
							$endframes[$time."-".$hour]=  array("reservedTimestamp" =>$temp["reservedTimestamp"]);
							//store endframe in keyframe
							$keyframes[$endframes[$time."-".$hour]["reservedTimestamp"]]["endFrame"]=$time."-".$hour;
						}
						//otherwise, mark as tween
						else{
							$tweens[$time."-".$hour] = array("reservedTimestamp" =>$temp["reservedTimestamp"]);
						}
					}
				}
				else
				{
					$allinones[$value] = array("user" =>$temp["user"], "admin" => $temp["admin"], "reservedTimestamp" =>$temp["reservedTimestamp"], "machine" =>$temp["machine"]);
					

				}
				
			}
			//returns an array of all timeslots
			return array("keyframes" => $keyframes, "tweens" => $tweens, "now"=>$nowdata, "endframes" => $endframes, "allinones" => $allinones);
		}
		else 
			return false;
	}
		//added on 11/14/2011...this fixes the calendar not showing all reservations
		function fixReservedTimestamp($ts){
		/*<-----MODIFICATION 11/14/2011 -------->*/
		//we get the parsed day in int form
		$ParsedId = intval(substr($ts, 0, strpos($ts, "-")));
		$ParsedHour = intval(substr($ts, strpos($ts, "-")+1));
		//this is the correct reservedtimestamp Id for timestamps!! make sure it starts at 6:00 am
		$actualday = strtotime(date("m/d/y", $ParsedId)." 6:00 am");
		//return the actual timetamp with hour given. HOLLA
		$actualStamp =  $actualday."-".$ParsedHour;
		return $actualStamp;
		/*<----- END MODIFICATION 11/14/2011 -------->*/
		}
		
		function grabReservationIds2($start, $end, $mach){

		//we'
		//first create the 4 arrays (keyframes, allinone, tweens, endframes)
		$keyframes = array();
		$tweens = array();
		$endframes = array();
		$allinones = array();
		$nowdata = "";
		$type = $this->getType($mach);
		
		//$q = "SELECT `reservedTimestamp` FROM `$type` WHERE ((`start`>=$start AND `start`<$end) OR  (`end`>$start AND `end`<=$end)) AND `machine`='$mach'"; 
		//$q = "SELECT `reservedTimestamp` FROM `$type` WHERE (`start`<=$start AND `end`>=$end) OR (`end`<$start AND `start`>$end) AND `machine`='$mach'"; 
		$q = "SELECT `reservedTimestamp` FROM `$type` WHERE ((`end`>$start  AND `start`<$end) OR  (`start`>=$start  AND `end`<=$end) OR (`start`<=$start  AND `end`>$start) OR  (`start`<$end  AND `end`>=$end)) AND `machine`='$mach'"; 
		$result = $this->query($q);
		$data = array();
		if ($result)
		{
			while ($row= mysql_fetch_assoc($result)){
				$data[$row["reservedTimestamp"]] = $row["reservedTimestamp"];
			}
			$newArray = array();
			foreach ($data as $value){
				$aDay = (60 * 60 * 24);
				$temp = $this->getReservationDataById($value, $mach);
				//identify user from email
				$emailParts = explode ("@", $temp['email']);
				$temp["user"] = ($temp["admin"]==0) ? $emailParts[0] : "Admin";
				//check to see if this timeslot is now
				if ($temp["start"]<=time() && $temp["end"]>=time())
					$nowdata =  array("user" =>$temp["user"], "admin" =>$temp["admin"], "reservedTimestamp" =>$temp["reservedTimestamp"], "machine" =>$temp["machine"]);
				//this will determime the next timeslots in the list if its more then just 1 hour
				if ($temp["hours"]>1){
					$keyframes[$this->fixReservedTimestamp($value)] = array("user" =>$temp["user"], "admin" =>$temp["admin"], "reservedTimestamp" =>$value, "machine" =>$temp["machine"]);
					$time = explode("-", $this->fixReservedTimestamp($value));
					$time = $time[0];
					$hour  = explode("-", $this->fixReservedTimestamp($value));
					$hour = $hour[1];
					for ($x=1; $x<$temp["hours"]; $x++)
					{
						$hour++;
						if ($hour==24){
							$hour=0;
							$time+=$aDay;
						}
						//if last timeslot in total reserved time, put in end frame
						if (($temp["hours"]-1)==$x){
							$endframes[$time."-".$hour]=  array("reservedTimestamp" => $value, "actualTimestamp" => $this->fixReservedTimestamp($value));
							//store endframe in keyframe
							$keyframes[$this->fixReservedTimestamp($value)]["endFrame"]=$time."-".$hour;
						}
						//otherwise, mark as tween
						else{
							$tweens[$time."-".$hour] = array("reservedTimestamp" => $value, "actualTimestamp" => $this->fixReservedTimestamp($value));
						}
					}
				}
				else
				{
					$allinones[$this->fixReservedTimestamp($value)] = array("user" =>$temp["user"], "admin" => $temp["admin"], "reservedTimestamp" =>$value,  "actualTimestamp" => $this->fixReservedTimestamp($value), "machine" =>$temp["machine"]);
					

				}
				
			}
			//returns an array of all timeslots
			return array("keyframes" => $keyframes, "tweens" => $tweens, "now"=>$nowdata, "endframes" => $endframes, "allinones" => $allinones);
		}
		else 
			return false;
	}
	//This function is for diplaying the reservation options, when user is choosing a timeslot
	function getReservationData($timestamp, $startHr, $maxHr)
	{
		$day = $timestamp;
		//hmm
		$start = $timestamp + (60 * 60 * ($startHr));
		$dayText = date("D, m/j/y g:i a", $start);
		$startText = date("g:i a", $start);
		$availableSlots = array();
		for ($x=1; $x<=$maxHr; $x++){
			$end = $start+($x*60*60);
			$s = ($x==1) ? "" : "s";
			$endText = date("g:i a", $end);
			$availableSlots[] = array("totalHours"=>$x,"start" => $start, "end" => $end, "text"=> $x." hour".$s.": ".$startText."-".$endText);
		}
		return array("dayat6" => $day,"min" => $timestamp+(($startHr)*60*60), "max" => $timestamp+(($startHr+1)*60*60), "dayText" => $dayText,"start" => $start, "startText" => $startText,  "slots" => $availableSlots);
	}
	
	//this function gets all reservation info by id and machine
	function getReservationDataById($id, $mach)
	{
		$type = $this->getType($mach);
		
		$q = "SELECT * FROM `$type` WHERE `reservedTimestamp` = '$id'  AND `machine`='$mach'";
		$result = $this->query($q);
		if ($result)
		{
			$data = mysql_fetch_array($result);
			//generate end keyframe
			$parts = explode("-", $data["reservedTimestamp"]);
			$aday=24*60*60;
			$data["dayslater_inSeconds"] = $aday*floor($data["hours"]/23);
			$data["endKeyframe"] = ($parts[0]+($aday*floor($data["hours"]/23)))."-".(($parts[1]+($data["hours"]-1))%23);
			$data["reservedTimestamp_id"] = $parts[0];
			return $data;
		}
		else
			return false;
	}
	
	//gets machine id from url filename i.e. file.php
	function getMachIdFromURL($f=""){
		
		//full path of current file
		$currentFile = $_SERVER["SCRIPT_NAME"];
		$originalFilePath = $currentFile;
		//path broken into parts by '/'
		$parts = Explode('/', $currentFile);
		//has filename i.e. 'file.html'
		$currentFile = $parts[count($parts) - 1];
		//if f is not defined, get current file name and use that
		$f = ($f=="") ? $currentFile : $f;
    		
    	//get all machines in database
    	$this->connect();
		$all_machines = $this->getMachines();
		foreach ($all_machines as $mach_data){
		print_r($mach_data);
		echo $f;
			//if url is in array, return that machines id
			if (strpos( $mach_data["url"], $f)){
				return $mach_data["id_name"];
			}
		}
		return false;
	}	
	
	//deprecated...see the getMachIdFromURL($f) function
	function getMachineInfo($id=false)
	{
		return false;
	}
	
	//gets machine info by id
	function getMachineInfo2($id, $data="*")
	{
		$whatToGet = ($data=="*") ? "*" : "`$data`";
		
		$q =  "SELECT $whatToGet FROM `tools` WHERE `id_name` = '$id'";
		$result = $this->query($q);
		if ($result){
			$stuff = mysql_fetch_array($result);
			if ($data=="*")
				return array("success" => "Success", "data" => $stuff);
			else
				return $stuff[$data];
		}
		else
		{
			return array("error" => "Could not Grab Machine Info");
		}
	}
	
	public function query($q)
	{
		$result= mysql_query($q, $this->db) or die ("<div style='clear:both' class='error' >SERVER ERROR: ".mysql_error()."</div>");
		if ($result)
			return $result;
		else
			return false;
	}
	function admin_deleteReservation($id, $mach, $alert=0){
	//check if it exists
		$type = $this->getType($mach);
		$RESERVATION_DATA = $this->getReservationDataById($id, $mach);
		if (!$RESERVATION_DATA){
			$error = "This timeslot reservation doesn't exist";
		}
		else{
			if ($error)
				return array("error" => $error);
			else{			
				$q = "DELETE FROM `$type` WHERE `reservedTimestamp`= '$id' AND `machine`='$mach'";
				$result = $this->query($q);
				if ($result){
					//if alert is true, send message out to students of open spot
					//sendEmail($to, $from, $email)
						
					return array("success" => "You have successfully cancelled this timeslot as Administrator.");
				}
				else{
					return array("error" => "Database Error: Could not delete from database as Administrator");
				}
			
			}
			
		}
		return array("error" => $error);	
	}
	
	
	function delete_reservation($id, $mach, $alert=0){

		//check if it exists
		$type = $this->getType($mach);
		$RESERVATION_DATA = $this->getReservationDataById($id, $mach);
		if (!$RESERVATION_DATA){
			$error = "This timeslot reservation doesn't exist";
		}
		else{
		
			$now = time() - (5 * 60); //5 min grace period
			//check to see if reservation has already passed
			if ($now>$RESERVATION_DATA["start"])
				$error = "This reservation has already began or passed.";
			
			//verify certs
			if (!$this->verify_cert())
				$error = "No MIT certificate found. Please make sure you have MIT certificates.";
			
			//check to see if user is creator
			$user = $this->getUser();
			if ($RESERVATION_DATA["email"] != $user["email"] )
				$error = "You do not have permissions to delete this reservation. It was created by another user.";
			//return error or success
			if ($error)
				return array("error" => $error);
			else{			
				$q = "DELETE FROM `$type` WHERE `email`='".$user["email"]."' AND `reservedTimestamp`= '$id' AND `machine`='$mach'";
				$result = $this->query($q);
				if ($result){
					//if alert is true, send message out to students of open spot
					//sendEmail($to, $from, $email)
						
					return array("success" => "You have successfully cancelled this timeslot.");
				}
				else{
					return array("error" => "Database Error: Could not delete from database");
				}
			
			}
			
		}
		return array("error" => $error);	
	}
	
	function sendEmail($to, $from, $email){
	
	}
	
	function verify_cert(){
		return true;
	}
	
	function verify_onList(){
		return true;
	}
	
	function getType($id){
			//all timeslots are currently stored in same table...so just return that table name
			return $this->DATA["timeslot_table"];
			
	}
	
	
	function validateAdmin($machine, $now, $slot, $hours, $description, $id, $confirm=0){
		
		//definitions
		$aHour = (60*60);
				
		$mach = $machine;
		$max_week=0;
		$max_hours=0;
		$max_hours_day=0;
		
		$admin=1;
		
		//get user info
		$user_data = $this->getUser();
		$email = $user_data['email'];
		$name = $user_data['name'];
		
		$slot_start = $slot;
		$slot_end = $slot+($aHour*$hours);
		
		//check if start and end are in same day/ same week
		$sameCheck = $this->compare($slot_start, $slot_end);
		
		$type = $this->getType($machine);
		
		//grab machine info..(looks it up by id)
		$dataFromFunc = $this->getMachineInfo2($machine);
	
		//machine info has all information about the machine
		$MACHINE_INFO = $dataFromFunc["data"];
		
		$max_week = $MACHINE_INFO["reserve_weeks_ahead"];
		$max_hours = $MACHINE_INFO["max_hours"];
		$max_hours_day = $MACHINE_INFO["max_hours_day"];
				
		
		
		if (!$error)
		{
			//check if user has certificates
			if (!$this->verify_cert())
				$error = "No MIT certificate found. Please make sure you have MIT certificates.";
			
			//check if user is on cutting list
			if (!$this->verify_onList())
				$error = "You are not on the cutters list. You need to be approved by RPL before being able to use the machines. Please contact us if you believe this is a mistake.";
			
			if (!$error)
			{
				
				//check to make sure user entered a description
				if ($this->cleanUp($description)!="")
					$description = $this->cleanUp($description);
				else
					$error = "Please make sure you enter a short description of the job";
				
			
				//check if within available Reservation Times
				$thisWeek = $this->getWeek(time());
				$FarthestWeek = $this->getWeek($thisWeek["start"], $max_week);
				//allow user to reserve spots 40 minutes before current time
				$minTimestamp = time() - (40 * 60);
				$maxTimestamp = $FarthestWeek["end"];
				
				//check if user is within reservation timeframe
				if ($slot_start<$minTimestamp)
				{
					$error = "The timeslot you have chosen has already passed.";
				}

	
				//check if slot is already taken by another user. conflict in times.
				$conflict = $this->checkConflict($mach, $slot_start, $slot_end);
				//confirm = 0 means admin has yet to see the conflicts
				if ($confirm==0){
				if ($conflict){
					//if there is a conflict, print a warning to admin
					if ($conflict["conflicts"] > 0){
						$warning = "There is a time conflict with ".$conflict["conflicts"]." user(s). Are you Sure you want to overwrite their Reservations?";
						$warningInfo =  $conflict["data"];
					}
				}
				else
					$error = "Database Error: Could not check for time conflicts.";
				}
				else
				{
					//if admin confirms overwrite, write over all conflicting reservations
					$allConflicts = $conflict["data"];
					foreach($allConflicts as $value){
						$result = $this->admin_deleteReservation($value["reservedTimestamp"], $value["machine"]);
						if ($result["error"])
							$error = $result["error"];
					}
				}
			}
		}
		
		//if any errors, return them
		if ($error)
			return array("error" => $error, "errorInfo" => $errorInfo);
		//if a warning return warning
		else if ($warning)
		{
			return array("warning" => $warning, "warningInfo" => $warningInfo, "params" => array("mach_id" => $machine, "now"=> $now, "slot"=> $slot, "hours" => $hours, "description" => $description, "id" => $id, "confirm" => 1));
		}
		//else return success
		else{
				if ($this->add_reservation($mach, $machine, $email, $name, $slot_start, $slot_end, $hours,  $description, time(), $id, 1)){
					$startText = date("D, m/j/y g:i a", $slot_start);
					$endText = date("D, m/j/y g:i a", $slot_end);
					return array("success" => "Slot Succesfully Reserved.", "slot_time" => "$startText - $endText") ;
				}else{
					return array("error" => "Database Error: Could not add timeslot to database.");
				}
		}
		
	}
	
	
	function validate($machine, $now, $slot, $hours, $description){
		
		//definitions
		$aHour = (60*60);
				
		$mach = $machine;
		$max_week=0;
		$max_hours=0;
		$max_hours_day=0;
		
		//get user info
		$user_data = $this->getUser();
		$email = $user_data['email'];
		$name = $user_data['name'];
		
		$slot_start = $slot;
		$slot_end = $slot+($aHour*$hours);
		
		//check if start and end are in same day/ same week
		$sameCheck = $this->compare($slot_start, $slot_end);
		
		$type = $this->getType($machine);
		
		//grab machine info..(looks it up by id)
		$dataFromFunc = $this->getMachineInfo2($machine);
	
		//machine info has all information about the machine
		$MACHINE_INFO = $dataFromFunc["data"];
		
		//get parameters
		$max_week = $MACHINE_INFO["reserve_weeks_ahead"];
		$max_hours = $MACHINE_INFO["max_hours"];
		$max_hours_day = $MACHINE_INFO["max_hours_day"];
		
		
		if (!$error)
		{
			//check if user has certificates
			if (!$this->verify_cert())
				$error = "No MIT certificate found. Please make sure you have MIT certificates.";
			
			//check if user is on cutting list
			if (!$this->verify_onList())
				$error = "You are not on the cutters list. You need to be approved by RPL before being able to use the machines. Please contact us if you believe this is a mistake.";
			
			if (!$error)
			{
				
				//check to make sure user entered a description
				if ($this->cleanUp($description)!="")
					$description = $this->cleanUp($description);
				else
					$error = "Please make sure you enter a short description of the job";
				
			
				//check if within available Reservation Times
				$thisWeek = $this->getWeek(time());
				$FarthestWeek = $this->getWeek($thisWeek["start"], $max_week);
				//allow user to reserve spots 40 minutes before current time
				$minTimestamp = time() - (40 * 60);
				$maxTimestamp = $FarthestWeek["end"];
				
				//check if user is within reservation timeframe
				if ($slot_start<$minTimestamp)
				{
					$error = "The timeslot you have chosen has already passed.";
				}
				
				if ($slot_end>$maxTimestamp)
				{
					$error = "The timeslot you have chosen is too far in advance to reserve.";
				}
				
				//check if user exceeds max amount of hours for timeslot
				if ($hours>$max_hours)
					$error = "Your timeslot exceeds the maximum amount of hours.";
				
				//check if user exceeds max amount of hours for the week(s) chosen.
				$userHours = $this->getUserHours($email, $mach, "week", $slot_start);				if ($userHours){
					if (($userHours["hours"]+$hours)>$max_hours){
						$error = "You have exceeded the max number of hours for the week (".date("m/j/y", $userHours["start"])." - ".date("m/j/y", $userHours["end"]).")";
						$errorInfo ="Current Hours/week: <b>".$userHours["hours"]."</b>";
						$errorInfo .="<br>This Timeslot: <b>+$hours</b>";
						$errorInfo .="<br>Max Hours/week: <b>$max_hours</b>";
					}
				}else{
					$error = "Database Error: Could not check total user hours for this week";
				}
				

				//if end of slot passes the week it began with, check that one too.
				if ($same["sameweek"]==false){	
					$userHours2 = $this->getUserHours($email, $mach, "week", $slot_end);
					if ($userHours2){	
						if (($userHours2["hours"]+$hours)>$max_hours){
							$error = "You have exceeded the max number of hours for the week (".date("m/j/y", $userHours["start"])." - ".date("m/j/y", $userHours["end"]).")";
						$errorInfo ="Current Hours/week: <b>".$userHours["hours"]."</b>";
						$errorInfo .="<br>This Timeslot: <b>+$hours</b>";
						$errorInfo .="<br>Max Hours/week: <b>$max_hours</b>";
						}
					}else{
						$error = "Database Error: Could not check total user hours for this week";
					}
				}
				
				//check if use exceed max amount of hours for the day chosen
				$userHours = $this->getUserHours($email, $mach, "day", $slot_start);
				if ($userHours){
					if (($userHours["hours"]+$hours)>$max_hours_day){
						$error = "You have exceeded the max number of hours for the day (".date("m/j/y", $userHours["start"]).").";
						$errorInfo = "Current Hours: <b>".$userHours["hours"]."</b>";
						$errorInfo .="<br>This Timeslot: <b>+$hours</b>";
						$errorInfo .="<br>Max Hours: <b>$max_hours_day</b>";
						}
				}else{
					$error = "Database Error: Could not check total user hours for this day.";
				}
				
				//if end of slot passes the day it began with, check that one too.	
				if ($same["sameweek"]==false){
					$userHours2 = $this->getUserHours($email, $mach, "day", $slot_end);	
					if ($userHours2){
						if (($userHours2["hours"]+$hours)>$max_hours_day){
							$error = "You have exceeded the max number of hours for the day (".date("m/j/y", $userHours["start"]).")";
							$errorInfo = "Current Hours: <b>".$userHours["hours"]."</b>";
							$errorInfo .="<br>This Timeslot: <b>+$hours</b>";
							$errorInfo .="<br>Max Hours: <b>$max_hours_day</b>";
						}
					}else{
						$error = "Database Error: Could not check total user hours for this day.";
					}
				}
				
				
				//check if slot is already taken by another user. conflict in times.
				$conflict = $this->checkConflict($mach, $slot_start, $slot_end);
				if ($conflict){
					if ($conflict["conflicts"] > 0){
						$error = "There is a time conflict with another user. Please check your times and try again";
						$error .= "<ul class='error'>";
						foreach ($conflict["data"] as $c){
							$error .= "<li>"."<u>".$c['name']."</u>: ".date("D, m/j/y g:i a", $c['start'])." to ".date("D, m/j/y g:i a", $c['end'])."</li>";
						}
						$error .= "</ul>";
					}
				}
				else
					$error = "Database Error: Could not check for time conflicts.";
				
			}
		}
		if ($error)
			return array("error" => $error, "errorInfo" => $errorInfo);
		else{
				if ($this->add_reservation($mach, $machine, $email, $name, $slot_start, $slot_end, $hours,  $description, time(), $_GET['id'], 0)){
					$startText = date("D, m/j/y g:i a", $slot_start);
					$endText = date("D, m/j/y g:i a", $slot_end);
					return array("success" => "Slot Succesfully Reserved.", "slot_time" => "$startText - $endText") ;
				}else{
					return array("error" => "Database Error: Could not add timeslot to database.");
				}
		}
		
	}
	function getcurrentReservedTimeslot($email){
	$type = $this->getType($mach);
			$start = strtotime(date("m/j/y g:00 a"));
			$end = $start+(60*60);
			if ($email!="admin")
				$q = "SELECT * FROM `$type` WHERE ((`end`>$start  AND `start`<$end) OR  (`start`>=$start  AND `end`<=$end) OR (`start`<=$start  AND `end`>$start) OR  (`start`<$end  AND `end`>=$end)) AND `email`='$email' AND `admin`!=1"; 
			else
				$q = "SELECT * FROM `$type` WHERE ((`end`>$start  AND `start`<$end) OR  (`start`>=$start  AND `end`<=$end) OR (`start`<=$start  AND `end`>$start) OR  (`start`<$end  AND `end`>=$end)) AND `admin`=1"; 
		$result = $this->query($q);
		if ($result){
			$data = array();
			while ($row = mysql_fetch_assoc($result)){
				$data = $row;
			}
			return $data;
			}
		else
			return false;
	}
	
	
	function getReservationsByUser($email, $admin, $howFarBack=0)
	{
		$minTimestamp = ($howFarBack==0) ? time() - 24  : (time() - $howFarBack);
		$currentTimeslot = ($admin==1) ? $this->getcurrentReservedTimeslot("admin") : $this->getcurrentReservedTimeslot($email);
		$type = $this->getType($mach);
		if ($admin==0){
		$q = "SELECT * FROM `$type` WHERE `email`='$email' AND `start`>'$minTimestamp' AND `admin`=0 ORDER BY `start` ASC ";
		}
		else if ($admin==1){
			$q = "SELECT * FROM `$type` WHERE `admin`=1  AND `start`>'$minTimestamp' ORDER BY `start` ASC ";
		}
		$result = $this->query($q);
		if ($result){
			$data = array();
			while ($row = mysql_fetch_assoc($result)){
				$data[$row["start"]] = $row;
			}
			if ($currentTimeslot)
				$data[$currentTimeslot["start"]] = $currentTimeslot;
			return $data;
		}
		else
			return false;
	}

	function checkConflict($mach, $start, $end)
	{
		$type = $this->getType($mach);
		//$q = "SELECT * FROM `$type` WHERE ((`start`>=$start AND `start`<$end) OR  (`end`>$start AND `end`<=$end)) AND `machine`='$mach'"; 
			$q = "SELECT * FROM `$type` WHERE ((`end`>$start  AND `start`<$end) OR  (`start`>=$start  AND `end`<=$end) OR (`start`<=$start  AND `end`>$start) OR  (`start`<$end  AND `end`>=$end)) AND `machine`='$mach'"; 
		$result = $this->query($q);
		if ($result){
			$data = array();
			while ($row = mysql_fetch_assoc($result))
				$data[] = $row;
			return array("data" => $data, "conflicts" => mysql_num_rows($result));
		}
		else 
			return false;			
	}
	
	function compare($t1, $t2){
		
		if (date("m/j/y", $t1) == date("m/j/y", $t2) )
			$sameDay = true;
		else
			$sameDay = false;
		
		$week1 = $this->getWeek($t1, $_GET['w']);
		$week2 = $this->getWeek($t2, $_GET['w']);
		
		if ($week1["start"] = $week2["start"])
			$sameWeek = true;
		else
			$sameWeek = false;
		
		return array("sameweek" => $sameWeek, "sameday" => $sameDay);
	}
	
	function getUserHours($user, $mach, $mode,  $time, $fromNow = 0)
	{
		$type = $this->getType($mach);
		switch($mode)
		{
			case "week":
				$span = $this->getWeek(time(), $_GET['w']);
				break;
			default:
			case "day":
				$span = $this->getDay($time);
				break;
		}
		$start = $span["start"];
		$end = $span["end"];
		if ($fromNow)
			$start = $span["now"];
			
		$q = "SELECT * FROM `$type` WHERE `start`>=$start AND `end`<=$end AND `email`='$user'  AND `machine`='$mach' AND `admin`!=1";
		$result = $this->query($q);
		$data = array();
		if ($result){
			$totalHours = 0;
			while ($row = mysql_fetch_assoc($result))
			{
				$data[] = $row;
				$totalHours += $row["hours"];
				
			}
			return array("data" => $data, "hours" => $totalHours,  "start" => $start ,"end"=> $end );
		}
		else 
			return false;
	}
	
	function getWeekSpan($max_week){
		//grab a week span
		$thisWeek = $this->getWeek(time());
		$FarthestWeek = $this->getWeek($thisWeek["start"], $max_week);
		$now = time() - (40 * 60);
		return array("start" => $thisWeek["start"], "now" => $now	,"end" => $FarthestWeek["end"]);
	}
	
	function cleanUp($str){
	$str = trim(addslashes(strip_tags($str)));
	return $str;
	}
	
	function in_multiarray($elem, $array) 
    { 
        // if the $array is an array or is an object 
         if( is_array( $array ) || is_object( $array ) ) 
         { 
             // if $elem is in $array object 
             if( is_object( $array ) ) 
             { 
                 $temp_array = get_object_vars( $array ); 
                 if( in_array( $elem, $temp_array ) ) 
                     return TRUE; 
             } 
             
             // if $elem is in $array return true 
             if( is_array( $array ) && in_array( $elem, $array ) ) 
                 return TRUE; 
                 
             
             // if $elem isn't in $array, then check foreach element 
             foreach( $array as $array_element ) 
             { 
                 // if $array_element is an array or is an object call the in_multiarray function to this element 
                 // if in_multiarray returns TRUE, than return is in array, else check next element 
                 if( ( is_array( $array_element ) || is_object( $array_element ) ) && $this->in_multiarray( $elem, $array_element ) ) 
                 { 
                     return TRUE; 
                     exit; 
                 } 
             } 
         } 
         
         // if isn't in array return FALSE 
         return FALSE; 
    } 
}
?>