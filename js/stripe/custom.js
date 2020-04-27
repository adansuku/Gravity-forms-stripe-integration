var table, c;
jQuery(document).ready(function($) {
    if($('#user-subscriptions').length > 0) {
		$('#user-subscriptions').DataTable({
			"responsive": true,
			'pageLength': 6,
			"order": [[ 0, "desc" ]],
			"ordering": false,
			"info":     false,
			"bInfo":     false,
			"bLengthChange": false,
			"searching": false,
			"fnDrawCallback": function(oSettings) {
				if ($('#user-subscriptions tbody tr').length < 10) {
					$('.dataTables_paginate').hide();
				}
			}
		});
	}
	
	
	

	var sel = document.getElementById('input_14_14').value;
	console.log(sel);


	if (sel == "Ally"){
		jQuery('#input_14_7 option[value="supporter|0"]').hide();
		jQuery('#input_14_7 option[value="candidate|0"]').prop('selected', true);
	}
	
	if (sel == "Latinas in Tech"){
		jQuery('#input_14_7 option[value="candidate|0"]').hide();
		jQuery('#input_14_7 option[value="supporter|0"]').prop('selected', true);
	}
	
	if (sel == "Employer Recruiter Pro"){
		jQuery('#input_14_7 option[value="employer-recruiter-50|50"]').hide();
		jQuery('#input_14_7 option[value="candidate|0"]').prop('selected', true);
	}
	
	if (sel == "Employer Free"){
		jQuery('#input_14_7 option[value="employer-free|0"]').hide();
		jQuery('#input_14_7 option[value="candidate|0"]').prop('selected', true);
	}


	
} );