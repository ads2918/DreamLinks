 /* tags field */
if($('input[name=tags]').length){
	var input = document.querySelector('input[name=tags]');
	new Tagify(input,{
			pattern: /^[A-Za-z_1-9 ]{1,25}$/, // 
			maxTags: 15, // Limits the number of tags to 10
	});
}

if($('#dataTable').length){
	let table = new DataTable('#dataTable');
}
 
$(document).ready(function() {
    $('.accept-link').on('click', function() {
		$link_id = $(this).attr('link_id');
		$link = $(this);
        $.ajax({
            url: "/user/accept-link/"+ $link_id , // Your CI4 route
            method: "POST",
            data: {},      
            success: function(response) {
               if($.isNumeric(response)){
				   $link.parent().parent().hide('slow');
				   if(response == 0){
						$('#dropdownMenuButton2').hide('slow');
				   }else{
						$('.pending-link-count').html(response);  
				   }
			   }
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            }
        });
    });
	
	$('.block-link').on('click', function() {
		$link_id = $(this).attr('link_id');
		$link = $(this);
        $.ajax({
            url: "/user/block-link/"+ $link_id, 
            data: {},      
            success: function(response) {
               if($.isNumeric(response)){
				   response = response - 1;
				   $link.parent().parent().hide('slow');
				   if(response <= 0){
						$('#dropdownMenuButton2').hide('slow');
				   }else{
					   	
						$('.pending-link-count').html(response);  
				   }
			   }
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            }
        });
    });
	
	$('.decline-link').on('click', function() {
		$link_id = $(this).attr('link_id');
		$link = $(this);
        $.ajax({
            url: "/user/decline-link/"+ $link_id,  
            method: "POST",
            data: {},      
            success: function(response) {
               if($.isNumeric(response)){
				   $link.parent().parent().hide('slow');
				   if(response == 0){
						$('#dropdownMenuButton2').hide('slow');
				   }else{
						$('.pending-link-count').html(response);  
				   }
			   }
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            }
        });
    });
const toggler = document.querySelector('.navbar-toggler');
const secondaryMenu = new bootstrap.Collapse('#navbarCollapse', { toggle: false });

toggler.addEventListener('click', function () {
    secondaryMenu.toggle();
});	
});
 

 
