<?
	//dependencies
	$rootPath = $_SERVER['DOCUMENT_ROOT'];
	include("../functions.php");
	$rpl = new RPLfunctions();
	
	
	$rpl->connect();
	
	//get user data
	$USER_DATA = $rpl->getUser();
	//get all reservation slots and depending on how far back user specifies. default is 0 (current)
	$howfarback = ($_GET['howfarback']!="") ? $_GET['howfarback'] : 7;
	
	//..if user is specified look it up
	if ($USER_DATA["email"]){
		$ALL_RESERVS = $rpl->getReservationsByUser($USER_DATA['email'], 0, $howfarback*24*60*60);
	
	
	//remove duplicates (if current timeslot is active and also within last 7 days, for example)
	$ALL_RESERVS = array_unique($ALL_RESERVS);
	
	//sort in ascending order
	ksort($ALL_RESERVS);
	}
	
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
     <head>
          <title>
               MIT | RPL | Rapid Prototyping Lab - Student Panel
          </title>
           <link rel="icon"  href="../images/favicon.ico" />
          <link href="../rpl.css" media="all" rel="stylesheet" type="text/css" />    
          <link rel="stylesheet" media="screen" href="../js/coin-slider/coin-slider-styles.css" type="text/css" />
          <link rel="stylesheet" media="screen" href="../js/superfish/superfish.css" type="text/css" />
          <link rel="stylesheet" media="screen" href="../js/superfish/superfish-navbar.css" type="text/css" />
          
          <script src="../js/jquery/jquery-1.4.4.min.js" type="text/javascript"></script>
          <script src="../js/superfish/superfish.js" type="text/javascript"></script>
		<script>
		                $(document).ready(function(){ 
                		//this initializes the navigation bar
                        $("ul.sf-menu").superfish({ 
                                pathClass:  "current"
                        }); 
                        }); 
		</script>
		<style>
		img.help{
			max-width: 600px;
			margin: 10px 0 10px 0;
			border: 1px solid #666;
			padding: 5px;
		}
		.subheader{
			font-style: italic;
			font-weight: bold;
		}
		</style>
     </head>
     <body>
		  <?include("../header.php")?>
          <div id="container">
               <?include("../navbar.php")?> 
               <div id="content">
