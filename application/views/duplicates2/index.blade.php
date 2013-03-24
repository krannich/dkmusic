@layout('layout.master')
<?php 

// DUPLICATES

?>
@section('header')

{{HTML::script('/js/underscore.min.js')}}
{{HTML::script('/js/backbone.min.js')}}

{{HTML::script('/js/jquery.jplayer.min.js')}}
{{HTML::script('/js/dk.jplayer.min.js')}}

{{HTML::style('/css/jplayer.css')}}

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

function show_files(route, value) {
	
	dkbatch_data = [];

	create_results_table(['' , 'Artist', 'Title', 'kbit/s', 'Length', 'Type', 'Size'], [[1,0]]);
		
	if (route=="acoustid") {
		$('#results_table').before('<p><img src="/img/icon_acoustid.png" /> <span style="color:#900; font-weight: bold; line-height: 20px;vertical-align: middle;">' + value + '</span></p>');
	} 

	$('#results_table').before('<div id="results_status" class="well" style="height: 50px;overflow: auto;"></div>');
	$('#results_table').before('<p><button class="btn" onclick="rescansongs();">Rescan songs</button></p>');
	
	$('#results_table').tabledata( {
		"source"		: "/duplicates/showfiles_" + route,
		"data"			: {"value" : value},
		"output"		: "playbutton,artist,title,bitrate,length,type,size",
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
	
	$('#results_table').after('<p>&nbsp;</p><p><button class="btn btn-max" onclick="select_all();">Select all songs</button></p><p><button class="btn btn-danger btn-max" onclick="delete_songs();">Delete selected songs</button></p>');	

	$('#results_table').bind('update', function() {
		$('#results_table tbody a').bind('click', function(e){
			e.preventDefault();
		    var song = ($(this).attr('href'));
			$('.isPlaying').html('<img src="img/but_play.png" />');
			if (isplaying==0 || songplaying!=song) {
				playsong(song);
				isplaying =1;
				songplaying = song;
				($(this).html('<img src="img/but_stop.png" />'));
				($(this).addClass('isPlaying'));
			} else {
				$("#jquery_jplayer_1").jPlayer("stop");
				isplaying=0;
				songplaying = '';
				($(this).html('<img src="img/but_play.png" />'));
			}
		return false;
		});
	});
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
	create_results_table(['acoustID' , 'Sum'], [[1,1]]);

	$('#results_table').tabledata( {
		"source"		: "/duplicates/dupacoustids",
		"output"		: "acoustid_and_filename,duplicates",
		"dk_data"		: "acoustid",
		"dk_options"	: {}
	});
	
	$('#results_table').bind('update', function() {
		$("#results_table tbody tr").bind("dblclick", function() {
			var dk_data = eval('([' + $(this).attr('dk_data') + '])')[0];
			show_files('acoustid', dk_data.acoustid);
	    });
	});
	
}


function get_dupfingerprints() {

	/*
	create_results_table(['Fingerprint' , 'Sum'],[[1,1]]);

	$('#results_table').tabledata( {
		"source"		: "/duplicates/dup_fingerprints",
		"output"		: "fingerprint_output,duplicates",
		"dk_data"		: "fingerprint",
		"dk_options"	: {}
	});
	
	
	$('#results_table').bind('update', function() {
		$("#results_table tbody tr").bind("dblclick", function() {
			var dk_data = eval('([' + $(this).attr('dk_data') + '])')[0];
			show_files('fingerprint', dk_data.fingerprint);
	    });
	});
	*/
	
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
			$('.isPlaying').html('<img src="img/but_play.png" />');
			if (isplaying==0 || songplaying!=song) {
				playsong(song);
				isplaying =1;
				songplaying = song;
				($(this).html('<img src="img/but_stop.png" />'));
				($(this).addClass('isPlaying'));
			} else {
				$("#jquery_jplayer_1").jPlayer("stop");
				isplaying=0;
				songplaying = '';
				($(this).html('<img src="img/but_play.png" />'));
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


	$("body").on({
	    ajaxStart: function() {
	        $(this).addClass("loading");
	    },
	    ajaxStop: function() {
	        $(this).removeClass("loading");
	    }    
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
				
						<div class="span9">
							<h2 class="normal pull-left" style="margin-right: 75px;">Duplicates</h2>

							<div>
								<p><button class="btn getdupacoustids">Duplicate AcoustIDs</button></p>
								<p><button class="btn getdupfingerprints">Duplicate Fingerprints</button></p>
								<p><button class="btn getdupartisttitle">Duplicate Artist/Title</button></p>
							</div>
							
						</div>
						
						<div class="span3">
							@include('partials._jp-player')
						</div>
					
					</div>					
				</div>
			</div>
		</div>

	 
	 
	 
		<div class="row">
			<div class="span12">	        
				<div role="main" id="main">
					<div id="results" class="well">
						<p><span class="label label-warning">NOTE</span> You will only see the most recent 50 results in order of decreasing frequency.<br />Double-click on a result to see which songs are involved.</p>
					</div>
				</div>
			</div>
		</div>
	 </div>

	
@endsection