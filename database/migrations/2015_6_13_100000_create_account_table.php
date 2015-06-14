<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountTable extends Migration {

	protected $connection = 'member';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection($this->getConnection())->create('account', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('mid')->index();
			$table->string('type')->index();
			$table->integer('available_money')->index();
			$table->integer('freeze_money')->index();
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
		Schema::drop('account');
	}

}
