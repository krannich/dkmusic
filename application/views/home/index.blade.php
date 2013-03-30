@layout('layout.master')

@section('header')

<style>

</style>

<script>

$(document).ready(function () {


});

</script>

@endsection

@section('content')

	 <div class="container">
		<div class="row">
			<div class="span3">
				<div class="well" style="width: 180px;" data-spy="affix">	
					<h2 class="normal">Library</h2>
					<table style="width: 100%">
						<tr>
							<td>Files</td>
							<td class="tdr"><strong>{{Librarysong::count_all()}}</strong></td>
						</tr>
						<tr>
							<td>AcoustIDs:</td>
							<td class="tdr">{{Librarysong::count_all_with_acoustid()}}</td>
						</tr>
						<tr>
							<td>Fingerprints:</td>
							<td class="tdr">{{Librarysong::count_all_with_acoustid_fingerprint()}}</td>
						</tr>					
						<tr>
							<td>Disk space:</td>
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
							<td>Convert to mp3:</td>
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
					
				</div>
			</div>
			
			<div class="span9">	        
				<div role="main" id="main">

					<div class="page-header">
						<h1 class="normal">dkmusic <small>the ultimate mp3 music library manager.</small></h1>
					</div>
					
					<div class="row">
						<div class="span3">
							<h2><i class="icon-music"></i> Library</h2>
							 <p>Browse through your music library and listen to your songs. If the song's metadata is not correct, simply double-click the entry and change them. The ID3Tags are updated accordingly.</p>
							<p><a class="btn" href="/library">Browse your library »</a></p>
						</div>
						<div class="span3">
							<h2><i class="icon-inbox"></i> Inbox</h2>
							 <p>Simply put your new music files into the inbox folder. dkmusic analyzes your mp3 files and puts them in the corresponding folders (#, A-Z). Duplicates are removed automatically.</p>
							<p><a class="btn" href="/inbox">Import files »</a></p>
					 	</div>
						<div class="span3">
							<h2><i class="icon-copy"></i> Duplicates</h2>
							<p>If you have a large music library, you might probably have some duplicates. dkmusic helps you to get rid of them, by creating acoustic fingerprints and comparing metadata.</p>
							<p><a class="btn" href="/duplicates">Find duplicates »</a></p>
						</div>
					</div>
		
		
		
	        	</div>
	        </div>
	        
		</div>
    </div>

	
	
@endsection
