@layout('layout.master')

@section('header')

{{HTML::script('/js/underscore.min.js')}}
{{HTML::script('/js/backbone.min.js')}}

{{HTML::script('/js/bootstrap-datepicker.js')}}

{{HTML::style('/css/datepicker.css')}}

<style type="text/css">

#results tbody tr {
	cursor: pointer;
}

#results td:first-child {
	width: 30px;
	text-align: center;
}

#results td:first-child a {
	text-decoration: none;
	color: #e0e0e0;
}

#results td:first-child a.isPlaying,
#results td:first-child a:hover {
	text-decoration: none;
	color: #08c;
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


<script type="text/javascript">

var tablemenu = {
    'position'      :   'after',
    'menu_items'    : {
    	/*
        'edit' 		: {
            'url'           :   '',
            'title'         :   'edit',
            'icon'          :   'icon-pencil icon-big',
            'js'			:	'page.editSong(url)',
        },
        */
        'del' 		: {
            'url'           :   '',
            'title'         :   'delete',
            'icon'          :   'icon-remove icon-big icon-red',
            'js'			:	'page.deleteSong(url)',
        },
    },
};


var page = {

	songs: new dkMusic.Collections.SongCollection(),
	song: null,
	collectionView: null,
	modelView: null,
	statusCode: null,
	fetchXhr: 0,

	fetchParams: { searchstring: '', searchdate: '' },
	fetchInProgress: false,
	dialogIsOpen: false,

	init: function() {
			
		$('#searchstring').bind('keyup', function(obj){
			page.loading(true);
			page.fetchParams.searchstring = $('#searchstring').val();
			page.fetchSongs(page.fetchParams);
		});
		
		$('#searchdate').bind('change', function(obj) {
			page.loading(true);
			page.fetchParams.searchdate = $('#searchdate').val();
			page.fetchSongs(page.fetchParams);
		});
		
		$("#saveSongButton").click(function(e) {
			e.preventDefault();
			page.updateModel();
		});
		
		this.collectionView = new dkMusic.Views.SongCollectionView({
			el: $("#results > tbody"),
			collection: page.songs,
		});
		
		this.modelView = new dkMusic.Views.SongDetailView({
			el: $("#songDetailDialog .modal-body"),
		});
		
		this.collectionView.on('rendered',function(){
			$('#results > tbody > tr').bind('dblclick', function(e) {
				e.preventDefault();
				var dk_data = eval('([' + $(this).attr('dk_data') + '])')[0];
				var selected_song = page.songs.get(dk_data.id);
				page.showDetailDialog(selected_song);
			});
			
			
			$('#results > tbody > tr').bind('selectstart', function(event) {
				event.preventDefault();
			});
			
			
			$('#results a').bind('click', function(e){
				e.preventDefault();
			
				var song = $(this).attr('href');

				$('.isPlaying').html('<i class="icon-play"></i>');
				$('#results a').removeClass('isPlaying');
			
				if (isplaying==0 || songplaying!=song) {
					playsong(song);
					isplaying =1;
					songplaying = song;
					$(this).html('<i class="icon-stop"></i>');
					$(this).addClass('isPlaying');
				} else {
					$("#jquery_jplayer_1").jPlayer("stop");
					isplaying=0;
					songplaying = '';
					$(this).html('<i class="icon-play"></i>');
				}
				
			});

			$('#results').tablemenu(tablemenu);

		});
			
		page.isInitialized = true;
		
	},

	fetchSongs: function(params, hideLoader) {
		page.fetchParams = params;

		page.fetchInProgress = true;
		
		if(page.fetchXhr.readyState > 0 && page.fetchXhr.readyState < 4){
			page.fetchXhr.abort();
		}
		
		page.fetchXhr = page.songs.fetch({
			data: params,
			success: function() {
				if (page.songs.collectionHasChanged) {
					page.collectionView.render();
				}
				
				var resultscount = page.songs.length;			
				if (resultscount > 0 ) {
					$('#results-count').html('Results: <span class="badge">' + resultscount + '</span>');
				} else {
					$('#results-count').html('');
				}
				
				page.loading(false);
				page.fetchInProgress = false;
			},

			error: function(m, r) {
				page.fetchInProgress = false;
			}

		});
	},
	
	showDetailDialog: function(selected_song) {
		page.song = selected_song;
		page.modelView.model = page.song;
		$('#songDetailDialog').modal({ show: true });
		page.renderModelView();		
		
	},
	
	renderModelView: function() {
		page.modelView.render();
	},
	
	editSong: function(id) {
		var selected_song = page.songs.get(id);
		page.showDetailDialog(selected_song);
	},
	
	deleteSong: function(id) {
		var selected_song = page.songs.get(id);
		bootbox.confirm("Do you really want to delete<br /><strong>" + selected_song.attributes.filename +"</strong>", function(result){
			if(result) {
				page.deleteModel(selected_song);
			} else {
				return;
			}
		});
	},
	
	deleteModel: function(selected_song) {
		page.song = selected_song;
		page.song.destroy({
			wait: true,
			success: function(){
				
				$.pnotify({
			    	text: selected_song.attributes.filename + ' moved into trash folder!',
			    	type: 'success'
			    });
			    
				page.collectionView.render();
			},
			error: function(model,response,scope){
				
				alert ('error');

			}
		});
	},
	
	updateModel: function() {
	
		page.song.save({
			'artist': $('#songDetailDialog input#artist').val(),
			'title': $('#songDetailDialog input#title').val(),
		}, {
			wait: true,
			success: function(){
				$('#songDetailDialog').modal('hide');
				page.fetchSongs(page.fetchParams,true);
		},
			error: function(model,response,scope){
				alert ('error');
				/*
				try {
					var json = $.parseJSON(response.responseText);

					if (json.errors)
					{
						$.each(json.errors, function(key, value) {
							$('#'+key+'InputContainer').addClass('error');
							$('#'+key+'InputContainer span.help-inline').html(value);
							$('#'+key+'InputContainer span.help-inline').show();
						});
					}
				} catch (e2) {
					if (console) console.log('error parsing server response: '+e2.message);
				}
				*/
			}
		});		
	},
	
	loading: function(is_loading) {
			if (is_loading===true) {
			var t_el = $("#results > tbody");
			$("#loading-table").css({
			  opacity: 1,
			  top: t_el.offset().top,
			  width: t_el.outerWidth(),
			  height: t_el.outerHeight()
			});
			$("#loading-table").show();
		} else {
			$("#loading-table").hide();
			
		}
	}

};



