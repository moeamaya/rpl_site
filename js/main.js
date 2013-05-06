$(window).load(function(){


    var machines = $('#student').find('li');
    
    $(machines).click( function(){
	choice( $(this) )
    })

    
    //select helper function
    function choice(el){
	$(el).siblings().each( function(){
	  $(this).removeClass('selected');
          $(this).find('span').html('')
	})
	
	$(el).addClass('selected');
        $(el).find('span').text('>')
        
        if ($(el).attr('id') == '60'){
            var title = '60 Watt Epilog'
        }
        if ($(el).attr('id') == '120'){
            var title = '120 Watt Epilog'
        }
        if ($(el).attr('id') == 'univ'){
            var title = 'Universal'
        }
        if ($(el).attr('id') == 'modela'){
            var title = 'Roland Modela'
        }
        if ($(el).attr('id') == 'vinyl'){
            var title = 'Vinyl Cutter'
        }
        
        
        $('#right').find('h2').text(title)
    }
    


})

