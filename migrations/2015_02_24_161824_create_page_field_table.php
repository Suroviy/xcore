<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePageFieldTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('page_fields', function($table)
		{
		    // первичный ключ с автоинкрементом
		    $table->increments('id');
		    
			$table->integer( 'page_id' );
		    $table->string('key',255);
		    $table->longText('value');
			
		    $table->timestamps();		    
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('page_fields');
	}

}
