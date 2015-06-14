<?php namespace App\DataModel;

use Illuminate\Database\Eloquent\Model;

abstract class CoreDatabase extends BaseDatabase {

	/**
	 * @var string
	 */
	protected $connection = 'core';

}
