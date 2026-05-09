$(document).ready(function() {
	$('.profile-block-link').on('click', function() {
		$link_id = $(this).attr('link_id');
		$link = $(this);
		bootbox.confirm('<strong>Are you sure you want to block user?</strong><br>', function (result) {
			if(result){
				$.ajax({
					url: "/user/block-link/"+ $link_id, 
					data: {},      
					success: function(response) {
					   if($.isNumeric(response)){
						  window.location.reload();
					   }
					},
					error: function(xhr, status, error) {
						console.error("Error:", error);
					}
				});
			}
		});
    });
 
});

$(document).ready(function() {	
	$('.request-link').on('click', function() {
		$link_id = $(this).attr('link_id');
		$link = $(this);
        $.ajax({
            url: "/user/request-link/"+ $link_id, 
            method: "POST",
            data: {},      
            success: function(response) {
			   bootbox.alert('Your request is now pending.');
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            }
        });
    });
});
 
