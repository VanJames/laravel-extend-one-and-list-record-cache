<?php namespace App;

use App\DataModel\MemberDatabase;
use App\Helpers\HelperDataOperation;
use App\Helpers\HelperSQLBuilder;
use App\Model\ModelContainer;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends MemberDatabase implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	const CACHE_KEY = '_user';

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'password'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];

	private $_mid = 0;

	/**
	 * @param $mid
	 * @param bool $isLock
	 */
	public function __construct( $mid = 0 , $isLock = false ){
		//用户ID
		$this->_mid = (integer)$mid;
		//是否是写数据
		$this->_isLock = $isLock;
		//是否使用缓存
		$this->_mid >0 && $this->_useCache = true;
		//缓存服务器
		$this->_cacheDatabase = 'data_memcached';

		$this->queryCondition = $this->_buildQuery();
		parent::__construct();
	}

	/**
	 * @param $mid
	 * @param bool $isLock
	 * @return DataModel\BaseDatabase
	 */
	public static function getInstance($mid,$isLock = false ){
		if( ( $object = ModelContainer::get( self::_buildCacheKey( $mid ) ) ) ===null
			||
			$object->getIsLocked() !== $isLock
		){
			$object = ModelContainer::register( new self( $mid , $isLock ) );
		}
		return $object;
	}

	public function getCacheKey(){
		return self::_buildCacheKey($this->_mid);
	}

	/**
	 * 建立缓存键
	 * @param $mid
	 * @return string
	 */
	private static function _buildCacheKey( $mid ){
		return $mid . self::CACHE_KEY;
	}

	/**
	 * 建立查询语句
	 * @return string
	 */
	private function _buildQuery(){
		return HelperSQLBuilder::buildSelectSQL(
			$this->table,
			[],
			['id'=>$this->_mid],
			[],
			['count'=>1]
		);
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return $this
	 */
	public function setData($key,$value){
		if( !isset( $this->data[$key] ) ){
			return $this;
		}
		if( is_array( $value ) && $this->data[$key] == $value ){
			return $this;
		}
		elseif( !is_array( $value ) && "{$this->data[$key]}" === "{$value}" ){
			return $this;
		}
		$this->data[$key] = $value;
		$this->updateToDb(
			$this->table,
			HelperDataOperation::DATA_ACTION_UPDATE,
			array(
				$key => $value
			)
		);
		return $this;
	}

	protected function formatToSQL($table , $action , $key , $data){
		switch( $action ){
			case HelperDataOperation::DATA_ACTION_ADD:

				return HelperSQLBuilder::buildInsertSQL( $this->table , $data );

			case HelperDataOperation::DATA_ACTION_UPDATE:

				return HelperSQLBuilder::buildUpdateSQL( $this->table , $data , array(
						'id' => $this->_mid ,
					)
				);

			case HelperDataOperation::DATA_ACTION_DELETE:

				return HelperSQLBuilder::buildDeleteSQL( $this->table , array(
						'id' => $this->_mid ,
					)
				);
		}
	}

}
