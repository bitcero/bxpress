/*!
 * bXpress Forums (http://xoopsmexico.net)
 * Copyright 2009-2015 © Eduardo Cortés
 * Licensed under GPL v2 (http://www.gnu.org/licenses/gpl-2.0.html)
 */
$(".bxpress-posts-list a[data-toggle='tooltip']").tooltip();

/**
 Likes for posts
 */
$(".post-item .like-this-post").click( function(){

    var id = $(this).data('post-id');

    if ( undefined == id )
        return false;

    var control = this;

    if ( $(this).parents("#p"+id).length <= 0 )
        return false;

    $(this).find(".fa").addClass('clicked');

    var params = {
        BXTOKEN_REQUEST: $("#bxpress-token").val(),
        id: id,
        action: 'like'
    };

    $.post('likes.php', params, function( response ){

        if ( 1 == response.error ){
            var notifier = '<div class="notifier-blocker"></div>';
            notifier += '<div class="notifier-content"><button type="button" class="close">&times;</button> ' + response.message;
            notifier += '</div>';

            if ( '' != response.token )
                $("#bxpress-token").val( response.token );

            $("body").append(notifier).css('overflow', 'hidden');
            $(control).find(".fa").removeClass('clicked');
        }

        if ( $("#p" + response.data.post).length <= 0 )
            return false;

        if ( response.data.likes > 0 )
            $("#p" + response.data.post + " .like-this-post .fa").removeClass('text-muted');
        else
            $("#p" + response.data.post + " .like-this-post .fa").addClass('text-muted');


        $("#p" + response.data.post + " .likes-counter").html(response.data.likes);
        $("#p" + response.data.post + " .fa").removeClass('clicked');

        if ( '' != response.token )
            $("#bxpress-token").val( response.token );

        if ( 'remove' == response.data.action ){

            // Remove the user icon
            var userIcon = $("#p" + response.data.post + " .post-likes > a[data-user='" + response.data.uid + "']");
            if ( undefined != userIcon )
                $(userIcon).fadeOut(300, function(){ $(this).remove();});

        } else {

            var users = $("#p" + response.data.post + " .post-likes a");

            if ( users.length >= 3 )
                $(users[0]).fadeOut(300, function(){
                    $(this).remove();
                });

            users = $("#p" + response.data.post + " .post-likes a");

            var html = '<a style="display: none;" href="' + response.data.info + '" data-user="' + response.data.uid + '" data-toggle="tooltip"' +
                ' title="' + response.data.name + '"><img src="' + response.data.avatar + '" alt="' + response.data.name + '">' +
                '</a>';

            if ( users.length > 0 ){

                $(users[0]).before(html);

                if ( response.data.likes > 3 ){
                    $("#p" + response.data.post + " .post-likes > .likes-more").html(bxpressLang.likes_more.replace("%u",response.data.likes-3));
                } else {
                    $("#p" + response.data.post + " .post-likes > .likes-more").html('');
                }

            } else {

                $("#p" + response.data.post + " .post-likes").append(bxpressLang.liked_by + '\n' + html);
                $("#p" + response.data.post + " .post-likes > .likes-more").html('');

            }

            $("#p" + response.data.post + " .post-likes > a[data-user='" + response.data.uid + "']").fadeIn('300');

        }


    }, 'json');

    return false;

} );

$("body").on('click', '.notifier-content .close', function(){

    $(this).parents('.notifier-content').fadeOut(300, function(){

        $(".notifier-blocker").fadeOut(300, function(){
            $(".notifier-content").remove();
            $(".notifier-blocker").remove();
            $("body").css('overflow', 'auto');
        });

    });

});