$(document).ready(function () {

	page.init();
	
	$('input,select').keypress(function(event) { return event.keyCode != 13; });
	
	$('#searchstring').focus();
	
	$('.datepicker').datepicker( {
		format	:	"yyyy-mm-dd",
		autoclose: true,
   		todayHighlight: true,
   		language: 'de',
   		weekStart: 1,
   		todayBtn: true,
	});
	
    $("#results").tablesorter({
        widgets: ['stickyHeaders'],
        sortList: [[1,0]],
        headers: {
	  		0: {sorter: false},
  		},
        widgetOptions: { 
	    	stickyHeaders : 'tablesorter-stickyHeader', 
	    	stickyHeadersOffset : 40,
  		},
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
							<h2 class="normal">Library</h2>
							<div id="results-count"></div>
						</div>

						<div class="span9">

							{{Form::open('', 'get', array('id'=>'results_form', 'class'=>"form-horizontal")  );}}
								<fieldset>
		
									<div class="input-append">
										{{ Form::text('searchstring' , '', array('id' => 'searchstring', 'placeholder' => 'Search', 'autocomplete' => 'off')); }}
										<span class="add-on"><i class="icon-search"></i></span>
									</div>
									
									<div class="datepicker input-append date">
										{{ Form::text('searchdate' , '', array('id' => 'searchdate', 'placeholder' => 'Date', 'class' => 'input-small', 'readonly' => 'readonly')); }}
										<span class="add-on"><i class="icon-calendar"></i></span>
									</div>
									
									<div class="clearfix"></div>
									
									<span class="help-inline">
										<code>phil</code>&nbsp;&nbsp;&nbsp;Search songs that begin with the phrase "phil"<br />
										<code>*phil</code> Search songs that contain the phrase "phil".<br />
										Double-click row to edit metadata.
									</span>
									
								</fieldset>
							{{Form::close()}}
						</div>
					</div>					
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="span12">
				<table id="results" class="tablesorter table-striped">
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


	<div class="modal hide fade" id="songDetailDialog">
		<div class="modal-header">
			<a class="close" data-dismiss="modal">&times;</a>
			<h3>
				<i class="icon-edit"></i> Edit Metadata
				<span id="modelLoader" class="loader progress progress-striped active"><span class="bar"></span></span>
			</h3>
		</div>
		<div class="modal-body"></div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal">Cancel</button>
			<button id="saveSongButton" class="btn btn-primary">Save Changes</button>
		</div>
	</div>		
	
@endsection
