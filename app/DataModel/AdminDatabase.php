<?php namespace App\DataModel;

use Illuminate\Database\Eloquent\Model;

abstract class AdminDatabase extends BaseDatabase {

	/**
	 * @var string
	 */
	protected $connection = 'admin';
}
