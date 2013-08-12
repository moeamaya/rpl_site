<?
	//dependencies
	include("../functions.php");
	$rpl = new RPLfunctions();
	
	$rpl->connect();
	
	//grab machine info..(looks it up by the id)
	$data = $rpl->getMachineInfo2($_GET['m']);
	$MACHINE_INFO = $data["data"];
	
	
	//grab selected reservation data with timestamp, start hr, and max hrs
	$explodeID = explode("-", $_GET['id']);
	$rpl->connect();
	$RESERVATION_DATA = $rpl->getReservationDataById($_GET['id'], $MACHINE_INFO["id_name"]);
	
	//if timeslot start and end are not on same day, format text accordingly
	$compare = $rpl->compare($RESERVATION_DATA["start"], $RESERVATION_DATA["end"]);
	
	$dayText =  date("D, m/j/y", $RESERVATION_DATA["start"]);
	$timeText = date("g:i a", $RESERVATION_DATA["start"])." - ".date("g:i a", $RESERVATION_DATA["end"]);
	
	
	if (!$compare["sameday"]){
		$dayText = date("D, m/j/y", $RESERVATION_DATA["start"])." - ".date("m/j/y", $RESERVATION_DATA["end"]);
	}
	
	//get user data
	$USER_DATA = $rpl->getUser();
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
     <head>
          <title>
               MIT | RPL | Rapid Prototyping Lab - Details
          </title>
           <link rel="icon"  href="images/favicon.ico" />
          <link href="../rpl.css" media="all" rel="stylesheet" type="text/css" />    
          <link rel="stylesheet" media="screen" href="../js/coin-slider/coin-slider-styles.css" type="text/css" />
          <link rel="stylesheet" media="screen" href="../js/superfish/superfish.css" type="text/css" />
          <link rel="stylesheet" media="screen" href="../js/superfish/superfish-navbar.css" type="text/css" />
          
          <script src="../js/jquery/jquery-1.4.4.min.js" type="text/javascript"></script>
          <script src="../js/superfish/superfish.js" type="text/javascript"></script>
                    <script type="text/javascript">
//<![CDATA[

                $(document).ready(function(){ 
                		//this initializes the navigation bar
                        $("ul.sf-menu").superfish({ 
                                pathClass:  "current"
                        });
                }); 
			
			function confirmDelete(choice){
				if (choice){
					document.getElementById("actionsBox").style.display="none";
					document.getElementById("confirmDel").style.display="block";
				}
				else{
					document.getElementById("actionsBox").style.display="block";
					document.getElementById("confirmDel").style.display="none";
				}
			}


          //]]>
          </script>
     </head>
     <body>
		  <?include("../header.php")?>
          <div id="container">
               <?include("../navbar.php")?> 
               <div id="content">
				<h1>Timeslot Details / <?=$timeText?></h1>
				<a href="javascript:history.back()">Go Back</a><br><br>
				<?
	//if user is attempting to delete
	if ($_POST['confirm_delete']){
		//use "admin trump all" delete, if user is an admin
		$result = $rpl->delete_reservation($_POST['RES_ID'], $_POST['MACH'], 0);
		if ($result["success"])
			echo "<p class='success'>".$result["success"]."</p>";
		else if ($result["error"])
			echo "<p class='error'>".$result["error"]."</p>";
		else if (!$result)
			echo "<p class='error'>An Unknown error occurred. Try again later.</p>";
		
	}
	else
	{
	?>
				Below is a detailed overview of the selected timeslot. 
				<br><br>
				<table border=0 cellspacing=5>
				<tr><td valign="middle" align="left" class="descriptor">Day</td><td><span class='highlight'><?=$dayText?></span></td></tr>
				<tr><td valign="middle" align="left" class="descriptor">Time</td><td><span class='highlightBlue'><?=$timeText?></span></td></tr>
				<tr><td valign="top" align="left" class="descriptor">Description</td><td><?=$RESERVATION_DATA['description']?></td></tr>	
				<tr><td valign="top" align="left" class="descriptor">Machine</td><td><?=$MACHINE_INFO["name"]?></td></tr>
				<tr><td valign="top" align="left" class="descriptor">Name</td><td><?=$RESERVATION_DATA["name"]?></td></tr>
				<tr><td valign="top" align="left" class="descriptor">Email</td><td><a href="mailto:<?=$RESERVATION_DATA["email"]?>"><?=$RESERVATION_DATA["email"]?></a></td></tr>
				<tr><td valign="top" align="left" class="descriptor">Reserved on</td><td><?=date("r", $RESERVATION_DATA["timestamp"])?></td></tr>
	
				</table>
	
				<br>
				<?
				if ($USER_DATA["email"] == $RESERVATION_DATA["email"])
				{
				?>
				
				
				<hr><br>
				<h5>Actions</h5>
								<div id="confirmDel" style="display: none; padding: 10px 0 10px 0;">
								<p><form method="POST" action="<? echo $rpl->getFilename()."?id=".$_GET['id']."&m=".$_GET['m']."&w=".$_GET['w']?>"><p>Are you sure you want to cancel?</p><input type="hidden" name="RES_ID" value="<?=$_GET['id']?>"><input type="hidden" name="MACH" value="<?=$_GET['m']?>"><input type="submit" name="confirm_delete" value="Confirm Cancellation"> <input type="button" value="Never Mind" onclick="confirmDelete(0)"></p></form></div>
				<div id="actionsBox" style='padding: 10px 0 10px 0;'>
				<p><input type="button" value="Cancel Reservation" onclick="confirmDelete(1)"> </p>
				</div>
				
				<?
				} //end of if user is author, then hide actions
				?>
				<hr><br><h5>Important Info</h5><br><br><b>Cancelling</b><p>Please make sure to be there at this time. If you need to cancel.. Return to the calendar slot, click <i><u>details</u></i> under your name, and then click <i><u>cancel reservation</u></i>. Be fair to your fellow students and make sure to cancel AS SOON as you know you will not make your reservation.</p>
					</div>
			<?
			} //end of if user sent a delete request
			?>
               <?include("../footer.php")?>
          </div>
     </body>
</html>