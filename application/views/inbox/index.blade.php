@layout('layout.master')
<?php 

// INBOX

?>

@section('header')


<style>
#results {
	overflow: auto;
}

#results p, #results li  {
	color: #666;
	margin: 0;
}

#results p.red {
	color: #c00;
}

</style>

<script>

var dkbatch_data = new Array();
var dkbatch_max = 0;
var dkbatch_index = 0;


function init_convert_batch() {
	dkbatch_data = [];
    dbkatch_index = 0;
    
	$.ajax({
       url: "/inbox/convert_content",
       async: false,
       success: function(msg) { 
	       dkbatch_data = JSON.parse(msg);
	       dkbatch_max = dkbatch_data.length;
       }
   });
	
}


function init_import_batch() {
	dkbatch_data = [];
    dbkatch_index = 0;
    
	$.ajax({
       url: "/inbox/inbox_content",
       async: false,
       success: function(msg) { 
	       dkbatch_data = JSON.parse(msg);
	       dkbatch_max = dkbatch_data.length;
       }
   });
	
}


function do_batch(url, title) {
	title = typeof title !== 'undefined' ? title : '';
	
	var file = dkbatch_data.shift();
	$.ajax({
       url: url,
       data: {"file" : file},
       async: true,
       success: function(response) { 
       	    var json = JSON.parse(response);
       		if(json.code==1) inboxinfo.imported();
       		if(json.code==2) inboxinfo.increase('convert_count');
       		if(json.code==3) inboxinfo.increase('missingdata_count');
       		if(json.code==4) inboxinfo.increase('duplicates_count');
       		if(json.code==10) inboxinfo.increase('trash_count');
            $("#results_status").prepend(json.text);
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





$(document).ready(function() {

	// Assign button actions

	$("button.import").bind('click', function(e) {
		bootbox.confirm("Do you really want to import all files?", function(result){
			if(result) {
				$('#info_box').hide();
				init_import_batch();
				$("#results_status").html("");
			    if (dkbatch_data.length > 0 ) {
			    	do_batch("/inbox/import", 'Import');
			    } else {
			    	$("#results_status").html('<p><span class="label label-warning">NOTE</span> Nothing to import<p>');
			    };
			} else {
				return;
			}
		});
	});

	$("button.convert").bind('click', function(e) {
		bootbox.confirm("Do you really want to convert all files?", function(result){
			if(result) {
				$('#info_box').hide();
				init_convert_batch();
				$("#results_status").html("");
			    if (dkbatch_data.length > 0 ) {
			    	do_batch("/inbox/convert", 'Convert');
			    } else {
			    	$("#results_status").html('<p><span class="label label-warning">NOTE</span> Nothing to convert<p>');
			    };
			} else {
				return;
			}
		});
	});


	$("button.emptytrash").bind('click', function(e) {
		bootbox.confirm("Do you really want to empty the trash?", function(result){
			if(result) {
				location.href = "/inbox/emptytrash";
			} else {
				return;
			}
		});
	});

    inboxinfo = new dkMusic.Models.InboxInfo();
	var inboxinfoview = new dkMusic.Views.InboxInfoView({model: inboxinfo});
    inboxinfo.fetch();

});
</script>


@endsection

@section('content')

	 <div class="container">
		<div class="row">
		
			<div class="span3">
				
				<div class="well">
					<div id="inboxinfoview"></div>

					<hr />
					
					<p><button class="btn btn-max import"><i class="icon-cloud-upload"></i> Import files</button></p>
					<hr />
					<p><button class="btn btn-max convert"><i class="icon-music"></i> Convert to mp3</button></p>
					<p><button class="btn btn-danger btn-max emptytrash"><i class="icon-trash"></i> Empty trash</button></p>

				</div>
			</div>
			
			<div class="span9">	        
				<div role="main" id="main">

					<div id="info_box" class="well">
						<h2 class="normal">How does it work?</h2>
						<p>Your music files will be organized in the corresponding folders (#, A-Z) of your music library.<br />
							Duplicates will be removed according to their acoustid and audio fingerprint<br />
							For more information about acoustid and audio fingerprints visit www.acoustid.org.
						</p>

						<p>The following steps are performed:</p>
						<ul>
							<li>Remove punctuation marks, apostrophes, curly and square brackets from artist and title</li>
							<li>Remove "The" and "Die" from beginning of artist</li>
							<li>Replace german umlauts, accents, and special characters.</li>
							<li>Convert "featuring" and "feat" to "ft"</li>
							<li>Convert artist and title to lowercase except first letter</li>
							<li>Rename filename accordingly (a timestamp is added if filename already exists)</li>
						</ul>
							
						</p>

						<p><strong>Afterwards your music files will look like this:</strong></p>
						<ul>
							<li>Phil collins - In the air tonight (radio edit).mp3</li>
							<li>Flo rida ft akon - Who dat girl (mds dont know who she is remix).mp3</li>
							<li>Howard carpendale - Nachts wenn alles schlaeft.mp3</li>
							<li>Black eyed peas - Dont stop the party.mp3</li>
						</ul>
						
						<p><span class="label label-warning">Note</span> Since the external library getID3 does not support to ID3-tag writing of m4a files,<br />
							all your m4a files must to be converted to mp3.
						</p>
					</div>
					
					<div id="results" class="well">
						<div id="results_progress" class="progress"><div class="bar"></div></div>
						<div id="results_status" class="well"></div>
					</div>
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
