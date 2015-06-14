<?php namespace App\DataModel;

use Illuminate\Database\Eloquent\Model;

abstract class MemberDatabase extends BaseDatabase {

	/**
	 * @var string
	 */
	protected $connection = 'member';

}
