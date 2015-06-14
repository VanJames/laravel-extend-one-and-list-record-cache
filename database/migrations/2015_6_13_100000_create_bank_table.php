<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankTable extends Migration {

	protected $connection = 'member';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection($this->getConnection())->create('bank', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uid')->index();
			$table->string('number')->unique();
			$table->string('address')->index();
			$table->integer('type')->index();
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
		Schema::drop('bank');
	}

}
