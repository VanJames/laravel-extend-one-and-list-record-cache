<?php
namespace App\Helpers;
/**
 * 基本图片处理，用于完成图片缩入，水印添加
 * 当水印图超过目标图片尺寸时，水印图能自动适应目标图片而缩小
 * 水印图可以设置跟背景的合并度
 *
 * Copyright(c) 2005 by ustb99. All rights reserved
 *
 * To contact the author write to {@link mailto:ustb80@163.com}
 *
 * @author 偶然
 * @version $Id: thumb.class.php,v 1.9 2006/09/30 09:31:56 zengjian Exp $
 * @package system
 */
class HelperThumbnail
{
	/**
	 * 单例对象
	 */
	public static $singletonObjects = array();
	
	var $dst_img;//目标文件
	var $h_src; //图片资源句柄
	var $h_dst;//新图句柄
	var $h_mask;//水印句柄
	var $img_create_quality = 100;// 图片生成质量
	var $img_display_quality = 80;// 图片显示质量,默认为75
	var $img_scale = 0;// 图片缩放比例
	var $src_w = 0;// 原图宽度
	var $src_h = 0;// 原图高度
	var $dst_w = 0;// 新图总宽度
	var $dst_h = 0;// 新图总高度
	var $fill_w;// 填充图形宽
	var $fill_h;// 填充图形高
	var $copy_w;// 拷贝图形宽
	var $copy_h;// 拷贝图形高
	var $src_x = 0;// 原图绘制起始横坐标
	var $src_y = 0;// 原图绘制起始纵坐标
	var $start_x;// 新图绘制起始横坐标
	var $start_y;// 新图绘制起始纵坐标
	var $mask_word;// 水印文字
	var $mask_img;// 水印图片
	var $mask_pos_x = 0;// 水印横坐标
	var $mask_pos_y = 0;// 水印纵坐标
	var $mask_offset_x = 5;// 水印横向偏移
	var $mask_offset_y = 5;// 水印纵向偏移
	var $font_w;// 水印字体宽
	var $font_h;// 水印字体高
	var $mask_w;// 水印宽
	var $mask_h;// 水印高
	var $mask_font_color = "#ffffff";// 水印文字颜色
	var $mask_font = 2;// 水印字体
	var $font_size;// 尺寸
	var $mask_position = 0;// 水印位置
	var $mask_img_pct = 80;// 图片水印透明度
	var $mask_txt_pct = 50;// 文字水印透明度
	var $img_border_size = 0;// 图片边框尺寸
	var $img_border_color;// 图片边框颜色
	var $_flip_x=0;// 水平翻转次数
	var $_flip_y=0;// 垂直翻转次数
	var $cut_type=0;// 剪切类型
	var $img_type;// 文件类型

	var $appendImage;
	var $appendImageSrc;
	var $appendImageWidth = 0;
	var $appendImageHeight = 0;

	// 文件类型定义,并指出了输出图片的函数
	var $all_type = array(
		"jpg" => array(
			"output" => "imagejpeg" ,
			"img_display_quality" => 100
		) ,
		"gif" => array(
			"output" => "imagegif" ,
			"img_display_quality" => 100
		) ,
		"png" => array(
			"output" => "imagepng" ,
			"img_display_quality" => 9 //（PNG质量为0-9）
		) ,
		"bmp" => array(
			"output" => "imagewbmp" ,
			"img_display_quality" => 100 ,
		) ,
	);
	
	/**
	 * 获取单例
	 * @return Helper_Thumbnail
	 * @throws AppException
	 */
	public static function getInstance()
	{
		return new self();
	}

	/**
	 * 构造器
	 */
	protected function __construct()
	{
		$this->mask_font_color = '#ffffff';
		$this->font = 2;
		$this->font_size = 12;
	}
 
	/**
	 * 取得图片的宽
	 * 
	 * @param string $src
	 * @return int
	 */
	public function getImgWidth( $src )
	{
		return imagesx( $src );
	}
 
	/**
	 * 取得图片的高
	 * 
	 * @param string	$src		图片地址
	 * @return int
	 */
	public function getImgHeight( $src )
	{
		return imagesy( $src );
	}
 
	/**
	 * 设置图片生成路径
	 *
	 * @param string	$src		图片源，二进制流
	 * @param int	   $img_type   图片类型
	 * @return	Helper_Thumbnail
	 */
	public function setSrcImg( $src , $img_type )
	{
		if( empty( $img_type ) || empty( $src ) )
		{
			throw new AppException( 303 );
		}
		
		$this->img_type = $img_type;
		
		//图片类型检查
		$this->_checkValid( $this->img_type );
		
		$this->h_src = ImageCreateFromString( $src );
		if( $this->h_src === false && $this->img_type == 'bmp' )
		{
			$path = Common::getConfig( 'images' ) . str_pad( mt_rand( 0 , 100000000 ) , 9 , '0' , STR_PAD_LEFT ) .'.'. $this->img_type;
			file_put_contents( $path , $src );
			$this->h_src = $this->_imageCreateFromBMP( $path );
			unlink( $path );
		}
		$this->src_w = $this->getImgWidth( $this->h_src );
		$this->src_h = $this->getImgHeight( $this->h_src );
		return $this;
	}
 
