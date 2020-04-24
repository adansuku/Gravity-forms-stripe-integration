var table, c;
jQuery(document).ready(function($) {
    if($('#user-subscriptions').length > 0) {
		$('#user-subscriptions').DataTable({
			"responsive": true,
			"columnDefs": [
				{ "width": "0%", "targets": 0, "visible": false }
			  ],
			
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
	if($('#admin-list').length > 0) {
		$('#admin-list').DataTable({
			"responsive": true,
			"columnDefs": [
				{ "width": "0%", "targets": 0, "visible": false },
				{ "width": "8%", "targets": 6},
				{ "width": "12%", "targets": 7},
				/*{ "targets": 1, "responsivePriority": 1 },
				{ "targets": 4, "responsivePriority": 5 },
				{ "targets": 6, "responsivePriority": 10 }*/
			  ],
			"order": [[ 0, "desc" ]],
			"ordering": false,
			"info":     false,
			"bInfo":     false,
			"bLengthChange": false,
			"searching": false,
			"fnDrawCallback": function(oSettings) {
				if ($('#admin-list tbody tr').length < 10) {
					$('.dataTables_paginate').hide();
				}
			}
		});
	}
	if($('#wpadmin-list').length > 0) {
		table = $('#wpadmin-list').DataTable({
			"responsive": true,
			"columnDefs": [
				{ "width": "0%", "targets": 0, "visible": false, "searchable": false },
				{ "targets": 1, "visible": false },
				{ "targets": 2, "searchable": false },
				{ "targets": 3, "searchable": false },
				{ "targets": 4, "searchable": false },
				{ "targets": 5, "searchable": false },
				{ "targets": 6, "searchable": false },
			  ],
			"order": [[ 0, "desc" ]],
			"ordering": false,
			"info":     false,
			"bInfo":     false,
			"bLengthChange": false,
			"searching": true,
			"fnDrawCallback": function(oSettings) {
				if ($('#wpadmin-list tbody tr').length < 10) {
					$('.dataTables_paginate').hide();
				}
				$("#wpadmin-list_filter").hide();
			},
			
		});
		$("#zzd_sort_forms").on( 'change', function () { 
			var val = $(this).val();			
			table.column(1).data().search( val ).draw();
		});
		
	}
	
	
	$(document).on("click", '.cancel_subscription', function(){
		
		var $this = jQuery(this); c = false;
		var go = cancel_at_end = false;
		
		if( confirm("Are you sure you want to cancel the subscription?") ) { 			
			process_cancellation($this);
		}
		
		
	});
	
	function process_cancellation($this) {
		
			
		if( $this.hasClass("cancelled") ) {
			return;
		}
		
		var $status_col = $this.parents(".gss_item").find(".status");
		var $parent = $this.parent();
		$parent.html("Please wait...");
		
		var data = {
			action: 'gss_cancel_subscription',
			eid: $this.attr('data-eid')
		};

		
		$.post(script_zzd.admin_url_zzd, data, function(response) {
			$(".resp").html(response);
			if( response == 1 ) {
				$parent.html("");
				console.log($parent);
				console.log($status_col);
				$status_col.html("Cancelled").addClass("cancelled").removeClass("active");
			}
		});
	}
	
} );