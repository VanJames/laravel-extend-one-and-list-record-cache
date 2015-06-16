<?php
/**
 * @User fanux(746439274@qq.com)
 */
namespace App\DataModel;

use App\Exceptions\AppException;
use App\Helpers\HelperCommon;
use App\Helpers\HelperDataOperation;
use Illuminate\Database\Eloquent\Model;

abstract class BaseDatabase extends Model {

	protected $_isLock = false;

	protected $_useCache = false;

	protected $data = array();

	protected $oneRow = true;

	protected $_cacheDatabase = 'data_memcached';

	protected $queryCondition = array();

	private $_cacheLockEngine = null;

	private $_cacheTime = 2592000;

	const CACHE_PREFIX_LOCK = '_lock';

	/**
	 * 当一个数据对象只代表一条记录时，可以使用这个数据键来更新数据库
	 * @var	string
	 */
	const ONLY_ONE_RECORD_KEY = 'oneRow';

	/**
	 * 数据是否已经修改（是否有脏数据）
	 * @var	array(
	 * 			{$tableName:string}:array(
	 * 				{$action:int}:\Framework\Model\DataOperation
	 * 			)
	 * 		)
	 */
	private $_dirtyData = array();

	public function __construct(){
		if($this->_isLock){
			$this->_lock();
		}
		if($this->_useMemcache()){
			$this->_cacheLockEngine = HelperCommon::getCache($this->_cacheDatabase);
		}
		if( $this->_useCache ){
			if( $this->oneRow ){
				$this->data = json_decode($this->_cacheLockEngine->get( $this->getCacheKey() ),true);
			}
			else{
				$this->data['list'] = json_decode($this->_cacheLockEngine->get( $this->getCacheKey() ),true);
			}
		}
		if( ( $this->oneRow && !is_array( $this->data ) )
			||
			( !$this->oneRow && !is_array( $this->data['list'] ) )
		){
			$this->_initFromDb();
			$this->afterLoadDb();
			$this->_saveCache();
		}
		parent::__construct();
	}

	public function getData($id){
		if( $this->oneRow ){
			return $this->data ? : [];
		}
		return $this->data['list'][$id] ? : [];
	}

	public function getList(){
		return $this->data['list'] ? : [];
	}

	/**
	 * Save the model to the database.
	 *
	 * @param  $db
	 * @return bool
	 */
	public function doSave($db)
	{
		if( AppException::hasException() ){
			return false;
		}
		return $this->_saveToDb($db);
	}

	/**
	 * 保存缓存
	 * @return bool
	 */
	public function doSaveCache(){
		if( AppException::hasException() ){
			return false;
		}
		if($this->_useMemcache()){
			$this->_cacheLockEngine->forget( $this->_getLockCacheKey() );
			$this->_isLock = false;
		}
		$this->_saveCache();
		return true;
	}

	protected function afterLoadDb(){

	}

	public function getIsLocked(){
		return $this->_isLock;
	}

	abstract public function getCacheKey();

	public function rollBack(){
		if($this->_useMemcache()){
			$this->_cacheLockEngine->forget( $this->_getLockCacheKey() );
			$this->_isLock = false;
		}
		$this->_dirtyData = [];
	}

	private function _lock(){
		if( !$this->_isLock
			&& $this->_useCache
			&& !$this->_cacheLockEngine->add( $lockKey = $this->_getLockCacheKey() , 1 , 5 ) )
		{
			throw new AppException( AppException::ERROR_CODE_CACHE_LOCK );
		}

		$this->_isLock = true;
	}

	private function _getLockCacheKey(){
		return $this->getCacheKey() . self::CACHE_PREFIX_LOCK;
	}

	private function _useMemcache(){
		return $this->_useCache && strpos($this->_cacheDatabase,'memcache')!==false;
	}

	private function _saveCache(){
		if($this->_useCache){
			$data = $this->oneRow ? $this->data : $this->data['list'];
			if( is_array( $this->data ) && !empty( $this->data ) ){
				$this->_cacheLockEngine->put( $this->getCacheKey() , json_encode($data) , $this->_cacheTime / 60 );
			}
		}
	}

	private function _initFromDb(){
		if($this->oneRow){
			$this->data = json_decode(
				json_encode(\DB::connection($this->connection)->selectOne($this->queryCondition))
				,
				true );
		}
		else{
			$this->data['list'] = json_decode(
				json_encode(\DB::connection($this->connection)->select($this->queryCondition)) ,
				true );
		}
	}

	/**
	 * 格式化保存到数据库的数据
	 * @param	string $table	表名
	 * @param	string $action	操作动作
	 * @param	string $key		数据键
	 * @param	array $data		数据
	 * @return	string
	 */
	abstract protected function formatToSQL( $table , $action , $key , $data );

	/**
	 * 更新数据到数据库
	 * @param	string	$table	表名
	 * @param	string|int	$key	键
	 * @param	int	$action	数据动作
	 */
	protected function updateToDb( $table , $action , $data , $key = self::ONLY_ONE_RECORD_KEY )
	{
		if( $this->_isNeedMergeDataAction( $table , $key ) )
		{
			$this->_dirtyData[$table][$key]->mergeOperation( $action , $data );
		}
		else
		{
			$this->_dirtyData[$table][$key] = new HelperDataOperation( $action , $data );
		}
	}

	/**
	 * 判断是否需要合并数据操作
	 * @param	string	$table	表名
	 * @param	string|int	$key	键
	 * @return boolean
	 */
	private function _isNeedMergeDataAction( $table , $key )
	{
		return isset( $this->_dirtyData[$table] )
		&& isset( $this->_dirtyData[$table][$key] );
	}

	/**
	 * 将数据保存到数据库中
	 * @param $db
	 * @return boolean
	 */
	private function _saveToDb($db)
	{
		$result = false;
		foreach( $this->_dirtyData as $table => $keys )
		{
			foreach( $keys as $key => $dataOperation )
			{
				if( $dataOperation->isDiscard() )
				{
					continue;
				}

				$sql = $this->formatToSQL( $table , $dataOperation->getAction() , $key , $dataOperation->getData() );
				if( $sql )
				{
					switch( $dataOperation->getAction() ){
						case HelperDataOperation::DATA_ACTION_ADD:
							if($db->insert($sql)){
								$result = true;
							}
							break;
						case HelperDataOperation::DATA_ACTION_UPDATE:
							if($db->update($sql)){
								$result = true;
							}
							break;

						case HelperDataOperation::DATA_ACTION_DELETE:
							if($db->delete($sql)){
								$result = true;
							}
							break;
					}
				}
			}
		}
		$this->_dirtyData = array();
		return $result;
	}
}
