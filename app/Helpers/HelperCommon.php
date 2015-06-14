<?php
namespace App\Helpers;
use App\Exceptions\AppException;

class HelperCommon
{
	/**
	 * 配置（只有单元测试的PHPUnit_Model_Common类可以直接访问）
	 * @var	array｀
	 */
	public static $configs = array();

    public static function prepareGPCData( & $var )
    {
		if( is_array( $var ) )
		{
			while( ( list( $key , $val ) = each( $var ) ) != false )
			{
				$var[$key] = self::prepareGPCData( $val );
			}
		}
		else
		{
			$var = HelperString::encode( $var );
		}

		return $var;
    }

	/**
	 * 获取Cache实例
	 * @param	string $param	Cache服务器名称
	 * @throws AppException
	 * @return	HelperICache
	 */
	public static function & getCache( $param = 'data_memcache' )
	{
		static $cache = array();
		if( empty( $cache[$param] ) )
		{
			if( !config('cache.stores.'.$param ) ){
				throw new AppException(AppException::ERROR_CODE_CACHE_NOT_EXIST,$param);
			}

			$cache[$param] = \Cache::store($param);
		}
		return $cache[$param];
	}

	/**
	 * 计算最小的不重复值
	 * @param	array $ids			数字
	 * @param	int $min			最小值
	 * @return	int
	 */
	public static function computeMinUnique( $ids , $min = 1 )
	{
		array_multisort( $ids , SORT_ASC );
		foreach ( $ids as $item )
		{
			if( $min == $item )
			{
				$min++;
			}
		}
		return $min;
	}
    
	/**
	 * 判断大小
	 * @param	string	$key	键名
	 * @param	array	$a	关联数组1	
	 * @param	array	$b	关联数组2
	 * @return	int
	 */
	public static function compare( $key , $a , $b )
	{
		return self::compareItem( $a[$key] , $b[$key] );
	}

	/**
	 * 判断大小
	 * @param	int|float	$a	关联数组1
	 * @param	int|float	$b	关联数组2
	 * @return	int
	 */
	public static function compareItem( $a , $b )
	{
		if( $a == $b )
		{
			return 0;
		}

		return $a > $b ? 1 : -1;
	}
}