<?
	//dependencies
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
           <link rel="icon"  href="images/favicon.ico" />
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
     </head>
     <body>
		  <?include("../header.php")?>
          <div id="container">
               <?include("../navbar.php")?> 
               <div id="content">
				<?include("studentnav.php")?>
				<h1>Your Timeslots</h1>
				<a href="javascript:history.back()">Go Back</a><br><br>
				<table border=0 cellspacing=5>
				<tr><td valign="top" align="left" class="descriptor">Name:</td><td> <?=$USER_DATA['name']?></td></tr>
				<tr><td valign="top" align="left" class="descriptor">Email:</td><td> <?=$USER_DATA['email']?></td></tr>
				<tr><td valign="top" align="left" class="descriptor">Issuer:</td><td> <?=$USER_DATA['issuer']?></td></tr>
			</table><br>
				<form method="GET" action="<?=$rpl->getFilename()?>">
					 <select name='howfarback'>
					<option value=0 <?=($howfarback==0) ? "selected" : "" ?>>Current</option>
					<option value=7 <?=($howfarback==7) ? "selected" : "" ?>>Last 7 days</option>
					<option value=30 <?=($howfarback==30) ? "selected" : "" ?>>Last 30 days</option>
					<option value=60 <?=($howfarback==60) ? "selected" : "" ?>>Last 60 days</option>
				</select> <input type='submit' value='go'>
				</form>
								<?
				if ($ALL_RESERVS){
				?>
				<p>Below is a summary of your scheduled Timeslots.</p> 
				<table border=0 cellpadding=5 class="reserveTable">
					<tr><td class='tableHead'>Date</td><td class='tableHead'>Machine</td><td class='tableHead'>Total Hrs.</td><td class="tableHead">TimeSlot</td><td class="tableHead">Actions</td></tr>
					<?
					foreach ($ALL_RESERVS as $value){
						$past_class = ($value["end"]<time()) ? "class='past_timeslot'" : "";
						$now_class = ($value["end"]>time() && $value["start"]<time()) ? "class='highlight'" : "";
					?>
					
					<tr <?=$past_class?><?=$now_class?>><td><?echo (date("m/j/y", $value["start"])== date("m/j/y", $value["end"])) ? date("m/j/y", $value["start"]) : date("m/j/y", $value["start"])." - ".date("m/j/y", $value["end"]); ?></td>
						<td><?=$rpl->getMachineInfo2($value["machine"], "name")?></td>
						<td><?=$value["hours"]?></td>
						<td><?=date("g:i a", $value["start"])." - ".date("g:i a", $value["end"]);?></td>
						<td><a href="details.php?id=<?=$value["reservedTimestamp"]?>&m=<?=$value["machine"]?>">details</a>
					</tr>
					<?
					}

					?>
				</table>
				<?
				}
				else if (!$USER_DATA["email"]){
						echo "<p class='error'>You  do not have any reserved slots in the time specified or yout certifcates might be invalid.</p>";
					}
				?>

					</div>
               <?include("../footer.php")?>
          </div>
     </body>
</html>