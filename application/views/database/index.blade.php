@layout('layout.master')



@section('header')
<style>
#results_table td:last-child {
	white-space: nowrap;
	text-align: right;
}

#results_table > thead > tr > th:first-child > div::before {
	content:'';
	margin: 0;
}
</style>

<script>

var dkbatch_data = new Array();
var dkbatch_max = 0;
var dkbatch_index = 0;

var dkqueue;

function init_batch() { 
	dkbatch_data = [];
	$("#results_table > tbody input:checkbox:checked").each(function() {
		dkbatch_data.push($(this).val());
	});
}

function do_batch(url) {
	var folder = dkbatch_data.shift();
	console.log(folder);
   	$.ajax({
       url: url,
       data: {"folder" : folder},
       async: true,
       success: function(msg) {
       		if (folder == '#') {
	           	$("#folder_N").html(' <span class="badge badge-success"><i class="icon-ok"></i></span>');
       		} else {
	           	$("#folder"+folder).html(' <span class="badge badge-success"><i class="icon-ok"></i></span>');
           	}
           	if (dkbatch_data.length > 0 ) {
            	$("#results").prepend(msg);
           	} else {
            	$("#results").prepend(msg);
            	
            	$.pnotify({
	            	text: 'Scan for new files finished!',
	            	type: 'success'
	            });
            	
            	return;
           	}
           	
            do_batch(url);
            
       }
   });
}


function init_table() {
	$('#results').html("");
	$('#results_table').tabledata( {
		"source"		: "/database/databaseinfo",
		"output"		: "folder_checkbox,foldername,files_count,acoustid_fingerprint_count,acoustid_acoustid_count,folder_size",
		"dk_data"		: "foldername",
		"dk_options"	: {},
	});
	
	$("#results_table").tablesorter({
        widgets: ['zebra', 'stickyHeaders'],
        sortList: [[1,0]],
        headers: {
	  		0: {sorter: false},
  		},
        widgetOptions: { 
	    	stickyHeaders : 'tablesorter-stickyHeader', 
	    	stickyHeadersOffset : 40,
  		},
  		
    });
	
}


