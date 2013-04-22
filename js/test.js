$(window).load(function(){


    var image = $('#wrapper').find('li')
    
    
    //var width = image.first().width(0)
    
    $('#prev, #next').click( function(){
        var dir = $(this).attr('id')
        
        move(dir)
    })
    
    
    function move(dir){
        if (dir == "prev"){
            $('#carousel').fadeOut('fast');
            
            $('#carousel').animate({
                top: '-=400',
            }, 0, function(){
               $('#carousel').hide().fadeIn('slow')
            }
            )
        }
       
    }
    
})

