<?php
namespace App\Helpers;
/*
 * 字符串助手
 */
class HelperString
{
	/**
	 * 字符串长度不对
	 */
	const ERROR_CODE_STRING_LENGTH = 7301;

	/**
	 * 过滤非法字符串
	 *
	 * @param string $str`
	 * @return string
	 */
	public static function encode( $str )
	{
		return htmlspecialchars( $str );
	}
	
	/**
	 * 工具方法。未来应该抽出来才好
	 * 参数不要用引用 防止重复urlencode
	 * @param array $params
	 */
	public static function buildBaseString( $params )
	{
		if (!$params) return '';

		// Urlencode both keys and values
		$keys = self::urlencodeRfc3986(array_keys($params));
		$values = self::urlencodeRfc3986(array_values($params));
		$params = array_combine($keys, $values);

		// Parameters are sorted by name, using lexicographical byte value ordering.
		// Ref: Spec: 9.1.1 (1)
		uksort($params, 'strcmp');

		$pairs = array();
		foreach ($params as $parameter => $value)
		{
			if (is_array($value))
			{
				// If two or more parameters share the same name, they are sorted by their value
				// Ref: Spec: 9.1.1 (1)
				natsort($value);
				foreach ($value as $duplicate_value)
				{
					$pairs[] = $parameter . '=' . $duplicate_value;
				}
			}
			else
			
			{
				$pairs[] = $parameter . '=' . $value;
			}
		}
		// For each parameter, the name is separated from the corresponding value by an '=' character (ASCII code 61)
		// Each name-value pair is separated by an '&' character (ASCII code 38)
		return implode('&', $pairs);
	}
	
	/**
	 * 对字符窜进行urlencode
	 *
	 * @param string $input
	 */
	public static function urlencodeRfc3986($input)
	{
		if (is_array( $input ) )
		{
			return array_map( array( 'self', 'urlencodeRfc3986' ), $input);
		}
		elseif ( is_scalar( $input ) )
		{
			return str_replace(
				'+',
				' ',
				str_replace('%7E', '~', rawurlencode($input))
			);
		}
		else
		{
			return '';
		}
	}

	/**
	* 截取字符串
	 */
	public static function subStr($str, $length, $append = TRUE, $htmlspecialchars = TRUE)
	{
		$str = mb_strimwidth($str, 0, $length, $append === TRUE ? '...' : '', 'UTF-8');

		if ($htmlspecialchars === TRUE)
		{
			return htmlspecialchars($str, ENT_QUOTES);
		}
		else
		{
			return $str;
		}
	}
    
    /**
     * 密码加密函数
     * @param string    $password
     * @param string    $key
     * @return string
     */
    public static function passwordHash( $password , $key = null )
    {
        if( $key === null )
        {
            $key = substr( md5( uniqid( rand() , true ) ) , 0 , 9 );
        }
        else
        {
            $key = substr( $key , 0 , 9 );
        }
        
        return md5( $password . $key );
    }

	public static function mbCheckLength( $str , $length )
	{
		if( mb_strlen( $str , 'UTF-8' ) > $length )
		{
			return false;
		}
		return true;
	}
	
	/**
	 * 对于MySQL5.1无法存储mb4的字符进行转换
	 * @param	string	$text	文字
	 * @return	string
	 */
	public static function safeToMysql5_1( $text )
	{
		mb_regex_encoding( 'utf-8' );
		return mb_ereg_replace_callback(
			"[^\d\w\s'=\"`\{\},\)\(]|[^\x{0000}-\x{ffff}]" ,
			function( $matches )
			{
				if( strlen( $matches[0] ) > 3 )
				{
					return '{'. bin2hex( $matches[0] ) .'}';
				}
				return $matches[0];
			} ,
			$text ,
			'ix'
		);
	}
	
	/**
	 * 对于MySQL5.1无法存储mb4的字符进行反转换
	 * @param	string	$text	文字
	 * @return	string
	 */
	public static function unsafeFromMysql5_1( $text )
	{
		mb_regex_encoding( 'utf-8' );
		return mb_ereg_replace_callback(
			'(\{[\da-f]{8}\})' ,
			function( $matches )
			{
				return hex2bin( trim( $matches[0] , '{}' ) );
			} ,
			$text ,
			'ix'
		);
	}

	/**
	 * 是否有MySQL5.1无法存储的字符
	 * @param	string	$text	文字
	 * @return	boolean
	 */
	public static function filterUnsafeWordByMysql5_1( & $text )
	{
		mb_regex_encoding( 'utf-8' );
		$isHas = false;
		$text = mb_ereg_replace_callback(
			'[^\d\w\s\'="`\{\},\)\(]|[^\x{0000}-\x{ffff}]' ,
			function( $matches ) use ( & $isHas )
			{
				if( strlen( $matches[0] ) > 3 )
				{
					$isHas = true;
					return '';
				}
				return $matches[0];
			} ,
			$text ,
			'ix'
		);
		return $isHas;
	}

	/**
	 * 将字符串中的换行替换为空格
	 * @param $content
	 */
	public static function replaceCarriage( $content )
	{
		$carriage = array( "\r\n","\n","\r" );
		return str_replace( $carriage , ' ' , $content );
	}

	/**
	 * @param $str
	 * @param $length
	 * @param bool $isThrowErr
	 * @param $errMessage
	 * @return bool
	 * @throws AppException
	 */
	public static function checkLength( $str , $length , $isThrowErr = false , $errMessage )
	{
		if( self::mbCheckLength( $str , $length ) )
		{
			return true;
		}
		if( $isThrowErr )
		{
			throw new AppException( self::ERROR_CODE_STRING_LENGTH , $errMessage );
		}
		return false;
	}


	/**
	 * 将字符串按照拼音规则拆解,如zhongguo会拆为zhong guo
	 * @param $str
	 * @return array
	 */
	public static function pinYinSplit( $str )
	{
		if( empty( $str ) )
		{
			return array();
		}
		$pinYinArr = array();
		preg_match_all(
			'/(a[io]?|ou?|e[inr]?|ang?|ng|[bmp](a[io]?|[aei]ng?|ei|ie?|ia[no]|o|u)|pou|me|m[io]u|[fw](a|[ae]ng?|ei|o|u)|fou|wai|[dt](a[io]?|an|e|[aeio]ng|ie?|ia[no]|ou|u[ino]?|uan)|dei|diu|[nl](a[io]?|ei?|[eio]ng|i[eu]?|i?ang?|iao|in|ou|u[eo]?|ve?|uan)|nen|lia|lun|[ghk](a[io]?|[ae]ng?|e|ong|ou|u[aino]?|uai|uang?)|[gh]ei|[jqx](i(ao?|ang?|e|ng?|ong|u)?|u[en]?|uan)|([csz]h?|r)([ae]ng?|ao|e|i|ou|u[ino]?|uan)|[csz](ai?|ong)|[csz]h(ai?|uai|uang)|zei|[sz]hua|([cz]h|r)ong|y(ao?|[ai]ng?|e|i|ong|ou|u[en]?|uan))/' ,
			$str ,
			$matches
		);
		if( !empty( $matches[0] ) )
		{
			foreach( $matches[0] as $pinYin )
			{
				if( strlen( $pinYin ) > 1 )
				{
					$pinYinArr[] = $pinYin;
				}
			}
		}
		return $pinYinArr;
	}

}