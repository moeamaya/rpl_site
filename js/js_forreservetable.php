
				function putTimeslotsIn(){
					//these variables notes if a tween has been labeled or not
					var tweenLabel_user = false;
					var tweenLabel_details = false;
					
					//mark keyframes
					var keyframes = eval(<?=json_encode($reserves2["keyframes"])?>);
					for (var i in keyframes){
						if (document.getElementById(i)){
							if (document.getElementById(i).className != "highlight" && document.getElementById(i).className != "past")
								document.getElementById(i).className = (keyframes[i]["admin"]==1) ? 'takenSlot-admin' : 'takenSlot';
							else if (document.getElementById(i).className != "highlight")
								document.getElementById(i).className = "past_taken";
							if (document.getElementById(i).className != "highlight")
								document.getElementById(i).innerHTML = keyframes[i]["user"];
						}
					}
					

					//mark endframes
					var endframes = eval(<?=json_encode($reserves2["endframes"])?>);
					for (var i in endframes){
						if (document.getElementById(i)){
							if (document.getElementById(i).className != "highlight")
							document.getElementById(i).innerHTML = "<div style='text-align: right'><a href='../students/details.php?id="+endframes[i]["reservedTimestamp"]+"&m="+keyframes[endframes[i]["actualTimestamp"]]["machine"]+"'>info</a></div>";
							if (document.getElementById(i).className != "highlight" && document.getElementById(i).className != "past"){
								document.getElementById(i).className = (keyframes[endframes[i]["actualTimestamp"]]["admin"]==1) ? 'takenSlot-admin' : 'takenSlot';
								document.getElementById(i).style.borderTop = (keyframes[endframes[i]["actualTimestamp"]]["admin"]==1) ?  '3px solid #BFBFBF' : '3px solid #BFBFBF';
							}
							else if (document.getElementById(i).className != "highlight"){
								document.getElementById(i).className = "past_taken";
								document.getElementById(i).style.borderTop =  '3px solid #7F7F7C';
							}
							
						}
					}
					
					//mark tweens
					var tweens = eval(<?=json_encode($reserves2["tweens"])?>);
					for (var i in tweens){
						if (document.getElementById(i)){
						if (document.getElementById(i).className != "highlight")
							document.getElementById(i).innerHTML =  "";
							if (document.getElementById(i).className != "highlight" && document.getElementById(i).className != "past"){
								document.getElementById(i).className = (keyframes[tweens[i]["actualTimestamp"]]["admin"]==1) ? 'takenSlot-admin' : 'takenSlot';
								document.getElementById(i).style.borderTop = (keyframes[tweens[i]["actualTimestamp"]]["admin"]==1) ?  '3px solid #BFBFBF' : '3px solid #BFBFBF';
							}
							else if (document.getElementById(i).className != "highlight"){
								document.getElementById(i).className = "past_taken";
								document.getElementById(i).style.borderTop =  '3px solid #7F7F7C';
							}
							
						if (!document.getElementById(tweens[i]["actualTimestamp"]) && tweenLabel_user == false){
						if (document.getElementById(i).className != "highlight")	
							document.getElementById(i).innerHTML =  keyframes[tweens[i]["actualTimestamp"]]["user"];
							tweenLabel_user = true;
						}
						else if (!document.getElementById(keyframes[tweens[i]["actualTimestamp"]]["endFrame"]) && tweenLabel_details == false){
						if (document.getElementById(i).className != "highlight")	
							document.getElementById(i).innerHTML =  "<span style='float: right'><a href='../students/details.php?id="+tweens[i]["reservedTimestamp"]+"&m="+keyframes[tweens[i]["actualTimestamp"]]["machine"]+"'>info</a></span>";
							tweenLabel_details = true;
							}
						}
					}
					
					
					//mark all in ones
					var allinones = eval(<?=json_encode($reserves2["allinones"])?>);
					for (var i in allinones){
						if (document.getElementById(i)){
						if (document.getElementById(i).className != "highlight")
							document.getElementById(i).innerHTML = allinones[i]["user"]+"<span style='float: right'><a href='../students/details.php?id="+allinones[i]["reservedTimestamp"]+"&m="+allinones[i]["machine"]+"'>info</a></span>";
							if (document.getElementById(i).className != "highlight" && document.getElementById(i).className != "past")
								document.getElementById(i).className = (allinones[i]["admin"]==1) ? 'takenSlot-admin' : 'takenSlot';
							else if (document.getElementById(i).className != "highlight")
								document.getElementById(i).className = "past_taken";
						}
					}
				}