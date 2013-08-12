//
//
// TODO: edit this/next for each ajax call
//	
//
//


$(window).load(function(){
    //onload default settings
    var defaults = {
	machine: '120',
	week: '0',
	url: 'tools/Laser-Epilog-2.php',
    }
    
    //get all links for student machines
    var machines = $('#student').find('li');
    
    
    //////// BEHAVIORS /////////////
    
    //on student machine click 
    $(machines).click( function(){
	if ( $(this).attr('id') != defaults.machine ){
	    getMachine( $(this) );
	    //reset the week when new machine clicked
	    defaults.week = 0;
	}
    })
    
    //get correct week ie either this week or next
    $('#main').on('click', '.week', function(e){
	e.preventDefault();
	var tempID = $(this).attr('id');
	var weekLink = tempID.replace('week', '');
	
	if (weekLink != defaults.week){
	    getWeek( $(this), tempID )
	    defaults.week = weekLink;
	}
    })    
    
    
    //////// FUNCTIONS /////////////
    
    
    // get selected machine
    function getMachine( el ){
	//clear the defaults.machine calendar
	$('#right').html("<img src='../rpl_site/img/loading.gif' class='center'>")
	
	//set selected link to defaults.machine
	$(el).addClass('selected');
	$(el).find('span').text(">")
	
	$(el).siblings().each( function(){
	    $(this).removeClass('selected');
	    $(this).find('span').html('')
	})
	
	//set defaults.machine variable
	defaults.machine = $(el).attr('id');
	var id = getMachineURL();
	
	//call ajax function with set ID
	getMachineAJAX( id, el )

    }

    
    //replace #right div with new machine php file
    function getMachineAJAX(id, el) {
	$.ajax({
	    type: "GET",
	    url: 'tools/' + id,
	    success: function(data) {
		// data is ur summary
		$('#right').html(data);
	    }
     
	});
     
     }
    
 
    function getWeek(el, id){
	$('#right').html("<img src='../rpl_site/img/loading.gif' class='center'>")
	if (id=='week0'){
	    getWeekAJAX( id, '' )
	} else {
	    getWeekAJAX( id, '?w=1' )
	}
    }
    
    
    //change the selector state
    function changeWeek( id ) {
 	$('#' + id).addClass('selected');
	$('#' + id).siblings().each( function(){
	    $(this).removeClass('selected');
	})
    }    
    
    
    //get additional weeks
    function getWeekAJAX( el, week ){
	//make call to correct machine
	var url = getMachineURL();
	
	$.ajax({
	    type: "GET",
	    url: 'tools/' + url + week,
	    success: function(data) {
		// data is ur summary
		$('#right').html(data);
		changeWeek( el )
	    }
     
	});
    }
    
    
    //////// HELPERS /////////////  
    
    
    // get the url for certain machines
    function getMachineURL(){
	//get machine id and set variable
	if ( defaults.machine == '60'){
	    var id = 'Laser-Epilog-1.php';
	}
	if ( defaults.machine == '120'){
	    var id = 'Laser-Epilog-2.php';
	}
	if ( defaults.machine == 'univ'){
	    var id = 'Laser-Univ-1.php'
	}
	if ( defaults.machine == 'modela'){
	    var id = 'Modela-1.php'
	}
	if ( defaults.machine == 'vinyl'){
	    var id = 'Vinyl-1.php'
	}
	return id;
    }
    
    
    
    
})// END window.load()

