$(document).ready(function() {
	var currentURL = window.location.href;
    new DataTable('.blockTable', {
		searching: false, 
		paging: false, 
		responsive: true,
	 
        // processing: true,  
        // serverSide: true, 
        // ajax: {
            // url: currentURL, 
            // type: 'POST' 
        // },
		// initComplete: function(settings, json) {
            // console.log("Data after DataTables processing:", json);
        // },
        // columns: [
			// { data: 'date' },
            // { data: 'title' },
            // { data: 'full_description' },
			// { data: 'tags' },
			// { data: 'links' },
        // ],
		// "columnDefs": [
            // {
                // "targets": [3,4], // Target columns 0 (first) and 2 (third)
                // "orderable": false // Disable sorting for these columns
            // },
			// {
				// "targets": [0,1,2],
				// "orderSequence": ['asc', 'desc'],
			// }
        // ],
		// pageLength: 10, // Default rows per page
		 
    });
});

$(".unblock").click(function() {
	$href = $(this).attr('href');
	bootbox.confirm('<strong>Are you sure you want to unblock dreamer?</strong><br>', function (result) {
		if(result){
			window.location = $href;
		}
	});
});