	/**
	 * 设置图片生成路径
	 * 
	 * @return string	   返回生成的图片的名字或图片id，不包含图片名称后缀
	 */
	public function setDstImg()
	{
		//生成图片名称
		$combination = $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'] . time() . mt_rand();  
		$picName = md5( $combination ) . '.' . $this->img_type;
		
		//图片完整路径
		$dst_img = Common::getConfig( 'images' ) . $picName;
		
		$arr = explode( '/' , $dst_img );
		array_pop( $arr );
		$path = implode( '/' , $arr );
		$this->_mkdirs( $path );
		$this->dst_img = $dst_img;
		
		return $picName;
	}
 
	/**
	 * 设置图片的显示质量
	 *
	 * @param	string	  $n	质量
	 */
	public function setImgDisplayQuality( $n )
	{
		$this->img_display_quality = (int)$n;
	}
 
	/**
	 * 设置图片的生成质量
	 *
	 * @param	string	  $n	质量
	 */
	public function setImgCreateQuality( $n )
	{
		$this->img_create_quality = (int)$n;
	}
 
	/**
	 * 设置文字水印
	 *
	 * @param	string	 $word	水印文字
	 * @param	integer	$font	水印字体
	 * @param	string	 $color   水印字体颜色
	 */
	public function setMaskWord( $word )
	{
		$this->mask_word .= $word;
	}
 
	/**
	 * 设置字体颜色
	 *
	 * @param	string	 $color	字体颜色
	 */
	public function setMaskFontColor( $color = "#ffffff" )
	{
		$this->mask_font_color = $color;
	}
 
	/**
	 * 设置水印字体
	 *
	 * @param	string|integer	$font	字体
	 */
	public function setMaskFont( $font = 2 )
	{
		if( !is_numeric( $font ) && !file_exists( $font ) )
		{
			throw new Exception( '字体文件不存在' );
		}
		$this->font = $font;
	}
 
	/**
	 * 设置文字字体大小,仅对truetype字体有效
	 */
	public function setMaskFontSize( $size = "12" )
	{
		$this->font_size = $size;
	}
 
	/**
	 * 设置图片水印
	 *
	 * @param	string	$img	 水印图片源
	 */
	public function setMaskImg( $img )
	{
		$this->mask_img = $img;
	}
 
	/**
	 * 设置水印横向偏移
	 *
	 * @param	integer	 $x	横向偏移量
	 */
	public function setMaskOffsetX( $x )
	{
		$this->mask_offset_x = (int)$x;
	}
 
	/**
	 * 设置水印纵向偏移
	 *
	 * @param	integer	 $y	纵向偏移量
	 */
	public function setMaskOffsetY( $y )
	{
		$this->mask_offset_y = (int)$y;
	}
 
	/**
	 * 指定水印位置
	 *
	 * @param	integer	 $position	位置,1:左上,2:左下,3:右上,0/4:右下
	 */
	public function setMaskPosition( $position = 0 )
	{
		$this->mask_position = (int)$position;
	}
 
	/**
	 * 设置图片合并程度
	 *
	 * @param	integer	 $n	合并程度
	 */
	public function setMaskImgPct( $n )
	{
		$this->mask_img_pct = (int)$n;
	}
 
	/**
	 * 设置文字合并程度
	 *
	 * @param	integer	 $n	合并程度
	 */
	public function setMaskTxtPct( $n )
	{
		$this->mask_txt_pct = (int)$n;
	}
 
	/**
	 * 设置缩略图边框
	 *
	 * @param	(类型)	 (参数名)	(描述)
	 */
	public function setDstImgBorder( $size = 1 , $color = "#000000" )
	{
		$this->img_border_size  = (int)$size;
		$this->img_border_color = $color;
	}

	/**
	 * 设置拼接图
	 */
	public function setAppendImage( $appendImage )
	{
		$this->appendImage = $appendImage;
	}

	/**
	 * 水平翻转
	 */
	public function flipH()
	{
		$this->_flip_x++;
	}
 
	/**
	 * 垂直翻转
	 */
	public function flipV()
	{
		$this->_flip_y++;
	}
 
	/**
	 * 设置剪切类型
	 *
	 * @param	(类型)	 (参数名)	(描述)
	 */
	public function setCutType( $type )
	{
		$this->cut_type = (int)$type;
	}
 
	/**
	 * 设置图片剪切
	 *
	 * @param   integer	 $width	宽度
	 * @param   integer	 $height   高度
	 */
	public function setRectangleCut( $width , $height )
	{
		$this->fill_w = (int)$width;
		$this->fill_h = (int)$height;
	}
 
	/**
	 * 设置源图剪切起始坐标点
	 *
	 * @param int $x
	 * @param int $y
	 */
	public function setSrcCutPosition( $x , $y )
	{
		$this->src_x  = (int)$x;
		$this->src_y  = (int)$y;
	}
 
