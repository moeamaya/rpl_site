
var machines = $('.options').find('li')

console.log(machines)

$(machines).click( function(){
    $.each($(this), function(el){
        $(el).removeClass('selected')
    })
})