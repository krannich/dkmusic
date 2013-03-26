@layout('layout.master')
<?php 

// DUPLICATES

?>
@section('header')

{{HTML::script('/js/underscore.min.js')}}
{{HTML::script('/js/backbone.min.js')}}
<style>

#results_status {
	position: relative;
	padding-top: 39px;
}

#results_status::after {
	content: "Status";
	position: absolute;
	top: -1px;
	left: -1px;
	padding: 3px 7px;
	font-size: 12px;
	font-weight: bold;
	background-color: #fff;
	border: 1px solid #ddd;
	color: #9da0a4;
	-webkit-border-radius: 4px 0 4px 0;
	-moz-border-radius: 4px 0 4px 0;
	border-radius: 4px 0 4px 0;
}

#results_table tbody tr td:first-child {
	text-align: center;
}

#results_table tbody tr td:first-child a {
	text-decoration: none;
}


</style>

<script type="text/javascript">
function select_all() {
	$("#results_table > tbody > tr").addClass('selected');
    $('#results_table > tbody').find('tr').each(function(i, el) {
		var dk_data = eval('([' + $(this).attr('dk_data') + '])')[0];
		if ($.inArray(dk_data.id, dkbatch_data)>=0) {
			// do nothing
		} else {
			dkbatch_data.push(dk_data.id);
		}
	});
}

function delete_songs() {
	if ($('#results_table > tbody > tr').length == dkbatch_data.length) {
		bootbox.alert("You cannot delete all songs?");
	} else {
		bootbox.confirm("Do you really want to delete all selected files?", function(result){
			if(result) {
				$("#results_table > tbody > tr.selected").remove();
				do_batch('/duplicates/remove');
			} else {
				return;
			}
		});
	}
}




function create_results_table(header_titles, sorting) {
	
	var html = "";
	html += '<table id="results_table" class="tablesorter">';
	html += '<thead><tr>';
	
	$.each(header_titles, function(index, value) { 
		html += '<th>' + value + '</th>';
	});
	html += '</tr></thead>';
	html += '<tbody></tbody>';
	html += '</table>';
	
	$('#results').html(html);

	$("#results_table").tablesorter({
        widgets: ['zebra', 'stickyHeaders'],
        sortList: sorting,
        widgetOptions: { 
	    	stickyHeaders : 'tablesorter-stickyHeader', 
	    	stickyHeadersOffset : 40,
  		},
    });
	
}

function get_dupartisttitle() {
	create_results_table(['Artist', 'Title', 'Sum'], [[0,0]]);

	$('#results_table').tabledata( {
		"source"		: "/duplicates/dupartisttitle",
		"output"		: "artist,title,duplicates",
		"dk_data"		: "artist,title",
		"dk_options"	: {}
	});
	
}

function get_dupacoustids() {

	dkbatch_data = [];

	create_results_table(['', 'Artist', 'Title', 'kbit/s', 'Length', 'Size']);
	
	$('#results_table').before('<div id="results_status" class="well" style="height: 50px;overflow: auto;"></div>');

	$('#results_table').tabledata( {
		"source"		: "/duplicates/dup_acoustids",
		"output"		: "playbutton,artist,title,bitrate,length,size",
		"dk_data"		: "id,filename",
		"dk_options"	: {}

	});
	
	$('#results_table').bind('update', function() {
		$("#results_table tbody tr").unbind("dblclick");
		
		$("#results_table tbody tr").bind("click", function() {
			var dk_data = eval('([' + $(this).attr('dk_data') + '])')[0];
			$(this).toggleClass('selected');	
			if ($.inArray(dk_data.id, dkbatch_data)>=0) {
				dkbatch_data.splice( $.inArray(dk_data.id,dkbatch_data) , 1 );				
			} else {
				dkbatch_data.push(dk_data.id);
			}
		});
				
	});
	
	$('#results_table').before('<p><button class="btn btn-danger btn-max" onclick="delete_songs();">Delete selected songs</button></p>');	
	$('#results_table').after('<p>&nbsp;</p><p><button class="btn btn-danger btn-max" onclick="delete_songs();">Delete selected songs</button></p>');	

	$('#results_table').bind('update', function() {
		$('#results_table tbody a').bind('click', function(e){
			e.preventDefault();
		    var song = ($(this).attr('href'));
			$('.isPlaying').html('<i class="icon-play"></i>');
			if (isplaying==0 || songplaying!=song) {
				playsong(song);
				isplaying =1;
				songplaying = song;
				($(this).html('<i class="icon-stop"></i>'));
				($(this).addClass('isPlaying'));
			} else {
				$("#jquery_jplayer_1").jPlayer("stop");
				isplaying=0;
				songplaying = '';
				($(this).html('<i class="icon-play"></i>'));
			}
		return false;
		});
	});
	
}


