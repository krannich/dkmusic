<?php

class Create_Library {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up() {

		Schema::table('library', function($table) {
		    
	        $table->create();
		    $table->increments('id');
		    
		    $table->string('folder', 1);
		    $table->text('filename');
		    $table->string('artist', 255);
		    $table->string('title', 255);
		    
		    $table->string('type', 10);
		    $table->string('size', 15);

		    $table->timestamps();  
		});
		

	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('library');
	}

}