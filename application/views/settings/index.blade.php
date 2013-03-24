@layout('layout.master')



@section('content')

	<div class="container">

	<h1>Settings</h1>

	{{Form::open('/settings', 'POST', array('class' => 'form-horizontal',  'enctype'=>'multipart/form-data'));}}

	
		<fieldset>
			
			{{Form::token();}}
			
			<legend>Basic folders</legend>
			
			<div class="control-group">
				<label class="control-label" for="username">Music Library</label>
				<div class="controls">
					<input type="text" input-xlarge disabled class="input-xlarge disabled" value="{{dkmusic_library}}" />
					@if ($errors->has('library'))
						<span class="label label-important">{{$errors->first('library')}}</span>
					@else
						<span class="label label-success">OK</span>
					@endif
					<span class="help-inline">This folder contains all your music files.</span>
				</div>
				<div class="controls">
					<span class="label label-warning">Note</span> Subfolders (A-Z and #) are created automatically on import (if missing).
				</div>
			</div>

			<p>&nbsp;</p>
			
			<div class="control-group">
				<label class="control-label" for="username">Inbox</label>
				<div class="controls">
					<input type="text" input-xlarge disabled class="input-xlarge disabled" value="{{dkmusic_inbox}}" />
					@if ($errors->has('inbox'))
						<span class="label label-important">{{$errors->first('inbox')}}</span>
					@else
						<span class="label label-success">OK</span>
					@endif
					<span class="help-inline">This folder contains all your <strong>new</strong> music files.</span>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="username">Trash</label>
				<div class="controls">
					<input type="text" input-xlarge disabled class="input-xlarge disabled" value="{{dkmusic_trash}}" />
					@if ($errors->has('trash'))
						<span class="label label-important">{{$errors->first('trash')}}</span>
					@else
						<span class="label label-success">OK</span>
					@endif
					<span class="help-inline">Already existing music files are moved to the trash folder.</span>
				</div>
				
			</div>		

			<legend>Internal folders</legend>
						
			<div class="control-group">
				<label class="control-label" for="username">Convert</label>
				<div class="controls">
					<input type="text" input-xlarge disabled class="input-xlarge disabled" value="{{dkmusic_internal_convert}}" />
					@if ($errors->has('convert'))
						<span class="label label-important">{{$errors->first('convert')}}</span>
					@else
						<span class="label label-success">OK</span>
					@endif
					<span class="help-inline">Storage folder for music files that need to be converted into mp3.</span>
				</div>
				<div class="controls">
					<span class="label label-warning">Note</span> Converted files are moved to the Inbox and origianl files are moved into the trash folder.
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="username">Missing data</label>
				<div class="controls">
					<input type="text" input-xlarge disabled class="input-xlarge disabled" value="{{dkmusic_internal_missingdata}}" />
					@if ($errors->has('missingdata'))
						<span class="label label-important">{{$errors->first('missingdata')}}</span>
					@else
						<span class="label label-success">OK</span>
					@endif
					<span class="help-inline">Music files with missing ID3 tags (artist and/or title)<br />or wrong ID3Tag version (v2.3.0 required).</span>
				</div>
			</div>
						
			<legend>Output folders</legend>

			<div class="control-group">
				<label class="control-label" for="username">Notordner</label>
				<div class="controls">
					<input type="text" input-xlarge disabled class="input-xlarge disabled" value="{{dkmusic_output_notordner}}" />
					@if ($errors->has('notordner'))
						<span class="label label-important">{{$errors->first('notordner')}}</span>
					@else
						<span class="label label-success">OK</span>
					@endif
					<span class="help-inline">Temporary folder to isolate files from your music library.</span>

				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="username">Duplicates</label>
				<div class="controls">
					<input type="text" input-xlarge disabled class="input-xlarge disabled" value="{{dkmusic_output_duplicates}}" />
					@if ($errors->has('duplicates'))
						<span class="label label-important">{{$errors->first('duplicates')}}</span>
					@else
						<span class="label label-success">OK</span>
					@endif
					<span class="help-inline">Storage folder for duplicated files.</span>

				</div>
			</div>

			<div class="form-actions">
				<a href="settings/create_directories" class="btn btn-large btn-primary">Create missing directories</a>
			</div>

		</fieldset>

	{{Form::close();}}
	
	</div>
	
@endsection