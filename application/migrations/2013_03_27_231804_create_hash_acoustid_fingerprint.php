<?php

class Create_Hash_Acoustid_Fingerprint {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('hash_acoustid_fingerprint', function($table) {
	        $table->create();
		    $table->increments('library_id');
		    $table->string('hash', 255);
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('hash_acoustid_fingerprint');
	}

}