<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectTable extends Migration {

	protected $connection = 'core';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection($this->getConnection())->create('project', function(Blueprint $table)
		{
			$table->increments('id');
			//发标人 借款人
			$table->integer('mid')->index();
			$table->smallInteger('status')->index();
			$table->integer('money');
			//利率
			$table->integer('rate');
			$table->integer('invest_money');
			//期望投满标时间
			$table->integer('expect_time')->index();
			//还款方式
			$table->smallInteger('repay_type');
			//借款期限
			$table->integer('period');
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
		Schema::drop('project');
	}

}