	/**
	 * 创建图片,主函数
	 * @param	integer	$a	 当缺少第二个参数时，此参数将用作百分比，否则作为宽度值
	 * @param	integer	$b	 图片缩放后的高度
	 */
	public function createImg( $a = null , $b = null )
	{
		switch( func_num_args() )
		{
			case 0:
				$this->_setNewImgSize( $this->src_w , $this->src_h );
				break;
				
			case 1:
				$r = (int)$a;
				if( $r < 1 )
				{
					throw new Exception( "图片缩放比例不得小于1" );
				}
				$this->img_scale = $r;
				$this->_setNewImgSize( $r );
				break;
 
			case 2:
				$w = (int)$a;
				$h = (int)$b;
				if( 0 == $w )
				{
					throw new Exception( "目标宽度不能为0" );
				}
				if( 0 == $h )
				{
					throw new Exception( "目标高度不能为0" );
				}
				$this->_setNewImgSize( $w , $h );
				break;
		}
 
		if($this->_flip_x%2 != 0)
		{
			$this->_flipH( $this->h_src );
		}
 
		if($this->_flip_y%2 !=0 )
		{
			$this->_flipV( $this->h_src );
		}
		
		if( empty( $this->dst_img ) )
		{
			$this->setDstImg();
		}

		$this->_createMask();
		return $this->_output();
	}
	
