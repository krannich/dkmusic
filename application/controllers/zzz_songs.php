<?php

class Songs_Controller extends Base_Controller {

	public $restful = true;

	public function get_edit($id) {
		$song = Librarysong::find($id);
		return View::make('songs.edit')->with('song', $song);
	}
	

	

}