<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRepaymentTable extends Migration {

	protected $connection = 'core';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		for($i=1;$i<=4;$i++){
			Schema::connection($this->getConnection())->create('repayment'.$i, function(Blueprint $table)
			{
				$table->increments('id');
				//项目id
				$table->integer('pid')->index();
				//收款用户用户id
				$table->integer('mid')->index();
				//还款期次
				$table->integer('index')->index();
				//应收本金
				$table->integer('should_capital');
				//应收利息
				$table->integer('should_interest');
				//应收罚息
				$table->integer('should_fine');
				//应收时间
				$table->integer('should_at')->index();
				//实收时间
				$table->integer('fact_at')->index();
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
		for($i=1;$i<=4;$i++){
			Schema::drop('repayment'.$i);
		}
	}

}
