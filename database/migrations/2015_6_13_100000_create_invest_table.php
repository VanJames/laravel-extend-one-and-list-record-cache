<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvestTable extends Migration {

	protected $connection = 'core';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		for($i=1;$i<=2;$i++){
			Schema::connection($this->getConnection())->create('invest'.$i, function(Blueprint $table)
			{
				$table->increments('id');
				//项目id
				$table->integer('pid')->index();
				//用户id
				$table->integer('mid')->index();
				//投标金额
				$table->integer('invest_money')->index();
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
		for($i=1;$i<=2;$i++){
			Schema::drop('invest'.$i);
		}
	}

}