function get_dupfingerprints() {

	dkbatch_data = [];

	create_results_table(['', 'Artist', 'Title', 'kbit/s', 'Length', 'Size']);
		
	$('#results_table').before('<div id="results_status" class="well" style="height: 50px;overflow: auto;"></div>');
	
	$('#results_table').tabledata( {
		"source"		: "/duplicates/dup_fingerprints",
		"output"		: "playbutton,artist,title,bitrate,length,size",
		"dk_data"		: "id,filename",
		"dk_options"	: {}
	});
	
	$('#results_table').bind('update', function() {
		$("#results_table tbody tr").unbind("dblclick");
		
		$("#results_table tbody tr").bind("click", function() {
			var dk_data = eval('([' + $(this).attr('dk_data') + '])')[0];
			$(this).toggleClass('selected');	
			if ($.inArray(dk_data.id, dkbatch_data)>=0) {
				dkbatch_data.splice( $.inArray(dk_data.id,dkbatch_data) , 1 );				
			} else {
				dkbatch_data.push(dk_data.id);
			}
		});
				
	});
	
	$('#results_table').before('<p><button class="btn btn-danger btn-max" onclick="delete_songs();">Delete selected songs</button></p>');	
	$('#results_table').after('<p>&nbsp;</p><p><button class="btn btn-danger btn-max" onclick="delete_songs();">Delete selected songs</button></p>');	

	$('#results_table').bind('update', function() {
		$('#results_table tbody a').bind('click', function(e){
			e.preventDefault();
		    var song = ($(this).attr('href'));
			$('.isPlaying').html('<i class="icon-play"></i>');
			if (isplaying==0 || songplaying!=song) {
				playsong(song);
				isplaying =1;
				songplaying = song;
				($(this).html('<i class="icon-stop"></i>'));
				($(this).addClass('isPlaying'));
			} else {
				$("#jquery_jplayer_1").jPlayer("stop");
				isplaying=0;
				songplaying = '';
				($(this).html('<i class="icon-play"></i>'));
			}
		return false;
		});
	});
	
}

var dkbatch_data = new Array();
var dkbatch_max = 0;
var dkbatch_index = 0;

function init_batch() {
	dkbatch_data = [];
    dkbatch_index = 0;
    $.each($('#results_table tbody tr'), function (e) {
		var dk_data = eval('([' + $(this).attr('dk_data') + '])')[0];
   		dkbatch_data.push(dk_data.id);
    });
}


function do_batch(url, title) {
	title = typeof title !== 'undefined' ? title : '';
	
	var file = dkbatch_data.shift();
	$.ajax({
       url: url,
       data: {"file" : file},
       async: true,
       success: function(msg) { 
       
            $("#results_status").prepend(msg);
            $("#results_progress .bar").css("width", ((dkbatch_index+1) * 100 / dkbatch_max) + "%");
          	
          	if (dkbatch_data.length <= 0 ) {
	          	$.pnotify({
	            	text: title + ' successfully finished!',
	            	type: 'success'
	            });
	            return;
          	}
          	
          	dkbatch_index++;
            do_batch(url);
          	
       }
   });
	
}

function rescansongs() {
	init_batch();	
	do_batch('/duplicates/rescan', 'Rescan');	
}


$(document).ready(function() {
	
	$(".getdupacoustids").bind('click', function() {
		get_dupacoustids();
	});

	$(".getdupfingerprints").bind('click', function() {
		get_dupfingerprints();
	});

	$(".getdupartisttitle").bind('click', function() {
		get_dupartisttitle();
	});


	$(document).ajaxStart(function() {
	        $('body').addClass("loading");
	});
	
	$(document).ajaxStop(function() {
	        $('body').removeClass("loading");
    });
    
    
  
});

</script>

@endsection


@section('content')

	 <div class="container">
	 
	 <div class="row">
			<div class="span12">
				<div class="well" style="padding-bottom: 0;">
					<div class="row-fluid">
				
						<div class="span3">
							<h2 class="normal">Duplicates</h2>
						</div>
						<div class="span9">
						</div>											
					</div>					
				</div>
			</div>
		</div>
	 
		<div class="row">
			<div class="span12">	        
				<div role="main" id="main">
					<div class="well">
						<div class="btn-group" data-toggle="buttons-radio">
							<button type="button" class="btn getdupacoustids">Duplicate AcoustIDs</button>
							<button type="button" class="btn getdupfingerprints">Duplicate Fingerprints</button>
							<button type="button" class="btn getdupartisttitle">Duplicate Artist/Title</button>
						</div>
						<p>&nbsp;<br /><span class="label label-warning">NOTE</span> To increase system performance, you will only see the most recent 100 results in order of decreasing frequency.</p>

					</div>
					<div id="results"></div>
				</div>
			</div>
		</div>
	 </div>

	
@endsection