(function($) {
	var vimeoID = wpvimpostsadmin.vimeoID;
	$(document).ready(function(){
		$("#get-video-duration").click(function(e){
			e.preventDefault();
			$.ajax({
				url: 'https://api.vimeo.com/videos/180183508',
				type: 'get',
				success: function( result ) {
					console.log(result);
				},
				error: function( result ) {
					alert( result );
				}
			})	
		});
	})
})(jQuery);