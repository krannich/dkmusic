<?php

Route::controller(array(
	'home',
	
	'inbox',
	'modifier',
	'duplicates',
	'playlists',
	
	'notordner',
	'database',
	'settings',

	'help',
	
));

Route::get('/', function() {
	return Redirect::to('home');
});

Route::get('test', function() {
	
	// Paul Kalkbrenner - Schnakeln: 95867
	// Peter Alexander - Weiße Weihnacht: 97009

	
	$song = DB::table('library')->where('id', '=' , '97009')->first();
	var_dump ($song);
	
	echo DS;
		
});

/*
|--------------------------------------------------------------------------
| Application 404 & 500 Error Handlers
|--------------------------------------------------------------------------
|
| To centralize and simplify 404 handling, Laravel uses an awesome event
| system to retrieve the response. Feel free to modify this function to
| your tastes and the needs of your application.
|
| Similarly, we use an event to handle the display of 500 level errors
| within the application. These errors are fired when there is an
| uncaught exception thrown in the application.
|
*/

Event::listen('404', function()
{
	return Response::error('404');
});

Event::listen('500', function()
{
	return Response::error('500');
});

/*
|--------------------------------------------------------------------------
| Route Filters
|--------------------------------------------------------------------------
|
| Filters provide a convenient method for attaching functionality to your
| routes. The built-in before and after filters are called before and
| after every request to your application, and you may even create
| other filters that can be attached to individual routes.
|
| Let's walk through an example...
|
| First, define a filter:
|
|		Route::filter('filter', function()
|		{
|			return 'Filtered!';
|		});
|
| Next, attach the filter to a route:
|
|		Router::register('GET /', array('before' => 'filter', function()
|		{
|			return 'Hello World!';
|		}));
|
*/

Route::filter('before', function()
{
	// Define diskdrive with closing backspace
	define ('dkmusic_library', 				'/Volumes/Music/Music Library/');

	define ('dkmusic_inbox', 				'/Volumes/Music/dkmusic/Inbox/');
	
	define ('dkmusic_internal',				'/Volumes/Music/dkmusic/Internal/');
	define ('dkmusic_internal_prepared',	'/Volumes/Music/dkmusic/Internal/prepared/');
	define ('dkmusic_internal_convert',		'/Volumes/Music/dkmusic/Internal/convert/');
	define ('dkmusic_internal_import',		'/Volumes/Music/dkmusic/Internal/import/');
	define ('dkmusic_internal_missingdata',	'/Volumes/Music/dkmusic/Internal/missingdata/');

	define ('dkmusic_output', 				'/Volumes/Music/dkmusic/Output/');
	define ('dkmusic_output_importlists', 	'/Volumes/Music/dkmusic/Output/importlists/');
	define ('dkmusic_output_notordner', 	'/Volumes/Music/dkmusic/Output/notordner/');
	define ('dkmusic_output_duplicates', 	'/Volumes/Music/dkmusic/Output/duplicates/');
	
	define ('dkmusic_trash', 				'/Volumes/Music/dkmusic/Trash/');

	define ('AcoustID_API_KEY', 			'K8NONPbK');

	// Do stuff before every request to your application...
});

Route::filter('after', function($response)
{
	// Do stuff after every request to your application...
});

Route::filter('csrf', function()
{
	if (Request::forged()) return Response::error('500');
});

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::to('login');
});