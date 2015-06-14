<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminUserTable extends Migration {

	protected $connection = 'admin';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection($this->getConnection())->create('admin_user', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('email')->unique();
			$table->string('password', 60);
			$table->smallInteger('status')->index();
			$table->rememberToken();
			$table->timestamps();
		});
		DB::connection($this->getConnection())->table('admin_user')->insert(
			array(
				array(
					'name' => '范旭',
					'email'=>'746439274@qq.com',
					'password' => \Hash::make('123456')
				)
			));
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('admin_user');
	}

}
