<?php namespace App;

use App\DataModel\AdminDatabase;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\HelperDataOperation;
use App\Helpers\HelperSQLBuilder;
use App\Model\ModelContainer;

class AdminUser extends AdminDatabase {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'admin_user';

	const CACHE_KEY = '_adminUser';

	/**
	 * @param bool $isLock
	 */
	public function __construct( $isLock = false ){
		//是否是写数据
		$this->_isLock = $isLock;
		//不止一行数据
		$this->oneRow = false;
		//是否使用缓存
		$this->_useCache = true;
		//缓存服务器
		$this->_cacheDatabase = 'data_memcached';

		$this->queryCondition = $this->_buildQuery();
		parent::__construct();
	}

	public static function get($data){
		return self::firstByAttributes($data);
	}

	/**
	 * @param bool $isLock
	 * @return DataModel\BaseDatabase
	 */
	public static function getInstance($isLock = false ){
		if( ( $object = ModelContainer::get( self::_buildCacheKey() ) ) ===null
			||
			$object->getIsLocked() !== $isLock
		){
			$object = ModelContainer::register( new self( $isLock ) );
		}
		return $object;
	}

	public function hasUser($aid){
		return isset( $this->data['list'][$aid] );
	}

	/**
	 * @param string $aid
	 * @param string $key
	 * @param mixed $value
	 * @return $this
	 */
	public function setData($aid , $key,$value){
		if( !isset( $this->data['list'][$aid][$key] ) ){
			return $this;
		}
		if( is_array( $value ) && $this->data['list'][$aid][$key] == $value ){
			return $this;
		}
		elseif( !is_array( $value ) && "{$this->data['list'][$aid][$key]}" === "{$value}" ){
			return $this;
		}
		$this->data['list'][$aid][$key] = $value;
		$this->updateToDb(
			$this->table,
			HelperDataOperation::DATA_ACTION_UPDATE,
			array(
				$key => $value,
			),
			$aid
		);
		return $this;
	}

	public function getCacheKey(){
		return self::_buildCacheKey();
	}

	protected function afterLoadDb(){
		$formatData = array();
		if( is_array($this->data['list'])&&!empty( $this->data['list'] ) ){
			foreach( $this->data['list'] as $row ){
				$formatData[$row['id']] = $row;
			}
			$this->data['list'] = $formatData;
		}
	}

	protected function formatToSQL($table , $action , $key , $data){
		switch( $action ){
			case HelperDataOperation::DATA_ACTION_ADD:

				return HelperSQLBuilder::buildInsertSQL( $this->table , $data );

			case HelperDataOperation::DATA_ACTION_UPDATE:

				return HelperSQLBuilder::buildUpdateSQL( $this->table , $data , array(
						'id' => $key ,
					)
				);

			case HelperDataOperation::DATA_ACTION_DELETE:

				return HelperSQLBuilder::buildDeleteSQL( $this->table , array(
						'id' => $key ,
					)
				);
		}
	}

	/**
	 * 建立查询语句
	 * @return string
	 */
	private function _buildQuery(){
		return HelperSQLBuilder::buildSelectSQL(
			$this->table,
			[],
			[
				'status' => 0
			],
			[],
			['count'=>1]
		);
	}

	private static function _buildCacheKey(){
		return self::CACHE_KEY;
	}

}
