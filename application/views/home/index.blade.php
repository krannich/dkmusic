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

var songs_menu = {

    'column'        :   0,
    'position'      :   'after',

    'menu_items'    : {

        'edit' : {
            'url'           :   '/songs/edit/',
            'title'         :   'Bearbeiten',
            'icon'          :   'icon-pencil icon-big',
        },
        
    },
};

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
    
  
	$('#results').tablemenu(songs_menu);
	
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

});
</script>


@endsection

@section('content')


	 <div class="container-fluid">
		<div class="row-fluid">
		
			<div class="sidebar-nav left">
				<div class="well">		
					<h2 class="normal">Music library</h2>
					<table style="width: 100%">
						<tr>
							<td>Total files</td>
							<td class="tdr"><strong>{{Librarysong::count_all()}}</strong></td>
						</tr>
						<tr>
							<td>with AcoustID:</td>
							<td class="tdr">{{Librarysong::count_all_with_acoustid()}}</td>
						</tr>
						<tr>
							<td>with AcoustFingerprint:</td>
							<td class="tdr">{{Librarysong::count_all_with_acoustid_fingerprint()}}</td>
						</tr>					
						<tr>
							<td>Total disk space:</td>
							<td class="tdr">{{Librarysong::count_folder_size_of()}}</td>
						</tr>
					</table>	
					<hr />	
					<h2 class="normal">Inbox</h2>
					<table style="width: 100%">
						<tr>
							<td>Inbox</td>
							<td class="tdr">{{dkFolder::count_files_in(dkmusic_inbox)}}</td>
						</tr>
						<tr>
							<td>Convert m4a->mp3:</td>
							<td class="tdr">{{dkFolder::count_files_in(dkmusic_internal_convert)}}</td>
						</tr>
						<tr>
							<td>Missing data:</td>
							<td class="tdr">{{dkFolder::count_files_in(dkmusic_internal_missingdata)}}</td>
						</tr>
						<tr>
							<td>Duplicates:</td>
							<td class="tdr">{{dkFolder::count_files_in(dkmusic_output_duplicates)}}</td>
						</tr>
						
						<tr>
							<td>Trash:</td>
							<td class="tdr">{{dkFolder::count_files_in(dkmusic_trash)}}</td>
						</tr>

						<tr><td colspan="2"><hr /></td></tr>
						<tr>
							<td>Notordner:</td>
							<td class="tdr">{{dkFolder::count_files_in(dkmusic_output_notordner)}}</td>
						</tr>
					</table>
					
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
						{{Form::open('', 'get', array('id'=>'results_form', 'class'=>"form-horizontal")  );}}
							<fieldset>

								{{ Form::text('searchstring' , '', array('placeholder' => 'Search', 'autocomplete' => 'off')); }}
								<span class="help-inline">phil: search songs that begin with "phil" (case is ignored).<br />
									*phil: search songs that contain "phil" (case is ignored).</span>
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

	

	<?php
	//$songs = Librarysong::find_duplicates();

	//$songs = DB::table('library_metadata')->where_not_null("acoustfingerprint")->distinct('acoustfingerprint')->group_by('acoustfingerprint')->count('acoustfingerprint');

	//$song = Librarysong::get_by_acoustfingerprint('AQABz0kSSUqSTGrgEweO44QPnzhwHCcMHweO44Th48BxnDAM4DhOGAZwHCcMAziOE4YB-PBxwjAAHz5OGAbgw8cJwwB8-DhhGIAPnzgMA_DhE4dhAD584jAMwIdPHDh-_IJx4ocBHMYJHwAOwycOAIfhEwcAH_CJAzgM-MQBHAZ84sABAz5x4ICPwycOHIcP-MSBAz4OnzhwHD5x-MSB4_AJ-DhwHD4BHweOwyfg48Bxwj_g4wd8-DhOCgeM3zjhwzAAHz5xGAbgwyd8wDgOHz5xGMZx-PCJw_Bx4vDhE4fh48QJ44QP4Dh8-MRh-DhxGAZwHCcMAziOE4YBw8dxwjCA4zhhGIAPHycMAziOEz6MI7hx5IQhAj984vBxQPAP-McJwwB8-DhhGIAPHycMA_Dh44RhAD58nABwGD5OAAF8w0dOADpwHD8OCIcPHycMA4fhEweAw_CJA8Bh-MQBqPgDwyTxAz8eHO05aKVy2OHx41uGI42PZzh-PEd6oJqCH_LR_9gy4g8ewg8e-JBNHCcO8zgHv3gB4axzSjjrnHDWOeGsc9Y565x1TgmnhFPCKeGUcEo4JZwSzjrhhHPGOmGFs8Ip4axwVjgrnFVWOOuEcFZZ4awTVjjrhHDWCeGsE8JZJ5RwSgnjvBFOKOGUEM4p4ZxTwinnlHDKOSecU8Ip56xz1jnlnHVOCWedM9I6ZpwwwiohlFPCKeGUcEo4K5wSzDvrlLVaOCucFc4KZ4VD1lklgAFYiGVEIwoLKoBAABCmmOLOCScY');
	
	//var_dump($songs);
	
	?>
	

	
@endsection
