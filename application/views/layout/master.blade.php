<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title>dkmusic :: the ultimate mp3 music library manager.</title>
    <meta name="viewport" content="width=device-width">
    
    
    {{ HTML::style('/css/bootstrap.min.css') }}
    {{ HTML::style('/css/dk.bootstrap.css') }}
    {{ HTML::style('/css/dk.basic.css') }}
    {{ HTML::style('/css/dk.tablemenu.css') }}
    {{ HTML::style('/css/dk.tablesorter.css') }}

    {{ HTML::style('/css/font-awesome.min.css') }}
    {{ HTML::style('/css/jquery.pnotify.default.css') }}

    {{ HTML::script('/js/jquery.min.js') }}
    {{ HTML::script('/js/jquery.mb.browser.min.js') }}

    {{ HTML::script('/js/bootstrap.min.js') }}
    {{ HTML::script('/js/underscore.min.js') }}
    {{ HTML::script('/js/backbone.min.js') }}
    {{ HTML::script('/js/mustache.min.js') }}
    {{ HTML::script('/js/mustache.templates.js') }}

    {{ HTML::script('/js/bootbox.min.js') }}
    {{ HTML::script('/js/jquery.pnotify.min.js') }}
    {{ HTML::script('/js/jquery.jqmq.min.js') }}

	{{ HTML::script('/js/dk.tablemenu.min.js') }}
	{{ HTML::script('/js/dk.tabledata.min.js') }}
	{{ HTML::script('/js/dk.tablesorter.min.js') }}


	{{HTML::script('/js/jquery.jplayer.min.js')}}
	{{HTML::script('/js/dk.jplayer.min.js')}}
	{{HTML::style('/css/jplayer-nav.css')}}


    {{ HTML::script('/app/init.js') }}    
    {{ HTML::script('/app/models.js') }}
    {{ HTML::script('/app/collections.js') }}
    {{ HTML::script('/app/views.js') }}

    <?php
    /*
    // TEST IMPLEMENTATION FOR BASSET BUNDLE
    // BUT IT CAUSES PROBLEMS WITH LONG JS FILES
    
    {{ Basset::show('app-css.css') }}
    {{ Basset::show('app-js.js') }}

    {{ HTML::script('/js/underscore.min.js') }}
    {{ HTML::script('/js/backbone.min.js') }}
    {{ HTML::script('/js/mustache.min.js') }}
    {{ HTML::script('/js/mustache.templates.js') }}

    {{ HTML::script('/js/bootbox.min.js') }}
    {{ HTML::script('/js/jquery.pnotify.min.js') }}
    {{ HTML::script('/js/jquery.jqmq.min.js') }}

	{{ HTML::script('/js/dk.tablemenu.min.js') }}
	{{ HTML::script('/js/dk.tabledata.min.js') }}
	{{ HTML::script('/js/dk.tablesorter.min.js') }}

    {{ HTML::script('/app/init.js') }}    
    {{ HTML::script('/app/models.js') }}
    {{ HTML::script('/app/views.js') }}
    */
    ?>
    <style type="text/css">

	* {
		-webkit-font-smoothing: antialiased !important;
		text-redering: optimizeLegibility;
	}
	
    body {
        padding-top: 65px;
        background-color: #fff;
        
    }
        
    </style>
    
    @if (!is_dir(dkmusic_library))
		<script type="text/javascript">
		
			$(document).ready(function () {
			
				$.pnotify({
			    	text: '<strong>Music libarary not found.</strong><br/>dkmusic may not work correctly. Please double-check if your hard disk is attached.',
			    	type: 'error',
			    	//addclass: "stack-bar-top",
			    	hide: false,
			    });
			
			});
			
		</script>
	@endif
	
    @yield('header')

<?php
/*
----- Init PNOTIFY Messages -----
*/
?>
	<script>
		$.pnotify.defaults.history = false;
		$.pnotify.defaults.shadow = false;
	</script>
	
	@if (Session::get('error'))
		<script type="text/javascript">
			$(document).ready(function () {
				$.pnotify({
			    	text: '{{Session::get('error');}}',
			    	type: 'error'
			    });
			});
		</script>
	@endif

	@if (Session::get('success'))
		<script type="text/javascript">
			$(document).ready(function () {
				$.pnotify({
			    	text: '{{Session::get('success');}}',
			    	type: 'success'
			    });
			});
		</script>
	@endif

	<script>
		$(document).ready(function () {
		    $("[disabled!=disabled][rel=tooltip]").tooltip();

		    $("[disabled=disabled]").attr('onclick','').unbind('click');
		    $("[disabled=disabled]").click(function(e){
		        e.stopPropagation();
		    	e.preventDefault();
		    });  
    });  
	</script>

</head>

<body>

	@include('layout._topnavbar')
	
    <div class="container">

		@yield('content')
		<div id="loading-table"><i class="icon-spinner icon-3x icon-spin"></i></div>

    </div>
    
    <div class="modal-loading"><div class="spinner-container"><i class="icon-spinner icon-3x icon-spin"></i></div></div>

</body>
</html>

