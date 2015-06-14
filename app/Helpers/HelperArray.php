<?php
namespace App\Helpers;
/**
 * 数组的助手类
 */
class HelperArray
{
	/**
	 * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
	 * keys to arrays rather than overwriting the value in the first array with the duplicate
	 * value in the second array, as array_merge does. I.e., with array_merge_recursive,
	 * this happens (documented behavior):
	 *
	 * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
	 *	 => array('key' => array('org value', 'new value'));
	 *
	 * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
	 * Matching keys' values in the second array overwrite those in the first array, as is the
	 * case with array_merge, i.e.:
	 *
	 * array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
	 *	 => array('key' => array('new value'));
	 *
	 * Parameters are passed by reference, though only for performance reasons. They're not
	 * altered by this method.
	 *
	 * @param array $array1
	 * @param array $array2
	 * @return array
	 * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
	 * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
	 */
	public static function arrayMergeRecursiveDistinct( array $array1 , array $array2 )
	{
		$merged = $array1;

		foreach( $array2 as $key => &$value )
		{
			if( is_array( $value ) && isset( $merged[$key] ) && is_array( $merged[$key] ) )
			{
				$merged[$key] = Helper_Array::arrayMergeRecursiveDistinct( $merged[$key] , $value );
			}
			else
			{
				$merged[$key] = $value;
			}
		}

		return $merged;
	}

	/**
	 * 实现Shift，但是不会影响键值
	 * @param    array   $arr         数组
	 * @param    boolean $isReturn    是否返回弹出的数据
	 * @param    boolean $isReturnKey false=>返回value，true=>返回key
	 * @return    mixed|null
	 */
	public static function arrayKeyShift( & $arr , $isReturn = true , $isReturnKey = false )
	{
		reset( $arr );
		$key = key( $arr );
		if( $key !== null )
		{
			$data = $arr[$key];
			unset( $arr[$key] );
		}
		if( $isReturn )
		{
			return $isReturnKey ? $key : $data;
		}
	}
	
	/**
	 * 实现Pop，但不会影响键值
	 * @param	array	$arr	数组
	 * @param	boolean	$isReturn	是否返回弹出的数据
	 * @return	mixed|null
	 */
	public static function arrayKeyPop( & $arr , $isReturn = true )
	{
		end( $arr );
		$key = key( $arr );
		if( $key !== null )
		{
			$data = $arr[$key];
			unset( $arr[$key] );
		}
		if( $isReturn )
		{
			return $data;
		}
	}
	
	/**
	 * 实现Unshift，但是不会影响键值
	 * @param	array	$arr	数组
	 * @param	string	$key	键
	 * @param	mixed	$value	值
	 */
	public static function arrayKeyUnshift( & $arr , $key , $value )
	{
		$arr = array( $key => $value ) + $arr;
	}
	
	/**
	 * 格式化输出
	 * 根据二维数组字段格式化输出
	 * 
	 * @param array $array
	 * @param string $index
	 * @author tom
	 */
	public static function format( $array , $index = 'id' )
	{
		$newArray = array();
		foreach ( $array as $item )
		{
			$newArray[ $item[ $index ] ] = $item;
		}
		return $newArray;
	}
	
	/**
	 * 获取不重复的二维数组字段列表
	 * 
	 * @param array $array
	 * @param string $index
	 * @author tom
	 */
	public static function getUniqueIds( $array , $index = 'id' )
	{
		$ids = array();
		foreach( $array as $item )
		{
			if( !in_array( $item[ $index ] , $ids))
			{
				$ids[] = $item[ $index ];
			}
		}
		
		return $ids;
		
	}
	
	/**
	 * 给数组添加前缀或者后缀
	 * 
	 * @param array $ids
	 * @param string $postfix	//后缀
	 * @param string $prefix	//前缀
	 */
	public static function addString( $ids , $postfix = '' , $prefix = '' )
	{
		$new = array();
		foreach( $ids as $id )
		{
			$new[] = "{$prefix}{$id}{$postfix}";
		}
		
		return $new;
		
	}
	
	/**
	 * 
	 * 根据指定的键对数组进行排序
	 * @param array $array
	 * @param string $keyname
	 * @param string $dir
	 */
	public static function sortByCol( $array , $keyname , $dir = SORT_DESC )
	{
		return self::sortByMultiCols( $array , array( $keyname => $dir ) );
	}

	/**
	* 将一个二维数组按照多个列进行排序，类似 SQL 语句中的 ORDER BY
	*
	* 用法：
	* @code php
	* $rows = Helper_Array::sortByMultiCols($rows, array(
	*		   'parent' => SORT_ASC, 
	*		   'name' => SORT_DESC,
	* ));
	* @endcode
	*
	* @param array $rowset 要排序的数组
	* @param array $args 排序的键
	*
	* @return array 排序后的数组
	*/
	public static function sortByMultiCols( $rowset, $args )
	{
		$sortArray = array();
		$sortRule = '';
		foreach ( $args as $sortField => $sortDir ) 
		{
			foreach ( $rowset as $offset => $row ) 
			{
				$sortArray[$sortField][$offset] = $row[$sortField];
			}
			
			$sortRule .= '$sortArray[\'' . $sortField . '\'], ' . $sortDir . ', ';
		}
		
		if ( empty($sortArray) || empty($sortRule ) )
			return $rowset;
			
		eval( 'array_multisort(' . $sortRule . '$rowset);' );
		return $rowset;
	}

	/**
	 * @param $arr
	 * @return array
	 */
	public static function iterationSort( $arr )
	{
		if( !is_array( $arr ) )
		{
			return $arr;
		}
		foreach( $arr as $key => $value )
		{
			if( is_array( $value ) )
			{
				$arr[$key] = self::iterationSort( $value );
			}
		}
		ksort( $arr );
		return $arr;
	}