<?include("studentnav.php")?> 
<h1>Help Guide <span style='font-size: 12px; font-weight:normal; '>Last Update: 6/14/11</span></h1>
<h5 style='margin-top: 50px; '>Quick Links</h5>
<p>
<a href='#intro'>Introduction</a><br>
<a href='#viewQueue'>Viewing the queue calendar</a><br>
<a href='#reserve'>Reserving a Timeslot</a><br>
<a href='#cancel'>Cancelling a Timeslot</a><br>
<a href='#studentPortal'>My Student Portal</a> (Checking your reserved timeslots)
</p>
<br><hr><br>
<h5><a name='intro'>Introduction</a></h5>
<p>Greetings, this guide was created to introduce newcomers to the <b>MIT RPL <i>OQS</i></b> (Online Queueing System) and how it works. If you have never heard of the RPL <i>OQS</i>, it's an online system created for students to reserve times to use the RPL machines (such as laser cutters, CNC, etc) and facilities.</p><p>  We hope it is useful, and if you have any comments/questions/suggestions..feel free to contact us. Thanks and enjoy!
</p><br><hr><br>
<h5><a name='viewQueue'>Viewing the queue calendar</a></h5>
<p><img class='help' src='images/rpl-help-home.jpg'></p>
<p>Above you will see what the rpl.mit.edu homepage might look like. In order to see a queue calendar, choose a machine by moving your mouse over tools > [machine type] > [machine].</p>
<p><img class='help' src='images/rpl-help-machine.jpg'></p>
<p>Next, you will see the machine's page (like the one above) offering you information such as where it is located, tutorials on how to use it, and more. </P><P><b>If it has a queue calendar</B>, you can scroll down to the bottom of page or click the shortcut link above the machine name. Both will take you to the calendar. </p>
<p><img class='help' src='images/rpl-help-cal.jpg'></p>
<p>
<div class='subheader'>QUEUE PANEL</div>
Above is what the typical calendar will look like more or less...depending on the machine or week, it may look more filled or less filled, but the structure will be the same. The calendar panel displays 2 months (current and next), with the current week shown and the current day highlighted in <span style='background: yellow'>yellow</span>. Next to these visual month calendars,  It also tells you <span style='color: red'>how many hours a day</span> you may reserve up to, current time, and a week dropdown selector.</p><p> Other restrictions may include <span style='color: red'>max hours/week</span>, and <span style='color: red'>up to how many weeks in advance you are permitted to reserve</span>. If you are allowed to reserve a day past just the current week, other weeks will be available via a dropdown menu.</p>
<p>
<div class='subheader'>QUEUE CALENDAR</div>
Below the panel is the weekly calendar...from sunday to monday. Each row represents a different 1 hour timeslot starting with 6am that day and ending with 5am the next day (archie format...same format as old signup sheets). The current timeslot, hour and day are all highlighted in <span style='background: yellow'>yellow</span>.</p>
<p>
<div class='subheader'>COLOR CODINGS</div>Blank spaces are the available times, and when rolled over, they reveal a <b>click to reserve</b> link. A reserved timeslot by another user is highlighted in <span style='background: #CAE1FF'>blue</span>. These display the user who has the spot, and a 'details' link for more info about their time. Admin related timeslots (for tutorials, classes, etc) are highlited in <span style='background: #FFB6C1'>red</span>. The current timeslot is highlighted in  <span style='background: yellow'>yellow</span> and also displays the user who has the spot, and a 'details' link for more info about their time. Anything already past the current timeslot, is <span style='background: #c0c0c0'>greyed out</span>. </p>
</p>
<br><hr><br>
<h5><a name='reserve'>Reserving a Timeslot</a></h5>
<p><img class='help' src='images/rpl-help-reserve.jpg'></p>
<p>As shown above, To reserve a timeslot, scroll over an available slot and click the <b>click to reserve</b> link.</p>
<p><img class='help' src='images/rpl-help-reservewidg.jpg'></p>
<p>This will bring you to the reservation widget. Your name and email will be already known from your MIT certificates. Just select from the dropdown the amount of hours you would like. The dropdown will allow you to choose up to the most amount of possible hours you are allowed to sign up for, for that day. You also need to add a small description of what your doing. After that, hit <b>Reserve</b></p><p>A screen will then either confirm your reservation or tell you an error if something went wrong. You may reserve up to 45 min into an available timeslot.</p>
<br><hr><br>
<h5><a name='cancel'>Cancelling a Timeslot</a></h5>
<p><img class='help' src='images/rpl-help-details.jpg'></p>
<p>On the queue calendar, find your timeslot reservation and click 'details' link under your username.</p>
<p><img class='help' src='images/rpl-help-cancel.jpg'></p>
<p>Under the "Actions" section, you will find a cancel button. Clicking and confirming, will cancel your reservation. You can do this up to 15 min into your timeslot. **If it wasn't your reservation, the cancel button won't be available.</p>
<br><hr><br>
<h5><a name='studentPortal'>My Student Portal</a></h5>
<P>The student portal is unique to you. It offers students special features and will be constantly added to. To access, click on <b>My Portal</b> on the navigation bar.</p>
<p><img class='help' src='images/rpl-help-myportal.jpg'></p>
<p>Right now, One of its most useful features is that is gives you the ability to view your past, current, and upcoming timeslots for all machines. That way you can look in one place to check your times, and not the individual calendars. This is most convenient when you have a LOT of reserved times. </P>
</div>
               <?include("../footer.php")?>
          </div>
     </body>
</html>