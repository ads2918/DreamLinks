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
               if(response){
				   $link.parent().html('<i title="Link request pending" title="Link request pending" class="bi bi-clock larger-icon link-pending"></i>');
			   }
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            }
        });
    });
});


$(document).ready(function() {
	var currentURL = window.location.href;
    new DataTable('#linkSearch', {
	responsive: true,
        processing: true,  
        serverSide: true, 
		searching: false,
        ajax: {
            url: currentURL, 
            type: 'POST' 
        },
		initComplete: function(settings, json) {
            console.log("Data after DataTables processing:", json);
        },
        columns: [
			{ data: 'username' },
        ],
		"columnDefs": [
         
			{
				"targets": [0],
				"orderSequence": ['asc', 'desc'],
			}
        ],
		pageLength: 10, // Default rows per page
		 
    });
});
 
 
