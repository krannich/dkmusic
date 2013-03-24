@layout('layout.master')

@section('header')

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
    
	
	$('#myTab a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	});
	
	
});
</script>


@endsection

@section('content')


	 <div class="container">
		<div class="row">
		
			<div class="span3">
				<div class="well">		
					<h2 class="normal">Modifier</h2>
				
					<hr />
					
				</div>
			</div>
			
			<div class="span9">	        
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
