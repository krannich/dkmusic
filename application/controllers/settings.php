<?php

class Settings_Controller extends Base_Controller {

	public $restful = true;

	public function get_index() {
	
		$errors = new Laravel\Messages();
		
		if (!is_dir(dkmusic_library)) $errors->add('library', 'missing');
		if (!is_dir(dkmusic_inbox)) $errors->add('inbox', 'missing');
		if (!is_dir(dkmusic_trash)) $errors->add('trash', 'missing');

		if (!is_dir(dkmusic_internal_convert)) $errors->add('convert', 'missing');
		if (!is_dir(dkmusic_internal_missingdata)) $errors->add('missingdata', 'missing');

		if (!is_dir(dkmusic_output_notordner)) $errors->add('notordner', 'missing');
		if (!is_dir(dkmusic_output_duplicates)) $errors->add('duplicates', 'missing');
			
		return View::make('settings.index')
			->with_errors($errors);
	}
	
	public function get_create_directories() {
					
		try {

			dkHelpers::create_path(dkmusic_library);
			dkHelpers::create_path(dkmusic_inbox);
			dkHelpers::create_path(dkmusic_trash);

			dkHelpers::create_path(dkmusic_internal_convert);
			dkHelpers::create_path(dkmusic_internal_missingdata);
			
			dkHelpers::create_path(dkmusic_output_notordner);
			dkHelpers::create_path(dkmusic_output_duplicates);

			$success = "Missing directories successfully created!";
			return Redirect::to('/settings')->with('success', $success);

		}
		
		catch (Exception $e) {
			$errors = $e->getMessage();
			return Redirect::to('/settings')->with('error', $errors);
		}

	}
	
		
	

}