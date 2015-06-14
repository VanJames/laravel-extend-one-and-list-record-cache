<?php
namespace App\Helpers;
class HelperNumber
{
	/**
	 * 判断是否整形（十进制）
	 * @param	mixed	$v	数字
	 * @return	boolean
	 */
	public static function isInteger( $v )
	{
		$i = intval( $v );
		if( "$i" == "$v" )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public static function isNumeric( $v )
	{
		return (string)(float)$v === (string)$v;
	}

	/**
	 * 获取百分比小数点两位 例 39.34%
	 * @param int $p 分子
	 * @param int $t 分母
	 * @return string
	 */
	public static function percent( $p , $t )
	{
        return sprintf( '%.2f%%' , $p * 100 / $t );
	}
}
?>