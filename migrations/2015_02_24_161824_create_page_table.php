<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pages', function($table)
		{

		    $table->increments('id');
		    
		    $table->string('title',255);
		    $table->longText('description');
		    $table->longText('content');
		    $table->string('url',255);
                    $table->boolean('active');
                    $table->string('type',255);
                    $table->integer('template');
                    $table->integer('menu_id');			
			
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
		Schema::drop('pages');
	}

}