	/**
	 * 按比例计算各部分值
	 * @param     $arr
	 * @param     $shareValue
	 * @param int $precision
	 * @return array
	 */
	public static function computeProportion( $arr , $shareValue , $precision = 0 )
	{
		$total = 0;
		//倒序排
		arsort( $arr );
		foreach( $arr as $memberValue )
		{
			$total += $memberValue;
		}
		$returnData = array();
		if( $total <= 0 || $shareValue <= 0 )
		{
			foreach( $arr as $key => $memberValue )
			{
				$returnData[$key] = 0;
			}
			return $returnData;
		}
		$newValue = 0;
		foreach( $arr as $key => $memberValue )
		{
			$returnData[$key] = round( ( $memberValue / $total ) * $shareValue , $precision );
			$newValue += $returnData[$key];
		}
		$returnData[$key] = $shareValue - $newValue + $returnData[$key];
		return $returnData;
	}
	
	/**
	 * 过滤数组中不需要的字段。如果某个字段的值是数组，请慎用此方法，特别在$recursive = true的时候
	 * @param array $array				要过滤的数组
	 * @param array $requiredFields		需要的字段
	 * @param boolean $recursive		是否递归过滤$array中的数组，仅适用于以数组为元素的列表
	 */
	public static function filterFields( &$array , &$requiredFields , $recursive = true )
	{
		foreach( $array as $field => &$value )
		{
			if( $recursive && is_array( $value ) )
			{
				self::filterFields( $value , $requiredFields , $recursive );
			}
			else
			{
				if( ! in_array( $field , $requiredFields, true ) )
				{
					unset( $array[ $field ] );
				}
			}
		}
	}
	
	/**
	 * 二维数组去掉重复值，不保留键
	 * 
	 * @param array $array2D
	 * @return array
	 */
	public static function arrayUnique2D( $array2D )
	{
		if( empty( $array2D ) )
		{
			return array();
		}
		
		//保存键
		$keys = array_keys( $array2D[0] );
		
		$result = array();
		foreach( $array2D as $v )
		{
			$v = join( ',' , $v );			  //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
			$temp[] = $v;
		}

		$temp = array_unique( $temp );		  //去掉重复的字符串,也就是重复的一维数组
		foreach( $temp as $k => $v )
		{
			$data = explode( ',' , $v );	//再将拆开的数组重新组装
			for( $i = 0; $i < count( $keys ); $i++ )
			{
				$result[$k][ $keys[$i] ] = $data[$i];
			}
		}
		
		return $result;
	}
	
	/**
	 * 插入数据到数组
	 * @param	array	$array
	 * @param	int	$position
	 * @param	array	$array2
	 */
	public static function arrayAssociativeInsert( & $array , $array2 , $position = 0 )
	{
		if( $position == 0 )
		{
			$array = array_merge( $array2 , $array );
			return;
		}
		else if( $position >= count( $array ) )
		{
			$array = array_merge( $array , $array2 );
			return;
		}
	
		$preDatas = array_slice( $array , 0 , $position , true );
		$postDatas = array_slice( $array , $position , null , true );
		$array = array_merge( $preDatas , $array2 , $postDatas );
	}
	
	/**
	 * 打散数据并且保持键不变
	 * @param	array	$array	数组
	 */
	public static function arrayAssociativeShuffle( & $array )
	{
		$keys = array_keys( $array );
		shuffle( $keys );
		$array = self::arrayMergeRecursiveDistinct( array_flip( $keys ) , $array );
	}

	/**
	 * 返回数组hash结果
	 * @param $array 数组
	 * @param int $length 长度
	 * @return string
	 */
	public static function hash( $array , $length = 0 )
	{
		sort( $array );
		$hash = md5( implode( ',' , $array ) );
		if( $length > 0 )
		{
			return substr( $hash , 0 , $length );
		}
		return $hash;
	}

	/**
	 * 取出整数二进制中位为1的数值
	 * @param $intVal
	 * @return array
	 */
	public static function extractValues( $intVal )
	{
		$rtn = array();
		$step = 0;
		while( $intVal > 0)
		{
			if( $intVal % 2 == 1 )
			{
				$rtn[] = 1 << $step;
			}
			$step++;
			$intVal >>= 1;
		}
		return $rtn;
	}

	/**
	 * Sort a Multidimensional Array Like SQL With usort
	 * http://stackoverflow.com/questions/96759/how-do-i-sort-a-multidimensional-array-in-php/16788610#16788610
	 */
	public static function usortMultiOrder()
	{
		// Normalize criteria up front so that the comparer finds everything tidy
		$criteria = func_get_args();
		foreach( $criteria as $index => $criterion )
		{
			$criteria[$index] = is_array( $criterion )
				? array_pad( $criterion , 3 , null )
				: array(
					$criterion , SORT_ASC , null
				);
		}

		return function ( $first , $second ) use ( &$criteria )
		{
			foreach( $criteria as $criterion )
			{
				// How will we compare this round?
				list( $column , $sortOrder , $projection ) = $criterion;
				$sortOrder = $sortOrder === SORT_DESC ? -1 : 1;

				// If a projection was defined project the values now
				if( $projection )
				{
					$lhs = call_user_func( $projection , $first[$column] );
					$rhs = call_user_func( $projection , $second[$column] );
				}
				else
				{
					$lhs = $first[$column];
					$rhs = $second[$column];
				}

				// Do the actual comparison; do not return if equal
				if( $lhs < $rhs )
				{
					return -1 * $sortOrder;
				}
				else if( $lhs > $rhs )
				{
					return 1 * $sortOrder;
				}
			}

			return 0; // tiebreakers exhausted, so $first == $second
		};
	}
}