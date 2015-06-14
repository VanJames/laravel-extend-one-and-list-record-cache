<?php
namespace App\Model;

use App\DataModel\BaseDatabase;

/**
 * @author	fanxu(746439274@qq.com)
 */
class ModelContainer
{
	/**
	 * 数据对象集
	 * @var	BaseDatabase[]
	 */
	protected static $objects = array();
	
	/**
	 * 注册需要保存的数据对象
	 * @param	BaseDatabase	$dataObject	数据对象
	 * @return BaseDatabase
	 */
	public static function register( BaseDatabase $dataObject )
	{
		if( !isset( self::$objects[$dataObject->getCacheKey()] ) )
		{
			self::$objects[$dataObject->getCacheKey()] = $dataObject;
			return self::$objects[$dataObject->getCacheKey()];
		}

		if( !self::$objects[$dataObject->getCacheKey()]->getIsLocked() && $dataObject->getIsLocked() )
		{
			self::$objects[$dataObject->getCacheKey()] = $dataObject;
		}

		return self::$objects[$dataObject->getCacheKey()];
	}

	/**
	 * 保存所有对象
	 */
	public static function save()
	{
		self::_unsetUnlockObject();
		
		self::_saveDataObject();

		self::clear();
	}

	public static function clear()
	{
		self::$objects = array();
	}

	/**
	 * 获取需要保存的数据对象
	 * @param	string	$key	索引键
	 * @return	BaseDatabase
	 */
	public static function get( $key )
	{
		if( isset( self::$objects[$key] ) )
		{
			return self::$objects[$key];
		}
		return null;
	}

	/**
	 * 获取多个保存了的数据对象
	 * @param	string[]	$keys	索引键
	 * @return	BaseDatabase[]
	 */
	public static function mget( $keys )
	{
		$returnDatas = array();
		foreach( $keys as $key )
		{
			if( !isset( self::$objects[$key] ) )
			{
				continue;
			}
			$returnDatas[$key] = self::$objects[$key];
		}

		return $returnDatas;
	}
	
	/**
	 * 释放数据
	 * @param	string	$key	索引键
	 */
	public static function free( $key )
	{
		if( isset( self::$objects[$key] ) )
		{
			unset( self::$objects[$key] );
		}
	}

	/**
	 * 回滚数据
	 */
	public static function rollback()
	{
		foreach( self::$objects as $dataObject )
		{
			$dataObject->rollback();
		}

		self::clear();
	}

	/**
	 * 保存数据对象
	 */
	private static function _saveDataObject()
	{
		foreach( self::$objects as $key => $dataObject )
		{
			$dataObject->save();
			unset( self::$objects[$key] );
		}
	}

	/**
	 * 删除没有加锁的数据对象
	 */
	private static function _unsetUnlockObject()
	{
		foreach( self::$objects as $key => $dataObject )
		{
			if( $dataObject->getIsLocked() )
			{
				continue;
			}
			unset( self::$objects[$key] );
		}
	}
}

?>