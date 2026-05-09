$(document).ready(function() {
	var currentURL = window.location.href;
    dataTableVar = new DataTable('#dreamsTable', {
        processing: true,  
        serverSide: true, 
	responsive: true,
        ajax: {
            url: currentURL, 
            type: 'POST' 
        },
		initComplete: function(settings, json) {
            // console.log("Data after DataTables processing:", json);
        },
        columns: [
			{ data: 'date' },
            { data: 'title' },
            { data: 'full_description' },
			{ data: 'tags' },
			{ data: 'visibility' },
			{ data: 'links' }
        ],
		"columnDefs": [
            {
                "targets": [3,5], // Target columns 0 (first) and 2 (third)
                "orderable": false // Disable sorting for these columns
            },
			{
				"targets": [0,1,2,4],
				"orderSequence": ['asc', 'desc'],
			}
        ],
		pageLength: 10, // Default rows per page
		 
    });
	
	$(".hide-show-ai-prompt").click(function() {
		if($('.recent-ai-prompt-wrapper').css('display') === 'none') {
			$('.recent-ai-prompt-wrapper').show();
			$(this).addClass('bi bi-eye-slash-fill');
			$(this).removeClass('bi-eye-fill');
		}else{
			$('.recent-ai-prompt-wrapper').hide();
			$(this).addClass('bi-eye-fill');
			$(this).removeClass('bi-eye-slash-fill');
		}
	});
 
	$(document).on('click', '.delete-dream', function() { 
		$href = $(this).attr('href');
		$title = $(this).attr('title');
		bootbox.confirm('<strong>Are you sure you want to delete dream?</strong><br>', function (result) {
			if(result){
				$.get($href, function(data){
					dataTableVar.ajax.reload(); //refresh datatable
				});
			}
		});
	});
});
 
