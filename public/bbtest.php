<!DOCTYPE html>
<html lang="de">
<head>
<title>dkMusic :: Backbone Tests</title>
<meta charset="utf-8">
<script src="/js/jquery.min.js" type="text/javascript"></script>
<script src="/js/bootstrap.min.js" type="text/javascript"></script>

<script src="/js/underscore.min.js" type="text/javascript"></script>
<script src="/js/backbone.min.js" type="text/javascript"></script>

<script src="/js/mustache.min.js" type="text/javascript"></script>
<script src="/js/mustache.templates.js" type="text/javascript"></script>

<script src="/app/init.js" type="text/javascript"></script>
<script src="/app/models.js" type="text/javascript"></script>
<script src="/app/views.js" type="text/javascript"></script>

<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css" media="all" />
<link rel="stylesheet" type="text/css" href="/css/font-awesome.min.css" media="all" />

<style type="text/css">

body {
	padding: 10px 0 10px 0;
}

</style>

<script type="text/javascript">
$(document).ready(function(){

    inboxinfo = new dkMusic.Models.InboxInfo();
	var inboxinfoview = new dkMusic.Views.InboxInfoView({model: inboxinfo});

});

</script>

</head>
<body>

<div class="container">
	
	<div class="navbar navbar-inverse">
		<div class="navbar-inner">
			<a class="brand" href="#">dkMusic</a>
			<ul class="nav">
				<li class="active"><a href="#"><i class="icon-home"></i></a></li>
			</ul>
		</div>
	</div>
	
	<div class="row">
		<div class="span3">
			<div id="inboxinfoview" class="well"></div>
		</div>
		<div class="span9">Content</div>
	</div>

</div>

</body>
</html>