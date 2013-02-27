<?php

class Create_Library_Metadata {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('library_metadata', function($table) {
	        $table->create();
		    $table->increments('library_id');
		    
		    $table->string('acoustid_acoustid', 100);
		    $table->text('acoustid_fingerprint');
		    $table->string('length', 10);
		    $table->string('bitrate', 10);
		    $table->string('year', 6);
		    
		    $table->string('genre', 100);
		    $table->string('bpm', 6);
		    $table->string('size', 15);
		    
   		    $table->float('acoustid_score');

		    $table->timestamp('added');
		    $table->timestamp('lastmodified');

		    $table->timestamps();  
		});

	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('library_metadata');
	}

}