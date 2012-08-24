@layout('layout.master')

@section('header')

{{HTML::script('/js/jquery.jplayer.min.js')}}
{{HTML::script('/js/dk.jplayer.min.js')}}

<link href="/css/skin/jplayer.blue.monday.css" rel="stylesheet" type="text/css" />

<style>
#results td:first-child {
	width: 30px;
}

#results td:last-child {
	white-space: nowrap;
	text-align: right;
}

#results > thead > tr > th:first-child > div::before {
	content:'';
	margin: 0;
}
</style>

<script>

$(document).ready(function () {

	$('#results').tabledata( {
		"source"		: "/home/search",
		"output"		: "playbutton,artist,title,bitrate,length,size",
		"dk_data"		: "id,filename",
		"dk_options"	: {

			"edit" : {
				'standard' : 1,
			}
		}
	});

    
    $("#results").tablesorter({
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
    
	$('#results').bind('update', function() {
		$('#results a').bind('click', function(e){
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
	
	
	$('#myTab a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	});
	
	
});
</script>


@endsection

@section('content')


	 <div class="container-fluid">
		<div class="row-fluid">
		
			<div class="sidebar-nav left">
				<div class="well">		
					<h2 class="normal">Modifier</h2>
				
					<hr />
					
					<div id="jquery_jplayer_1" class="jp-jplayer"></div>
						<div id="jp_container_1" class="jp-audio">
							<div class="jp-type-single">
								<div class="jp-gui jp-interface">
									<ul class="jp-controls">
										<li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
										<li><a href="javascript:;" class="jp-pause" tabindex="1" style="display: none; ">pause</a></li>
										<li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
										<li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
										<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute" style="display: none; ">unmute</a></li>
										<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
									</ul>
									
									<div class="jp-volume-bar">
										<div class="jp-volume-bar-value" style="width: 100%; "></div>
									</div>
									<div class="jp-time-holder">
										<div class="jp-current-time">00:00</div>
										<div class="jp-duration">00:00</div>

										<ul class="jp-toggles">
											<li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat" style="display: block; ">repeat</a></li>
											<li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off" style="display: none; ">repeat off</a></li>
										</ul>
									</div>
								</div>

							<div id="jp_playlist" class="jp-playlist">&nbsp;</div>
							<div class="jp-progress">
								<div class="jp-seek-bar" style="width: 100%; ">
									<div class="jp-play-bar" style="width: 0%; "></div>
								</div>
							</div>
						</div>
					</div>


				</div>
			</div>
			
			<div class="content fixed-270">	        
				<div role="main" id="main">

					<div class="well">
						{{Form::open('/modifier', 'get', array('id'=>'search', 'class'=>"form-horizontal")  );}}
							<fieldset>

								<h2 class="normal">Search filter</h2>
								{{ Form::select('search_field[]', 	array('filename' => 'Filename', 'artist' => 'Artist', 'title' => 'Title'), '', array('class' => 'input-small') )}}
								{{ Form::text('search_string[]' 	, '', array('placeholder' => 'Search term', 'autocomplete' => 'off', 'class' => 'input-xlarge')); }}
								<button class="btn btn-success">Go</button>
							</fieldset>
						{{Form::close()}}
					</div>
								
					<div class="well">
						{{Form::open('/modifier', 'get', array('id'=>'search', 'class'=>"form-horizontal")  );}}
							<fieldset>
								
								<ul class="nav nav-tabs">
									<li class="active"><a href="#replace" data-toggle="tab">Replace</a></li>
									<li><a href="#rename" data-toggle="tab">Rename</a></li>
								</ul>
								

								<div class="tab-content">
									<div class="tab-pane active" id="replace">
										<h2 class="normal">Replace operations</h2>
										{{ Form::select('operation_field[]', 	array('filename' => 'Filename', 'artist' => 'Artist', 'title' => 'Title'), '', array('class' => 'input-small') )}}
										{{ Form::text('operation_string[]' 	, '', array('placeholder' => 'Needle', 'autocomplete' => 'off', 'class' => 'input-medium')) }}
										{{ Form::text('operation_with[]' 	, '', array('placeholder' => 'New String', 'autocomplete' => 'off', 'class' => 'input-medium')); }}
										<button class="btn"><i class="icon-plus"></i></button>
										<button class="btn btn-success">Go</button>
									</div> 								
									
									<div class="tab-pane" id="rename">
										<h2 class="normal">Rename operations</h2>
										{{ Form::select('operation_field[]', 	array('filename' => 'Filename', 'artist' => 'Artist', 'title' => 'Title'), '', array('class' => 'input-small') )}}
										{{ Form::text('operation_string[]' 	, '', array('placeholder' => 'New string', 'autocomplete' => 'off', 'class' => 'input-xlarge')); }}
										<button class="btn btn-success">Go</button>
									</div>
								</div>
								
																
							</fieldset>
						{{Form::close()}}
					</div>
	
					<table id="results" class="tablesorter">
						<thead>
							<tr>
								<th></th>
								<th>Artist</th>
								<th>Title</th>
								<th>kbit/s</th>
								<th>Length</th>
								<th>Size</th>
							</tr>
						</thead>
					
						<tbody></tbody>

					</table>

	        	</div>
	        </div>
	        
		</div>
    </div>
	
@endsection
