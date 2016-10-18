(function($) {
	var watched = ajaxuserfield.watched;
	$(document).ready(function(){
		var iframe = document.querySelector('iframe');
		if (iframe){
			var player = new Vimeo.Player(iframe);
			player.on('timeupdate', function(data) {
				if (data.percent >= 0.5 && !watched){
					watched = true;
					update_user_meta(ajaxuserfield.postID, true);
				}
			});
		};
		
		$(".watched-button").click(function(e){
			e.preventDefault();
			if (watched == "true"){
				update_user_meta(ajaxuserfield.postID, "false");
				$(".watched-button button").html("Mark Completed");
				$(".watched-button").removeClass("watched");
				watched = "false";
			}
			else {
				update_user_meta(ajaxuserfield.postID, "true");
				$(".watched-button button").html("Completed");
				$(".watched-button").addClass("watched");
				watched = "true";
			}
		})
	})
	
	function update_user_meta(postID, watched){
		$.ajax({
			url: ajaxuserfield.ajaxurl,
			type: 'post',
			data: {
				action: 'update_user_field',
				postID: postID,
				watched: watched
			},
			success: function( result ) {
				if (result == "true"){
					$(".watched-button button").html("Completed");
					$(".watched-button").addClass("watched");
				}
				else {
					$(".watched-button button").html("Mark Completed");
					$(".watched-button").removeClass("watched");
				}
			},
			error: function( result ) {
				alert( result );
			}
		})
	}
})(jQuery);