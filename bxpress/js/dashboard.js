$(document).ready(function(){

    $("#activity-options li").click(function(){
        var el = $("#activity-options li.pressed");
        el.removeClass("pressed");
        $("#"+el.attr("class")).slideUp('fast');
        
        $("#"+$(this).attr("class")).slideDown('fast');
        $(this).addClass("pressed");
        
    });
    
    drawVisualization($("activity").width(), 270);
        
});