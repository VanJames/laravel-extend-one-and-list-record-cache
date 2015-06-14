<?php namespace App\Exceptions;

use Exception;

class AppException extends Exception {

	private static $_hasException = false;

	/**
	 * 类型错误
	 */
	const ERROR_CODE_TYPE = 1000;

	/**
	 * 缓存服务器不存在
	 */
	const ERROR_CODE_CACHE_NOT_EXIST = 1001;

	/**
	 * 数据正在更新，请稍后 锁
	 */
	const ERROR_CODE_CACHE_LOCK = 1002;

	/**
	 * @var array
	 */
	private static $_errorConfig = [
		self::ERROR_CODE_TYPE => '{msg}参数类型错误',
		self::ERROR_CODE_CACHE_NOT_EXIST => '{msg}缓存服务器不存在',
		self::ERROR_CODE_CACHE_LOCK => '{msg}数据正在更新，请稍后',
	];

	/**
	 * @param int $code
	 * @param string $massage
	 */
	public function __construct($code=0,$massage=''){
		self::$_hasException = true;
		if( isset( self::$_errorConfig[$code] ) ){
			$massage = strtr(
				self::$_errorConfig[$code],
				[
					'{msg}' => $massage
				]
			);
		}
		parent::__construct($massage,$code);
	}

	/**
	 * 是否有异常
	 */
	public static function hasException(){
		return self::$_hasException;
	}

}
