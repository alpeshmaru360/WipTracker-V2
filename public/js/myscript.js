var totalItems = $('.carousel-item').length;
var currentIndex = $('div.active').index() + 1;
$('.num').html(''+currentIndex+'/'+totalItems+'');

$('#carouselExampleControls').carousel({
    interval: 2000
});

$(document).ready(function(){
    
    var totalItems = $('.item').length;
    var currentIndex = $('div.item.active').index() + 1;

    down_index = 0;
    $('.num').html('0'+currentIndex+'');

        $(".next").click(function(){
        currentIndex_active = $('div.item.active').index() + 2;
        if (totalItems >= currentIndex_active)
        {
            down_index= $('div.item.active').index() + 2;
            $('.num').html('0'+currentIndex_active+'');
        }
    });

    $(".prev").click(function(){
        down_index=down_index-1;
        if (down_index >= 1 )
        {
            $('.num').html('0'+down_index+'');
        }
    });
});