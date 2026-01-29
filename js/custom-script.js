jQuery(function($) {
    $('.custom-vote-btn').on('click', function(e) {
        e.preventDefault();
        var $button = $(this);
        var postId = $button.data('post-id');
        var actionType = $button.data('action');
        var data = {
            'action': 'process_post_vote',
            'post_id': postId,
            'vote_type': actionType
        };
        $.post(customscript.ajaxurl, data, function(response) {
            if (response.success) {
                $('#' + actionType + '-count-' + postId).text(response.data.new_count);
                $button.addClass('voted').attr('disabled', true);
            } else {
                console.log(response.data.message);
            }
        });
    });
});



jQuery(document).ready(function($){
    $('.wp-like-btn').on('click', function(e){
        e.preventDefault(); 
        var $btn = $(this);
        var post_id = $btn.data('postid'); 
        $.ajax({
            url: customscript.ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_post_like',
                post_id: post_id
            },
            success: function(count) {
                $btn.next('.wp-like-count').text(count);
            }
        });
    });
});


jQuery(document).ready(function($){
    $('body').on('click','.aj_thumbsup',function(event){
        event.preventDefault();
        var OBJ = $(this);
        if(OBJ.hasClass('recorded')) return;
        var id = $(this).data('id'); 
        var data_arg = {};
        data_arg['id'] = id;
        data_arg['action'] = 'aj_like_post'; 
        $.ajax({
            beforeSend: function(){},
            type: 'POST',
            url:customscript.ajaxurl,
            data: data_arg,dataType:'json',
            success:function(data){
                OBJ.siblings('span').html( data.new_count); 
                OBJ.addClass('recorded');
                OBJ.find('i').removeClass('far').addClass('fas'); 
            }
        });
    });
});



jQuery( document ).on( 'click', '.pt-like-it', function() {
    var post_id = jQuery(this).find('.like-button').attr('data-id');
    var nonce = jQuery(this).find('.like-button').attr("data-nonce");
 
    jQuery.ajax({
        url : customscript.ajax_url,
        type : 'post',
        data : {
            action : 'pt_like_it',
            post_id : post_id,
            nonce : nonce
        },
        success : function( response ) {
            jQuery('#like-count-'+post_id).html( response );
        }
    });
      
    return false;
})