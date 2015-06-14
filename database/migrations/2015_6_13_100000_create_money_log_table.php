<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMoneyLogTable extends Migration {

	protected $connection = 'member';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		for($i=1;$i<=10;$i++){
			Schema::connection($this->getConnection())->create('money_log'.$i, function(Blueprint $table)
			{
				$table->increments('id');
				$table->integer('mid')->index();
				$table->smallInteger('type')->index();
				$table->integer('effect_money');
				$table->integer('account_money');
				$table->timestamps();
			});
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		for($i=1;$i<=10;$i++){
			Schema::drop('money_log'.$i);
		}
	}

}
