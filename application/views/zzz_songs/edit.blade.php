@layout('layout.master')



@section('content')



	<h1>Edit Song</h1>

	{{Form::open('/songs/save', 'POST', array('class' => 'form-horizontal',  'enctype'=>'multipart/form-data'));}}
	
		{{ Form::hidden('_id' , $song->id); }}
		{{ Form::token(); }}
	
		<div class="control-group">
			<label class="control-label">Artist</label>
			<div class="controls">
				{{ Form::text('artist' , $song->artist, array() ); }}
			</div>
		</div>
	
		<div class="control-group">
			<label class="control-label">Title</label>
			<div class="controls">
				{{ Form::text('title' , $song->title, array() ); }}
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">Genre</label>
			<div class="controls">
				{{ Form::text('genre' , $song->genre, array() ); }}
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">Year</label>
			<div class="controls">
				{{ Form::text('year' , $song->year, array() ); }}
			</div>
		</div>
	
	{{Form::close()}}
	
@endsection