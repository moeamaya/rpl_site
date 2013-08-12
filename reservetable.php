<?
//pull up reservations
$rpl->connect();


?>

<table border=0 cellpadding=5  class="reserveTable">
<tr>
<td class='tableHead first'></td>
<td <?=($week['today']=="Sunday" && $thisWeek==1) ? "class='tableHeadSel'": "class='tableHead'"?>>Sun <?=date("(m/j)", $selWeek["start"]);?></td>
<td <?=($week['today']=="Monday" && $thisWeek==1) ? "class='tableHeadSel'": "class='tableHead'"?>>Mon <?=date("(m/j)", $selWeek["week"]["Monday"]);?></td>
<td <?=($week['today']=="Tuesday"&& $thisWeek==1) ? "class='tableHeadSel'": "class='tableHead'"?>>Tues <?=date("(m/j)", $selWeek["week"]["Tuesday"]);?></td>
<td <?=($week['today']=="Wednesday" && $thisWeek==1) ? "class='tableHeadSel'": "class='tableHead'"?>>Wed <?=date("(m/j)", $selWeek["week"]["Wednesday"]);?></td>
<td <?=($week['today']=="Thursday" && $thisWeek==1) ? "class='tableHeadSel'": "class='tableHead'"?>>Thurs <?=date("(m/j)", $selWeek["week"]["Thursday"]);?></td>
<td <?=($week['today']=="Friday" && $thisWeek==1) ? "class='tableHeadSel'": "class='tableHead'"?>>Fri <?=date("(m/j)", $selWeek["week"]["Friday"]);?></td>
<td <?=($week['today']=="Saturday" && $thisWeek==1) ? "class='tableHeadSel'": "class='tableHead'"?>>Sat <?=date("(m/j)", $selWeek["week"]["Saturday"]);?></td>
</tr>
<?
$days = array( "Sunday" => date("m-j-y", $selWeek["week"]["Sunday"]),
	"Monday" => date("m-j-y", $selWeek["week"]["Monday"]),
	"Tuesday" => date("m-j-y", $selWeek["week"]["Tuesday"]), 
	"Wednesday" => date("m-j-y", $selWeek["week"]["Wednesday"]), 
	"Thursday" => date("m-j-y", $selWeek["week"]["Thursday"]), 
	"Friday" => date("m-j-y", $selWeek["week"]["Friday"]), 
	"Saturday" => date("m-j-y", $selWeek["week"]["Saturday"])
	);
for ($x=0; $x<24; $x++ )
{
	$asterisk="";
	$time=$x+6;
	$ampm="am";
	if ($time>=24){
		$ampm="am";
		$time = ($time-24);
		if ($time==0)
			$time=12;
		$asterisk="<span class='red'>*</span>";
	}
	else if ($time>=12){
		$ampm="pm";
		$time = ($time-12);
		if ($time==0)
			$time=12;
	}

	$timeColumn_class = (date("g a")== $time." ".$ampm) ? "timeColumn highlight" : "timeColumn white";
	echo "<tr><td class='$timeColumn_class' id='$time-$ampm'>$time:00 $ampm$asterisk</td>\n";
		$w="";
	if ($_GET['w'])
		$w="&w=".$_GET['w'];
		
	foreach ($days as $key => $value){
		//if timeslot is now, highlight!
		if (date("m/j/y g a")==date("m/j/y", ($selWeek["week"][$key]+($x*60*60)))." ".$time." ".$ampm){
		//generate text to display for the 'now' timeslot...if no one is in it, generate a blank text
		$nowText = ($reserves2["now"]["user"]) ? "In use: <a href='../students/details.php?id=".$reserves2['now']["reservedTimestamp"]."&m=".$reserves2['now']["machine"]."'>".$reserves2['now']['user']."</a>" : "<a href='../students/reserve.php?id=".$selWeek["week"][$key]."-".$x."&m=".$MACHINE_INFO['id_name']."$w'>click to reserve</a>";
		echo "<td class='highlight' align='center' id='".$selWeek["week"][$key]."-".$x."'>".$nowText."</td>";
		}
		//if it is already past, grey out!
		else if (time()>($selWeek["week"][$key]+($x*60*60)))
	{
		echo "<td class='past' id='".$selWeek["week"][$key]."-".$x."'></td>\n";
		//otherwise, allow it to be reserved
	}else{
			echo "<td class='availableSlot' id='".$selWeek["week"][$key]."-".$x."'><a href='../students/reserve.php?id=".$selWeek["week"][$key]."-".$x."&m=".$MACHINE_INFO['id_name']."$w'>click to reserve</a></td>\n";
			}
	}
	echo "</tr>";
}
?>
</table>
<br>
<i>*This Late night AM timeslot is referring to the next day.</i>