	/**
	 * 释放
	 */
	public function destroyImg()
	{
		if( imagedestroy( $this->h_src ) && imagedestroy( $this->h_dst ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * 删除图片文件
	 * @param	string	$filename	文件名
	 * @return	boolean
	 */
	public function removeImageFile( $filename )
	{
		if( Helper_Fastdfs::isUse() )
		{
			return Helper_Fastdfs::fastdfsStorageDeleteFile1( $filename );
		}
		
		if( file_exists( Common::getConfig( 'images' ) . $filename ) )
		{
			unlink( Common::getConfig( 'images' ) . $filename );
			return true;
		}
		return false;
	}
	
	/**
	 * 上传图片
	 * @param string $content	图片内容
	 * @param int $width		图片宽度
	 * @param int $height		图片高度
	 */
	public static function uploadImg( $content , $width = 0 , $height = 0 )
	{
		//图片助手类
		$imgHelper = Helper_Thumbnail::getInstance();
	
		//获取文件格式
		$imgType =Helper_Thumbnail::checkImgType( $content );
		
		//文件类型错误
		if( !$imgType )
		{
			//throw new AppException( 2004 );
			return;
		}
	
		//生成70x70的头像
		$imgHelper->setSrcImg( $content , $imgType );
		$imgHelper->setDstImg();
	
		$width = empty( $width ) ? $imgHelper->src_w : $width;
		$height = empty( $height ) ? $imgHelper->src_h : $height;

		$picName = $imgHelper->createImg( $width , $height );
	
		//释放
		$imgHelper->destroyImg();
	
		return $picName;
	}
 
	/**
	 * 生成水印,调用了生成水印文字和水印图片两个方法
	 */
	private function _createMask()
	{
		if( $this->mask_word )
		{
			// 获取字体信息
			$this->_setFontInfo();
 
			if( $this->_isFull() )
			{
				throw new Exception( "水印文字过大" );
			}
			else
			{
				$this->h_dst = imagecreatetruecolor( $this->dst_w , $this->dst_h );
				$white = ImageColorAllocate( $this->h_dst , 255 , 255 , 255 );
				imagefilledrectangle( $this->h_dst , 0 , 0 , $this->dst_w , $this->dst_h , $white );// 填充背景色
				$this->_drawBorder();
				imagecopyresampled( $this->h_dst , $this->h_src,
									$this->start_x , $this->start_y,
									$this->src_x , $this->src_y,
									$this->fill_w , $this->fill_h,
									$this->copy_w , $this->copy_h );
				$this->_createMaskWord( $this->h_dst );
			}
		}
 
		if( $this->mask_img )
		{
			//加载时，取得宽高
			$this->_loadMaskImg();
 
			if($this->_isFull())
			{
				// 将水印生成在原图上再拷
				$this->_createMaskImg( $this->h_src );
				$this->h_dst = imagecreatetruecolor( $this->dst_w , $this->dst_h );
				$white = ImageColorAllocate( $this->h_dst , 255 , 255 , 255 );
				
				//填充背景色
				imagefilledrectangle( $this->h_dst , 0 , 0 , $this->dst_w , $this->dst_h , $white );
				$this->_drawBorder();
				imagecopyresampled( $this->h_dst , $this->h_src,
									$this->start_x , $this->start_y,
									$this->src_x , $this->src_y,
									$this->fill_w , $this->start_y,
									$this->copy_w , $this->copy_h );
			}
			else
			{
				//创建新图并拷贝
				$this->h_dst = imagecreatetruecolor( $this->dst_w , $this->dst_h );
				$white = ImageColorAllocate( $this->h_dst , 255 , 255 , 255 );
				
				//填充背景色
				imagefilledrectangle( $this->h_dst , 0 , 0 , $this->dst_w , $this->dst_h , $white );
				$this->_drawBorder();
				imagecopyresampled( $this->h_dst , $this->h_src,
									$this->start_x , $this->start_y,
									$this->src_x , $this->src_y,
									$this->fill_w , $this->fill_h,
									$this->copy_w , $this->copy_h );
				$this->_createMaskImg( $this->h_dst );
			}
		}

		if( empty( $this->mask_word ) && empty( $this->mask_img ) )
		{
			if( !empty( $this->appendImage ) )
			{
				$this->_loadAppendImage();
				$this->dst_h += $this->appendImageHeight;
			}
			$this->h_dst = imagecreatetruecolor( $this->dst_w , $this->dst_h );
			$white = ImageColorAllocate( $this->h_dst , 255 , 255 , 255 );
			
			//填充背景色
			imagefilledrectangle( $this->h_dst , 0 , 0 , $this->dst_w , $this->dst_h , $white );
			$this->_drawBorder();
 
			imagecopyresampled( $this->h_dst , $this->h_src ,
						$this->start_x , $this->start_y ,
						$this->src_x , $this->src_y ,
						$this->fill_w , $this->fill_h ,
						$this->copy_w , $this->copy_h );
			if( !empty( $this->appendImage ) )
			{
				imagecopy( $this->h_dst , $this->appendImageSrc ,
					0 , $this->start_y + $this->fill_h ,
					0 , 0 ,
					$this->appendImageWidth , $this->appendImageHeight );
			}
		}
	}
 
	/**
	 * 画边框
	 */
	private function _drawBorder()
	{
		if( !empty($this->img_border_size ) )
		{
			$c = $this->_parseColor( $this->img_border_color );
			$color = ImageColorAllocate( $this->h_src , $c[0] , $c[1] , $c[2] );
			imagefilledrectangle( $this->h_dst , 0 , 0 , $this->dst_w , $this->dst_h , $color );// 填充背景色
		}
	}
 
	/**
	 * 生成水印文字
	 * 
	 * @param string $src
	 */
	private function _createMaskWord( $src )
	{
		$this->_countMaskPos();
		$this->_checkMaskValid();
 
		$c = $this->_parseColor( $this->mask_font_color );
		$color = imagecolorallocatealpha( $src , $c[0] , $c[1] , $c[2] , $this->mask_txt_pct );
 
		if( is_numeric( $this->font ) )
		{
			imagestring($src,
						$this->font,
						$this->mask_pos_x, $this->mask_pos_y,
						$this->mask_word,
						$color);
		}
		else
		{
			imagettftext($src,
						$this->font_size, 0,
						$this->mask_pos_x, $this->mask_pos_y,
						$color,
						$this->font,
						$this->mask_word);
		}
	}
 
	/**
	 * 生成水印图
	 * 
	 * @param string $src
	 */
	private function _createMaskImg( $src )
	{
		$this->_countMaskPos();
		$this->_checkMaskValid();
		
		$this->_imagecopymergeAlpha( $src,
			$this->h_mask,
			$this->mask_pos_x ,$this->mask_pos_y,
			0 , 0,
			$this->mask_w, $this->mask_h ,
			$this->mask_img_pct
		);
 
		imagedestroy( $this->h_mask );
	}
 
	/**
	 * 加载水印图
	 */
	private function _loadMaskImg()
	{
		$mask_type = $this->_getImgType($this->mask_img);
		$this->_checkValid($mask_type);
 
		// file_get_contents函数要求php版本>4.3.0
		$src = '';
		if(function_exists("file_get_contents"))
		{
			$src = file_get_contents($this->mask_img);
		}
		else
		{
			$handle = fopen( $this->mask_img , 'r' );
			while( !feof( $handle ) )
			{
				$src .= fgets( $handle , 4096 );
			}
			fclose ( $handle );
		}
		
		if( empty( $this->mask_img ) )
		{
			throw new Exception( '水印图片为空' );
		}
		
		$this->h_mask = ImageCreateFromString( $src );
		imagealphablending( $this->h_mask , false );//取消默认的混色模式
		imagesavealpha( $this->h_mask , true );//设定保存完整的 alpha 通道信息
		//imagecolorallocatealpha( $this->h_mask , 255 , 255 , 255 , 100 );
		
		$this->mask_w = $this->getImgWidth( $this->h_mask );
		$this->mask_h = $this->getImgHeight( $this->h_mask );
	}

	/**
	 * 加载水印图
	 */
	private function _loadAppendImage()
	{
		$src = file_get_contents( $this->appendImage );

		if( empty( $src ) )
		{
			throw new Exception( '拼接图片为空' );
		}

		$this->appendImageSrc = ImageCreateFromString( $src );

		$this->appendImageWidth = $this->getImgWidth( $this->appendImageSrc );
		$this->appendImageHeight = $this->getImgHeight( $this->appendImageSrc );
	}

	/**
	 * 图片输出
	 */
	function _output()
	{
		$img_type  = $this->img_type;
		$func_name = $this->all_type[$img_type]['output'];
		$img_display_quality = $this->all_type[$img_type]['img_display_quality'];
		if( function_exists( $func_name ) )
		{
			if( $img_type != 'bmp' )
			{
				$func_name( $this->h_dst , $this->dst_img , $img_display_quality );
			}
			else
			{
				$this->dst_img = preg_replace( '|\.bmp$|' , '.jpg' , $this->dst_img );
				imagejpeg( $this->h_dst , $this->dst_img , $img_display_quality );
			}
			if( Helper_Fastdfs::isUse() )
			{
				if( ( $name = Helper_Fastdfs::fastdfsStorageUploadAppenderByFilename1( $this->dst_img ) ) == false )
				{
					throw new AppException( Helper_Image::ERROR_CODE_IMAGE_UPLOAD_ERR );
				}
				unlink( $this->dst_img );

				return $name;
			}
			else
			{
				$dstImgInfo = explode( '/' , $this->dst_img );
				return array_pop( $dstImgInfo );
			}
		}
		else
		{
			return false;
		}
	}
 
	/**
	 * 分析颜色
	 *
	 * @param	string	 $color	十六进制颜色
	 */
	private function _parseColor( $color )
	{
		$arr = array();
		for( $ii = 1; $ii < strlen( $color ); $ii++ )
		{
			$arr[] = hexdec( substr( $color ,$ii , 2 ) );
			$ii++;
		}
 
		return $arr;
	}
 
	/**
	 * 计算出位置坐标
	 */
	private function _countMaskPos()
	{
		if( $this->_isFull() )
		{
			switch( $this->mask_position )
			{
				case 1:
					// 左上
					$this->mask_pos_x = $this->mask_offset_x + $this->img_border_size;
					$this->mask_pos_y = $this->mask_offset_y + $this->img_border_size;
					break;
				case 2:
					// 左下
					$this->mask_pos_x = $this->mask_offset_x + $this->img_border_size;
					$this->mask_pos_y = $this->src_h - $this->mask_h - $this->mask_offset_y;
					break;
				case 3:
					// 右上
					$this->mask_pos_x = $this->src_w - $this->mask_w - $this->mask_offset_x;
					$this->mask_pos_y = $this->mask_offset_y + $this->img_border_size;
					break;
				case 4:
					// 右下
					$this->mask_pos_x = $this->src_w - $this->mask_w - $this->mask_offset_x;
					$this->mask_pos_y = $this->src_h - $this->mask_h - $this->mask_offset_y;
					break;
				default:
					// 默认将水印放到右下,偏移指定像素
					$this->mask_pos_x = $this->src_w - $this->mask_w - $this->mask_offset_x;
					$this->mask_pos_y = $this->src_h - $this->mask_h - $this->mask_offset_y;
					break;
			}
		}
		else
		{
			switch( $this->mask_position )
			{
				case 1:
					// 左上
					$this->mask_pos_x = $this->mask_offset_x + $this->img_border_size;
					$this->mask_pos_y = $this->mask_offset_y + $this->img_border_size;
					break;
				case 2:
					// 左下
					$this->mask_pos_x = $this->mask_offset_x + $this->img_border_size;
					$this->mask_pos_y = $this->dst_h - $this->mask_h - $this->mask_offset_y - $this->img_border_size;
					break;
				case 3:
					// 右上
					$this->mask_pos_x = $this->dst_w - $this->mask_w - $this->mask_offset_x - $this->img_border_size;
					$this->mask_pos_y = $this->mask_offset_y + $this->img_border_size;
					break;
				case 4:
					// 右下
					$this->mask_pos_x = $this->dst_w - $this->mask_w - $this->mask_offset_x - $this->img_border_size;
					$this->mask_pos_y = $this->dst_h - $this->mask_h - $this->mask_offset_y - $this->img_border_size;
					break;
				default:
					// 默认将水印放到右下,偏移指定像素
					$this->mask_pos_x = $this->dst_w - $this->mask_w - $this->mask_offset_x - $this->img_border_size;
					$this->mask_pos_y = $this->dst_h - $this->mask_h - $this->mask_offset_y - $this->img_border_size;
					break;
			}
		}
	}
 
	/**
	 * 设置字体信息
	 */
	private function _setFontInfo()
	{
		if( is_numeric( $this->font ) )
		{
			$this->font_w = imagefontwidth( $this->font );
			$this->font_h = imagefontheight( $this->font );
 
			// 计算水印字体所占宽高
			$word_length = strlen( $this->mask_word );
			$this->mask_w = $this->font_w * $word_length;
			$this->mask_h = $this->font_h;
		}
		else
		{
			$arr = imagettfbbox ( $this->font_size , 0 , $this->font , $this->mask_word );
			$this->mask_w = abs( $arr[0] - $arr[2] );
			$this->mask_h = abs( $arr[7] - $arr[1] );
		}
	}
 
	/**
	 * 设置新图尺寸
	 *
	 * @param	integer	 $img_w   目标宽度
	 * @param	integer	 $img_h   目标高度
	 */
	private function _setNewImgSize( $img_w , $img_h = null )
	{
		$num = func_num_args();
		if( 1 == $num )
		{
			$this->img_scale = $img_w;// 宽度作为比例
			$this->fill_w = round( $this->src_w * $this->img_scale / 100 ) - $this->img_border_size * 2;
			$this->fill_h = round( $this->src_h * $this->img_scale / 100 ) - $this->img_border_size * 2;
 
			// 源文件起始坐标
			$this->src_x  = 0;
			$this->src_y  = 0;
			$this->copy_w = $this->src_w;
			$this->copy_h = $this->src_h;
 
			// 目标尺寸
			$this->dst_w  = $this->fill_w + $this->img_border_size * 2;
			$this->dst_h  = $this->fill_h + $this->img_border_size * 2;
		}
 
		if( 2 == $num )
		{
			$fill_w   = (int)$img_w - $this->img_border_size * 2;
			$fill_h   = (int)$img_h - $this->img_border_size * 2;
			if( $fill_w < 0 || $fill_h < 0 )
			{
				throw new Exception( '图片边框过大，已超过了图片的宽度' );
			}
			$rate_w = $this->src_w / $fill_w;
			$rate_h = $this->src_h / $fill_h;
 
			switch($this->cut_type)
			{
				case 0:
					// 如果原图大于缩略图，产生缩小，否则不缩小
					if($rate_w < 1 && $rate_h < 1)
					{
						$this->fill_w = (int)$this->src_w;
						$this->fill_h = (int)$this->src_h;
					}
					else
					{
						if($rate_w >= $rate_h)
						{
							$this->fill_w = (int)$fill_w;
							$this->fill_h = round($this->src_h/$rate_w);
						}
						else
						{
							$this->fill_w = round($this->src_w/$rate_h);
							$this->fill_h = (int)$fill_h;
						}
					}
 
					$this->src_x  = 0;
					$this->src_y  = 0;
 
					$this->copy_w = $this->src_w;
					$this->copy_h = $this->src_h;
 
					// 目标尺寸
					$this->dst_w   = $this->fill_w + $this->img_border_size*2;
					$this->dst_h   = $this->fill_h + $this->img_border_size*2;
					break;
 
				// 自动裁切
				case 1:
					// 如果图片是缩小剪切才进行操作
					if($rate_w >= 1 && $rate_h >=1)
					{
						if($this->src_w > $this->src_h)
						{
							$src_x = round($this->src_w-$this->src_h)/2;
							$this->setSrcCutPosition($src_x, 0);
							$this->setRectangleCut($fill_h, $fill_h);
 
							$this->copy_w = $this->src_h;
							$this->copy_h = $this->src_h;
							
						}
						elseif($this->src_w < $this->src_h)
						{
							$src_y = round($this->src_h-$this->src_w)/2;
							$this->setSrcCutPosition(0, $src_y);
							$this->setRectangleCut($fill_w, $fill_h);
 
							$this->copy_w = $this->src_w;
							$this->copy_h = $this->src_w;
						}
						else
						{
							$this->setSrcCutPosition(0, 0);
							$this->copy_w = $this->src_w;
							$this->copy_h = $this->src_w;
							$this->setRectangleCut($fill_w, $fill_h);
						}
					}
					else
					{
						$this->setSrcCutPosition(0, 0);
						$this->setRectangleCut($this->src_w, $this->src_h);
 
						$this->copy_w = $this->src_w;
						$this->copy_h = $this->src_h;
					}
 
					// 目标尺寸
					$this->dst_w   = $this->fill_w + $this->img_border_size*2;
					$this->dst_h   = $this->fill_h + $this->img_border_size*2;
					
					break;
 
				// 手工裁切
				case 2:
					$this->copy_w = $this->fill_w;
					$this->copy_h = $this->fill_h;
 
					// 目标尺寸
					$this->dst_w   = $this->fill_w + $this->img_border_size*2;
					$this->dst_h   = $this->fill_h + $this->img_border_size*2;				
					
					break;
				case 3:
					// 如果原图大于缩略图，产生缩小，否则不缩小
					if($rate_w >= $rate_h)
					{
						$this->fill_w = (int)$fill_w;
						$this->fill_h = round($this->src_h/$rate_w);
						$this->start_x = $this->img_border_size;
						$this->start_y = $this->img_border_size + round( ( $this->fill_w - $this->fill_h ) / 2 );
					}
					else
					{
						$this->fill_w = round($this->src_w/$rate_h);
						$this->fill_h = (int)$fill_h;
						$this->start_x = $this->img_border_size + round( ( $this->fill_h - $this->fill_w ) / 2 );
						$this->start_y = $this->img_border_size;
					}

					$this->src_x  = 0;
					$this->src_y  = 0;

					$this->copy_w = $this->src_w;
					$this->copy_h = $this->src_h;

					// 目标尺寸
					$this->dst_w   = $fill_w + $this->img_border_size*2;
					$this->dst_h   = $fill_h + $this->img_border_size*2;
					return;

				case 4:
					if( $fill_w > $this->src_w )
					{
						$fill_h = $this->src_h;
						$this->fill_w = $this->src_w;
						$this->fill_h = $this->src_h;
						$this->start_x = $this->img_border_size + round( ( $fill_w - $this->src_w ) / 2 );
						$this->start_y = $this->img_border_size;
					}
					else
					{
						$fill_h = round( $fill_w / $this->src_w * $this->src_h );
						$this->fill_w = $fill_w;
						$this->fill_h = $fill_h;
						$this->start_x = $this->img_border_size;
						$this->start_y = $this->img_border_size;

					}

					$this->src_x  = 0;
					$this->src_y  = 0;

					$this->copy_w = $this->src_w;
					$this->copy_h = $this->src_h;

					// 目标尺寸
					$this->dst_w   = $fill_w + $this->img_border_size*2;
					$this->dst_h   = $fill_h + $this->img_border_size*2;
					return;
				default:
					break;
 
			}
		}
 
		// 目标文件起始坐标
		$this->start_x = $this->img_border_size;
		$this->start_y = $this->img_border_size;
	}
 
	/**
	 * 检查水印图是否大于生成后的图片宽高
	 */
	private function _isFull()
	{
		return (   $this->mask_w + $this->mask_offset_x > $this->fill_w
				|| $this->mask_h + $this->mask_offset_y > $this->fill_h)
				   ? true : false;
	}
 
	/**
	 * 检查水印图是否超过原图
	 */
	private function _checkMaskValid()
	{
		if(	$this->mask_w + $this->mask_offset_x > $this->src_w
			|| $this->mask_h + $this->mask_offset_y > $this->src_h)
		{
			throw new Exception( '水印图片尺寸大于原图，请缩小水印图' );
		}
	}
 
	/**
	 * 取得图片类型
	 *
	 * @param	string	 $file_path	文件路径
	 */
	private function _getImgType( $file_path )
	{
		$type_list = array(
			1 => 'gif' ,
			2 => 'jpg' ,
			3 => 'png' ,
			4 => 'swf' ,
			5 => 'psd' ,
			6 => 'bmp' ,
			15 => 'wbmp' );
		if( file_exists( $file_path ) )
		{
			$img_info = @getimagesize( $file_path );
			if( isset( $type_list[$img_info[2]] ) )
			{
				return $type_list[$img_info[2]];
			}
		}
		else
		{
			throw new Exception( '文件不存在,不能取得文件类型!' );
		}
	}
 
	/**
	 * 检查图片类型是否合法,调用了array_key_exists函数，此函数要求
	 * php版本大于4.1.0
	 *
	 * @param	string	 $img_type	文件类型
	 */
	private function _checkValid( $img_type )
	{
		if(!array_key_exists( $img_type , $this->all_type ) )
		{
			return false;
		}
	}
 
	/**
	 * 按指定路径生成目录
	 *
	 * @param	string	 $path	路径
	 */
	private function _mkdirs( $path )
	{
		$adir = explode( '/' , $path );
		$dirlist = '';
		$rootdir = array_shift( $adir );
		if( ( $rootdir != '.' || $rootdir != '..' ) && !file_exists( $rootdir ) )
		{
			@mkdir( $rootdir , 0777 , true );
		}
		foreach( $adir as $val )
		{
			if( $val != '.' && $val != '..' )
			{
				$dirlist .= "/" . $val;
				$dirpath = $rootdir . $dirlist;
				if( !file_exists( $dirpath ) )
				{
					@mkdir( $dirpath , 0777 , true );
				}
			}
		}
	}
 
	/**
	 * 垂直翻转
	 *
	 * @param	string	 $src	图片源
	 */
	private function _flipV( $src )
	{
		$src_x = $this->getImgWidth( $src );
		$src_y = $this->getImgHeight( $src );
 
		$new_im = imagecreatetruecolor( $src_x , $src_y );
		for( $y = 0; $y < $src_y; $y++ )
		{
			imagecopy( $new_im , $src , 0 , $src_y - $y - 1 , 0 , $y , $src_x , 1 );
		}
		$this->h_src = $new_im;
	}
 
	/**
	 * 水平翻转
	 *
	 * @param	string	 $src	图片源
	 */
	private function _flipH( $src )
	{
		$src_x = $this->getImgWidth( $src );
		$src_y = $this->getImgHeight( $src );
 
		$new_im = imagecreatetruecolor( $src_x , $src_y );
		for( $x = 0; $x < $src_x; $x++)
		{
			imagecopy( $new_im , $src , $src_x - $x - 1 , 0 , $x , 0 , 1 , $src_y );
		}
		$this->h_src = $new_im;
	}
	
	/**
	 * 判断上传的图片文件格式
	 * 
	 * @param	strint|string	$imageBinary	图片二进制流信息
	 * @return	string|boolean	图片格式（jpg，png，gif），否则返回false
	 */
	public static function checkImgType( $imageBinary )
	{
		if( $imageBinary{0} . $imageBinary{1} == "\x89\x50"  )
		{
			return 'png';
		}
		elseif( $imageBinary{0} . $imageBinary{1} == "\xff\xd8" )
		{
			return 'jpg';
		}
		elseif( $imageBinary{0} . $imageBinary{1} . $imageBinary{2} == "\x47\x49\x46" )
		{
			return 'gif';
		}
		else if( $imageBinary{0} . $imageBinary{1} == 'BM' )
		{
			return 'bmp';
		}
		
		return false;
	}
	
	private function _imageCopyMergeAlpha( $dst_im , $src_im , $dst_x , $dst_y , $src_x , $src_y , $src_w , $src_h , $pct )
	{
		// creating a cut resource 
		$cut = imagecreatetruecolor( $src_w , $src_h ); 

		// copying relevant section from background to the cut resource 
		imagecopy( $cut , $dst_im , 0 , 0 , $dst_x , $dst_y , $src_w , $src_h ); 
		
		// copying relevant section from watermark to the cut resource 
		imagecopy( $cut , $src_im , 0 , 0 , $src_x , $src_y , $src_w , $src_h ); 
		
		// insert cut resource to destination image 
		imagecopymerge( $dst_im , $cut , $dst_x , $dst_y , 0 , 0 , $src_w , $src_h , $pct ); 
	}
	
	/*********************************************/
	/* Fonction: ImageCreateFromBMP              */
	/* Author:   DHKold                          */
	/* Contact:  admin@dhkold.com                */
	/* Date:     The 15th of June 2005           */
	/* Version:  2.0B                            */
	/*********************************************/
	private function _imageCreateFromBMP( $filename )
	{
		// Ouverture du fichier en mode binaire
		if( !$f1 = fopen( $filename , "rb" ) )
			return FALSE;
			
			// 1 : Chargement des ent�tes FICHIER
		$FILE = unpack( "vfile_type/Vfile_size/Vreserved/Vbitmap_offset" , fread( $f1 , 14 ) );
		if( $FILE['file_type'] != 19778 )
			return FALSE;
			
			// 2 : Chargement des ent�tes BMP
		$BMP = unpack( 'Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel' . '/Vcompression/Vsize_bitmap/Vhoriz_resolution' . '/Vvert_resolution/Vcolors_used/Vcolors_important' , fread( $f1 , 40 ) );
		$BMP['colors'] = pow( 2 , $BMP['bits_per_pixel'] );
		if( $BMP['size_bitmap'] == 0 )
			$BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
		$BMP['bytes_per_pixel'] = $BMP['bits_per_pixel'] / 8;
		$BMP['bytes_per_pixel2'] = ceil( $BMP['bytes_per_pixel'] );
		$BMP['decal'] = ( $BMP['width'] * $BMP['bytes_per_pixel'] / 4 );
		$BMP['decal'] -= floor( $BMP['width'] * $BMP['bytes_per_pixel'] / 4 );
		$BMP['decal'] = 4 - ( 4 * $BMP['decal'] );
		if( $BMP['decal'] == 4 )
			$BMP['decal'] = 0;
			
			// 3 : Chargement des couleurs de la palette
		$PALETTE = array();
		if( $BMP['colors'] < 16777216 )
		{
			$PALETTE = unpack( 'V' . $BMP['colors'] , fread( $f1 , $BMP['colors'] * 4 ) );
		}
		
		// 4 : Cr�ation de l'image
		$IMG = fread( $f1 , $BMP['size_bitmap'] );
		$VIDE = chr( 0 );
		
		$res = imagecreatetruecolor( $BMP['width'] , $BMP['height'] );
		$P = 0;
		$Y = $BMP['height'] - 1;
		while( $Y >= 0 )
		{
			$X = 0;
			while( $X < $BMP['width'] )
			{
				if( $BMP['bits_per_pixel'] == 24 )
					$COLOR = unpack( "V" , substr( $IMG , $P , 3 ) . $VIDE );
				elseif( $BMP['bits_per_pixel'] == 16 )
				{
					$COLOR = unpack( "n" , substr( $IMG , $P , 2 ) );
					$COLOR[1] = $PALETTE[$COLOR[1] + 1];
				}
				elseif( $BMP['bits_per_pixel'] == 8 )
				{
					$COLOR = unpack( "n" , $VIDE . substr( $IMG , $P , 1 ) );
					$COLOR[1] = $PALETTE[$COLOR[1] + 1];
				}
				elseif( $BMP['bits_per_pixel'] == 4 )
				{
					$COLOR = unpack( "n" , $VIDE . substr( $IMG , floor( $P ) , 1 ) );
					if( ( $P * 2 ) % 2 == 0 )
						$COLOR[1] = ( $COLOR[1] >> 4 );
					else
						$COLOR[1] = ( $COLOR[1] & 0x0F );
					$COLOR[1] = $PALETTE[$COLOR[1] + 1];
				}
				elseif( $BMP['bits_per_pixel'] == 1 )
				{
					$COLOR = unpack( "n" , $VIDE . substr( $IMG , floor( $P ) , 1 ) );
					if( ( $P * 8 ) % 8 == 0 )
						$COLOR[1] = $COLOR[1] >> 7;
					elseif( ( $P * 8 ) % 8 == 1 )
						$COLOR[1] = ( $COLOR[1] & 0x40 ) >> 6;
					elseif( ( $P * 8 ) % 8 == 2 )
						$COLOR[1] = ( $COLOR[1] & 0x20 ) >> 5;
					elseif( ( $P * 8 ) % 8 == 3 )
						$COLOR[1] = ( $COLOR[1] & 0x10 ) >> 4;
					elseif( ( $P * 8 ) % 8 == 4 )
						$COLOR[1] = ( $COLOR[1] & 0x8 ) >> 3;
					elseif( ( $P * 8 ) % 8 == 5 )
						$COLOR[1] = ( $COLOR[1] & 0x4 ) >> 2;
					elseif( ( $P * 8 ) % 8 == 6 )
						$COLOR[1] = ( $COLOR[1] & 0x2 ) >> 1;
					elseif( ( $P * 8 ) % 8 == 7 )
						$COLOR[1] = ( $COLOR[1] & 0x1 );
					$COLOR[1] = $PALETTE[$COLOR[1] + 1];
				}
				else
					return FALSE;
				imagesetpixel( $res , $X , $Y , $COLOR[1] );
				$X++;
				$P += $BMP['bytes_per_pixel'];
			}
			$Y--;
			$P += $BMP['decal'];
		}
		
		// Fermeture du fichier
		fclose( $f1 );
		
		return $res;
	}
}