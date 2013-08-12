<?
	//dependencies
	include("../functions.php");
	$rpl = new RPLfunctions();
	
	$rpl->connect();
	//grab machine info..(looks it up by the id)
	$data = $rpl->getMachineInfo2($_GET['m']);
	$MACHINE_INFO = $data['data'];
	
	//get user data
	$USER_DATA = $rpl->getUser();
	
	//grab selected reservation data with timestamp, start hr, and max hrs
	$explodeID = explode("-", $_GET['id']);
	$RESERVATION_DATA = $rpl->getReservationData($explodeID[0],$explodeID[1], $MACHINE_INFO["max_hours_day"]);	
	

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
     <head>
          <title>
               MIT | RPL | Rapid Prototyping Lab - Reserve a Timeslot
          </title>
           <link rel="icon"  href="images/favicon.ico" />
          <link href="../rpl.css" media="all" rel="stylesheet" type="text/css" />    
          <link rel="stylesheet" media="screen" href="../js/coin-slider/coin-slider-styles.css" type="text/css" />
          <link rel="stylesheet" media="screen" href="../js/superfish/superfish.css" type="text/css" />
          <link rel="stylesheet" media="screen" href="../js/superfish/superfish-navbar.css" type="text/css" />
          
          <script src="../js/jquery/jquery-1.4.4.min.js" type="text/javascript"></script>
          <script src="../js/superfish/superfish.js" type="text/javascript"></script>
          <script src="../js/coin-slider/coin-slider.min.js" type="text/javascript"></script>
          
          <script type="text/javascript">
//<![CDATA[

                $(document).ready(function(){ 
                		//this initializes the navigation bar
                        $("ul.sf-menu").superfish({ 
                                pathClass:  "current"
                        }); 
                        
                        //settings for slideshow
                                var coinSettings = {    
                width: 500, // width of slider panel
                height: 375,
                delay: 5000, // delay between images in ms
                titleSpeed: 2000, // speed of title appereance in ms
                effect: 'straight', // random, swirl, rain, straight
                navigation: true, // prev next and buttons
                opacity: .85,
                links : true, // show images as links 
                hoverPause: true // pause on hover              
          };      
          				//initializes slideshow
                        jQuery("#coin_slider80").coinslider(coinSettings);
                }); 

          //]]>
          </script>
     </head>
     <body>
		  <?include("../header.php")?>
          <div id="container">
               <?include("../navbar.php")?> 
               <div id="content">
				<h1>Reserve a Timeslot</h1>
				<a href="javascript:history.back()">Go Back</a><br><br>
				<?
				//if user submitted a reservation request, validate it.
				if ($_POST['reserve']){		
					//print_r($_POST);
					$connect = $rpl->connect();
					$result = $rpl->validate($_POST['mach'], $_POST['now'], $_POST['slot'], $_POST['hours'], $_POST['description']);
					if ($result["error"])
						echo "<p class='error'>ERROR: ".$result["error"]."</p>";
					if ($result["errorInfo"])
						echo "<p>".$result["errorInfo"]."</p>";
					if ($result["success"]){
						echo "<p class='success'>".$result["success"]."</p>";
						echo "<p>Your timeslot is for the following time: <br><span class='highlight'>".$result['slot_time']."</span></p>";
						
						echo "<br><hr><br><br><b>Cancelling</b><p>Please make sure to be there at this time. If you need to cancel.. Return to the calendar slot, click <i><u>details</u></i> under your name, and then click <i><u>cancel reservation</u></i>. Be fair to your fellow students and make sure to cancel AS SOON as you know you will not make your reservation.</p>";
					}
				}
				if (!$result["success"])
				{
				?>
				Please fill out the information below and submit to reserve your timeslot.
				<br><br>
				<form method="POST" action="<? echo $rpl->getFilename()."?id=".$_GET['id']."&m=".$_GET['m']."&w=".$_GET['w']?>">
				<input type="hidden" name="slot" value="<?=$RESERVATION_DATA['start']?>">
				<input type="hidden" name="now" value="<?=time()?>">
				<input type="hidden" name="id" value="<?=$_GET['id']?>">
				<input type="hidden" name="mach" value="<?=$_GET['m']?>">
				<table border=0 cellspacing=5>
				<tr><td valign="top" align="left" class="descriptor">Machine</td><td><?=$MACHINE_INFO['name']?></td></tr>
				<tr><td valign="top" align="left" class="descriptor">Name</td><td> <?=$USER_DATA["name"]?></td></tr>
				<tr><td valign="top" align="left" class="descriptor">Email</td><td> <?=$USER_DATA["email"]?></td></tr>
				<tr><td valign="top" align="left" class="descriptor">Day</td><td><span class='highlight'><?=$RESERVATION_DATA['dayText']?></span></td></tr>
				<tr><td valign="top" align="left" class="descriptor">Hours</td><td><SELECT NAME="hours">
<?
foreach ($RESERVATION_DATA['slots'] as $key=>$value)
echo "<option value='".$value['totalHours']."'>".$value["text"];
?>
</SELECT>
</td></tr>
				<tr><td valign="top" align="left" class="descriptor">Description</td><td><textarea class='reserveText' name="description"><?=$_POST['description']?></textarea></td></tr>		
								<tr><td valign="top" align="left" class="descriptor"></td><td><input type="submit" name="reserve" value="Reserve"></td></tr>	
				</table>
				</form>
				<?
				} //end of if not success statement
				?>
					</div>
               <?include("../footer.php")?>
          </div>
     </body>
</html>