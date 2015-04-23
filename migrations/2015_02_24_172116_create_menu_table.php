<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('menus', function($table)
		{

		    $table->increments('id');
		    $table->integer('parent_id')->nullable();

		    $table->string('title',255);
		    $table->string('url',255);
            
		    $table->integer('page_id');
		    //$table->foreign('page_id')->references('id')->on('pages');

		    $table->boolean('active')->default(false);

		    $table->integer('lft')->nullable();
		    $table->integer('rgt')->nullable();
		    $table->integer('depth')->nullable();

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
		Schema::drop('menus');
	}

}