$(document).ready(function() {
	
	$("body").on({
	    ajaxStart: function() {
	        $(this).addClass("loading");
	    },
	    ajaxStop: function() {
	        $(this).removeClass("loading");
	    }    
    });

	$('#results_table').bind('update', function() {
	    $("#results_table > thead input:checkbox").click(function(){
		      if($(this).val()==0){
		        $('#results_table > tbody input:checkbox').attr("checked","checked");
		        $(this).val("1");
		        
		      } else{
		        $('#results_table > tbody input:checkbox').removeAttr("checked");
				$(this).val("0");
				
		      }
		});
	});    
   
	$("button.rescan").bind('click', function(e) {
       	$('#results_table > tbody td span[id^=folder]').html("");
		var checked = $("input[type=checkbox]:checked").length;
		if (checked >= 1) {
			init_batch();
			$('#results').html("");
			do_batch("/database/rescan");
		}
	});


    $("button.scannewqueue").bind('click', function(e) {
    	$('#results_table > tbody td span[id^=folder]').html("");
		var checked = $("input[type=checkbox]:checked").length;
		if (checked >= 1) {
		
			dkqueue = $.jqmq({
				delay: -1,
				callback: function( id ) {
					var q = this;
					
					$.getJSON( '/database/scanfornewfiles', { folder: id }, function(data) {
						if ( data.success ) {
						
							if (id == '#') {
								$("#folder_N").html(' <span class="badge badge-success"><i class="icon-ok"></i></span>');
							} else {
								$("#folder"+id).html(' <span class="badge badge-success"><i class="icon-ok"></i></span>');
							}
							
							$.each(data.files, function(i,file){
								var content = '<p>' + file + ' not in database and moved to inbox.</p>';
								$("#results").prepend(content);
							});
							
						}

						q.next( !data.success );
						
					});
					
				},
				complete: function(){
					$.pnotify({
		            	text: 'Scan for new files finished!',
	            		type: 'success'
	            	});
	            }
			});

			dkqueue.clear();
			$("#results_table > tbody input:checkbox:checked").each(function() {
				dkqueue.add($(this).val());
			});
			
			$('#results').html("");
			
			dkqueue.start();
			
		}
	});

	$("button.scanremovedqueue").bind('click', function(e) {
    	$('#results_table > tbody td span[id^=folder]').html("");
		var checked = $("input[type=checkbox]:checked").length;
		if (checked >= 1) {
		
			dkqueue = $.jqmq({
				delay: -1,
				callback: function( id ) {
					var q = this;
					
					$.getJSON( '/database/scanforremovedfiles', { folder: id }, function(data) {
						if ( data.success ) {
						
							if (id == '#') {
								$("#folder_N").html(' <span class="badge badge-success"><i class="icon-ok"></i></span>');
							} else {
								$("#folder"+id).html(' <span class="badge badge-success"><i class="icon-ok"></i></span>');
							}
							
							$.each(data.files, function(i,file){
								var content = '<p>Entry for ' + file + ' has been removed from DB.</p>';
								$("#results").prepend(content);
							});
							
						}

						q.next( !data.success );
						
					});
					
				},
				complete: function(){
					$.pnotify({
		            	text: 'Scan for removed files finished!',
	            		type: 'success'
	            	});
	            }
			});

			dkqueue.clear();
			$("#results_table > tbody input:checkbox:checked").each(function() {
				dkqueue.add($(this).val());
			});
			
			$('#results').html("");
			
			dkqueue.start();
			
		}
	});
	
	
	$("button.renamequeue").bind('click', function(e) {
    	$('#results_table > tbody td span[id^=folder]').html("");
		var checked = $("input[type=checkbox]:checked").length;
		if (checked >= 1) {
		
			dkqueue = $.jqmq({
				delay: -1,
				callback: function( id ) {
					var q = this;
					
					$.getJSON( '/database/renamefiles', { folder: id }, function(data) {
						if ( data.success ) {
						
							if (id == '#') {
								$("#folder_N").html(' <span class="badge badge-success"><i class="icon-ok"></i></span>');
							} else {
								$("#folder"+id).html(' <span class="badge badge-success"><i class="icon-ok"></i></span>');
							}
							
							$.each(data.files, function(i,file){
								$("#results").prepend(file);
							});
							
						}

						q.next( !data.success );
						
					});
					
				},
				complete: function(){
					$.pnotify({
		            	text: 'Scan for new files finished!',
	            		type: 'success'
	            	});
	            }
			});

			dkqueue.clear();
			$("#results_table > tbody input:checkbox:checked").each(function() {
				dkqueue.add($(this).val());
			});
			
			$('#results').html("");
			
			dkqueue.start();
			
		}
	});
	
	
	
	
	
	init_table();

});



</script>

@endsection

@section('content')

	 <div class="container">
		<div class="row">
		
			<div class="span3">
				<div class="well">	
					<h2 class="normal">Database</h2>	
					<hr />
					<p><button class="btn btn-max scannewqueue">New songs</button></p>
					<p><button class="btn btn-max scanremovedqueue">Removed songs</button></p>
					<hr />
					<p><span class="label label-warning">Attention!</span> Handle with care!</p>
					<p><button class="btn btn-max btn-warning rescan">Rescan fingerprint</button></p>
					<p><button class="btn btn-max btn-warning renamequeue">Rename modified songs</button></p>
				</div>
			</div>
			
			<div class="span9">	        
				<div role="main" id="main">

					<div class="well">
					
						<div id="results"></div>
						
						<table id="results_table" class="tablesorter">
							<thead>
								<tr>
									<th style="text-align: left;"><input type="checkbox" name="check_all" value="0" /> All</th>
									<th><img src="/img/icon_folder.png"></th>
									<th>Files</th>
									<th><img src="/img/icon_fingerprint.png"></th>
									<th><img src="/img/icon_acoustid.png"></th>
									<th>Size</th>
								</tr>										
							</thead>
							<tbody></tbody>
						</table>
											
					</div>
				</div>
			</div>
		</div>
	 </div>



	
	
	
@endsection