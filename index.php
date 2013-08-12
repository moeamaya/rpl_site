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

                    
		  





<!DOCTYPE html>
<html class="no-js">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>RPL | Reserve</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">

        <link rel="stylesheet" href="css/normalize.min.css">
        <link rel="stylesheet" href="css/main.css">
	

	 <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
	 
	 
        <!--[if lt IE 9]>
            <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
            <script>window.html5 || document.write('<script src="js/vendor/html5shiv.js"><\/script>')</script>
        <![endif]-->
    
	<script>
	    $(document).ready(function(){ 
		
		putTimeslotsIn();
		
		<?
		//<--------- scripts to populate calandar table --------------->//
		//if dynamics is turned on
		    if ($ADMIN_PREFS["dynamics"]=="on" && $rpl->machine_exists($MACH_ID)){
			
			// "js_forreservetable.php" needs to be included to populate reservations via javascript. 
			// it loads one function... putTimeslotsIn()...
			include("js/js_forreservetable.php");
			
			//putTimeslotsIn() is ran on loading of page
			$onload = "putTimeslotsIn()";
		    }
		    //<------ end scripts to populate calandar table ---------->//  
		?>
	    });
		
	</script>
    
    </head>
    <body>

        <header>
	    <div class="wrap">
		<img src="img/mit_logo.png">
		<h1>Rapid Prototyping Lab</h1>
		<div id="menu">
		    <a href="#" class="selected"><img src="img/reservations.png">RESERVATIONS</a>
		    <a href="hours.html"><img src="img/hours.png">HOURS</a>
		    <a href="about.html"><img src="img/about.png">ABOUT</a>
		</div>
		<a href="mailto:rpl@mit.edu" id="email">questions? rpl@mit.edu<img src="img/email.png"></a> 
	    </div>

	</header>
	
	<div id="shadow">
	<div id="main-head">
	    <div class="wrap">
		<img src="img/reservations.png">
		RESERVATIONS
	    </div>
	</div>
	<!-- setting height for ajax data -->
	<div id="main" style="height: 730px">
	    <div class="wrap">
		<div id="left">
		    <div id="left-menu">
			<h3>STUDENT SIGN UP</h3>
			<ul id="student" class="options">
			    <li id="120" class="selected">120 Epilog<span>></span></li>
			    <li id="60">60 Epilog<span></span></li>
			    <li id="univ">Universal<span></span></li>
			    <li id="modela">Modela<span></span></li>
			    <li id="vinyl">Vinyl Cutter<span></span></li>
			</ul>
			
			
			<h3>3D PRINTER UPLOAD</h3>
			<ul id="student" class="options">
			    <li id="abs">ABS<span></span></li>
			    <li id="zcorp">ZCorp<span></span></li>
			</ul>			
			
			
			<h3>TA SIGN UP</h3>
			<ul class="options">
			    <li id="buddy">Shopbot Buddy<span></span></li>
			    <li id="n51">Shopbot N51<span></span></li>
			    <li id="waterjet">Waterjet<span></span></li>
			</ul>
		    </div>	    
		</div>
		
		<div id="right">
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
			    <? include("reservetable.php"); ?>
			
			</div>
			
		    </div>
		</div>
		
	    </div>
	</div>
	</div>
	
	
	<footer>
	    &#xA9; 2013 RPL @ MIT Department of Architecture, 77 Massachusetts Avenue, Room 7-337, Cambridge, MA 02139 | (617) 253-7791
	</footer>
	
	
	
	
        <script src="js/main.js"></script>

	
    </body>
</html>
