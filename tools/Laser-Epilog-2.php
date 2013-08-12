<?
//machine id...as defined in database...(required)
$MACH_ID = "Laser-Epilog-2";

//<---------NORMAL PEOPLE....DO NOT EDIT PHP DATA PAST THIS LINE ------------------------>


//dependencies 
include("../functions.php");
include("lib/calendar.php");
$rpl = new RPLfunctions();

//<---start of dyanamic preDATA ---->

//connect;
$rpl->connect();

//get admin prefs
$ADMIN_PREFS = $rpl->getAdminPrefs();

//if dynamics are turned on
if ($ADMIN_PREFS["dynamics"]=="on" && $rpl->machine_exists($MACH_ID)){

	
	//grab machine info..(looks it up by id)
	$data = $rpl->getMachineInfo2($MACH_ID);
	
	//machine info has all information about the machine
	$MACHINE_INFO = $data["data"];
	
	//set up times for this months and next months calander
	$time = time();
	$nextMonth = strtotime("+1 months");
	
	//handle this week on calendar
	$week = $rpl->getWeek($time);
	
	//find the amount of time students are allowed to reserve ahead
	$ALLOWED_WEEKS_AHEAD = $MACHINE_INFO["reserve_weeks_ahead"];
	
	$other_weeks = array();
	//get weeks ahead
	for ($x=0; $x<$ALLOWED_WEEKS_AHEAD; $x++){
		$other_weeks[$x] = $rpl->getWeek($week["start"], $x+1);
	}
	
	if  ($_GET['w']>0 && $_GET['w']<=$ALLOWED_WEEKS_AHEAD )
		$selWeek = $other_weeks[$_GET['w']-1];
	else
	{
		$selWeek = $week;
		$thisWeek = 1;
	}
	
	//mark this week and current day on calendar
	foreach ($selWeek["week"] as $key=>$value)
	{
		$monthday = date('j', $value);
			$days[$selWeek["monthday"]] = array(NULL,NULL,'<span class="monthday_today">'.$selWeek["monthday"].'</span>');
		 if (date('F', $value) == $selWeek["month"])
			$days[$monthday] = array(NULL,NULL,'<span class="monthday_week">'.$monthday.'</span>');
		else if (date('F', $value) != $selWeek["month"])
			$days2[$monthday] = array(NULL,NULL,'<span class="monthday_week">'.$monthday.'</span>');
			
	}
	
	
	//store arrays
	$reserves2 = $rpl->grabReservationIds2($selWeek["start"] - (60 * 60 * 24), $selWeek["end"], $MACHINE_INFO["id_name"]);
}

//<---end of dyanamic preDATA ---->
?>


<script>
    $(document).ready(function(){ 
        
        putTimeslotsIn();
        
        <?
        //<--------- scripts to populate calandar table --------------->//
        //if dynamics is turned on
            if ($ADMIN_PREFS["dynamics"]=="on" && $rpl->machine_exists($MACH_ID)){
                
                // "js_forreservetable.php" needs to be included to populate reservations via javascript. 
                // it loads one function... putTimeslotsIn()...
                include("../js/js_forreservetable.php");
                
                //putTimeslotsIn() is ran on loading of page
                $onload = "putTimeslotsIn()";
            }
            //<------ end scripts to populate calandar table ---------->//  
        ?>
    });
        
</script>



<div id="right-space">
    <div id="machine-title">
        <h2>120 Watt Epilog</h2>
        <a href="#" id="week0" class="week selected">THIS WEEK</a>
        /
        <a href="#" id="week1" class="week">NEXT WEEK</a>
        
        
        <span>You may sign up for <b>2 hours</b> per day.</span>
    </div>    
        
    <div id="calendar">
    
        <!-- PHP Calendar -->
        <? include("../reservetable.php"); ?>
    
    </div>
    
</div>