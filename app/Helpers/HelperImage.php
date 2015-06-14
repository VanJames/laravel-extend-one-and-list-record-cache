<?php
namespace App\Helpers;
/**
 * 图片帮助类
 */
class HelperImage
{
	/**
	 * 图片上传失败
	 */
	const ERROR_CODE_IMAGE_UPLOAD_ERR = 5003;

	/**
	 * @var null|string
	 */
	private $binaryContent = null;

	/**
	 * @var int
	 */
	private $width = 0;

	/**
	 * @var int
	 */
	private $height = 0;

	/**
	 * @var bool
	 */
	private $isWriteWaterMark = false;

	/**
	 * @var bool
	 */
	private $isCompress = false;

	/**
	 * @var string
	 */
	private $forceType = 'jpg';
	
	/**
	 * 裁剪模式
	 * @var	int
	 */
	private $_cutType = 3;

	/**
	 * @var string
	 */
	private $appendImage = '';


	/**
	 * @var string
	 */
	private $imageBorder = 0;

	/**
	 * @return array(
	 * 	path:string
	 * 	width:int
	 * 	height:int
	 * )
	 */
	public function upload()
	{
		//图片类型错误
		$imgType = Helper_Thumbnail::checkImgType( $this->getBinaryContent() );
		if( !$imgType )
		{
			return array(
				'path' => '',
				'width' => 0,
				'height' => 0,
			);
		}

		if( !file_exists( Common::getConfig( 'images' ) ) )
		{
			mkdir( Common::getConfig( 'images' ) , 0777 , true );
		}

		if( $this->_isNeedCompress( $imgType ) )
		{
			return $this->uploadCompress();
		}
		return $this->uploadKeepOriginal();
	}

	/**
	 * @return array(
	 * 	path:string
	 * 	width:int
	 * 	height:int
	 * )
	 * @throws AppException
	 */
	private function uploadKeepOriginal()
	{
		//获取文件格式
		$imgType = Helper_Thumbnail::checkImgType( $this->binaryContent );
		$combination = $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'] . time() . mt_rand();
		$picName = md5( $combination ) . '.' . $imgType;

		//图片完整路径
		$dstImg = Common::getConfig( 'images' ) . $picName;

		file_put_contents( $dstImg , $this->binaryContent );
		$imageInfo = getimagesize( $dstImg );
		if( empty( $imageInfo ) )
		{
			$imageSrc = ImageCreateFromString( $this->binaryContent );
			$imageInfo = array(
				imagesx( $imageSrc ) ,
				imagesy( $imageSrc ) ,
			);
			unset( $imageSrc );
		}
		if( Helper_Fastdfs::isUse() )
		{
			if( ( $name = Helper_Fastdfs::fastdfsStorageUploadAppenderByFilename1( $dstImg ) ) == false )
			{
				throw new AppException( self::ERROR_CODE_IMAGE_UPLOAD_ERR );
			}
			unlink( $dstImg );
		}
		else
		{
			$dstImgInfo = explode( '/' , $dstImg );
			$name = array_pop( $dstImgInfo );
		}
		return array(
			'path' => $name,
			'width' => $imageInfo[0],
			'height' => $imageInfo[1],
		);
	}

	/**
	 * @return array(
	 * 	path:string
	 * 	width:int
	 * 	height:int
	 * )
	 */
	private function uploadCompress()
	{
		//图片助手类
		$imgHelper = Helper_Thumbnail::getInstance();
		//生成图片并保存
		$imgHelper->setSrcImg( $this->binaryContent , Helper_Thumbnail::checkImgType( $this->binaryContent ) );
		if( $this->width > 0 && $this->height > 0 )
		{
			$imgHelper->setCutType( $this->_cutType );
		}
		$imgHelper->setDstImg();
		
		$width = $this->width ? : $imgHelper->src_w;
		$height = $this->height ? : $imgHelper->src_h;

		//压缩大小
		$imageMaxWidthOrHeight = Common::getConfig( 'imageMaxWidthOrHeight' );
		if( $this->isCompress && $this->width == 0
			&& ( $imgHelper->src_w > $imageMaxWidthOrHeight ) )
		{
			$width = $imageMaxWidthOrHeight;
			$height = round( $imageMaxWidthOrHeight / $imgHelper->src_w * $imgHelper->src_h );
		}

		if( $this->appendImage && $this->width > 0 )
		{
			$imgHelper->setCutType( 4 );
			$imgHelper->setDstImgBorder( 5 , '#FFFFFF' );
			$imgHelper->setAppendImage( $this->appendImage );
		}

		if( $this->isWriteWaterMark && Common::getConfig( 'imageMaskPath' ) )
		{
			$imgHelper->setMaskImg( Common::getConfig( 'imageMaskPath' ) );
		}
		
		$pic = $imgHelper->createImg( $width , $height );

		//释放
		$imgHelper->destroyImg();

		return array(
			'path' => $pic ,
			'width' => $width ,
			'height' => $height ,
		);
	}

	/**
	 * 获取单例
	 * @return Helper_Image
	 * @throws AppException
	 */
	public static function getInstance()
	{
		return new self();
	}

	/**
	 * @param $deviceType
	 * @return string
	 */
	public static function getDeviceTypeIcon( $deviceType )
	{
		$mediaURL = Common::getConfig( 'cdnBaseURL' ) . Common::getConfig( 'mediaPath' );
		switch( $deviceType )
		{
			case User_Info_Data::DEVICE_TYPE_ANDROID:
				return $mediaURL . '/images/android.png';
			case User_Info_Data::DEVICE_TYPE_IPAD:
				return $mediaURL . '/images/ipad.png';
			case User_Info_Data::DEVICE_TYPE_IPAD_IPHONE:
				return $mediaURL . '/images/ipadIphone.png';
			case User_Info_Data::DEVICE_TYPE_IOS:
			default:
				return $mediaURL . '/images/apple.png';
		}
	}

	/**
	 * @param $binaryContent
	 * @return Helper_Image
	 */
	public function setBinaryContent( $binaryContent )
	{
		$this->binaryContent = $binaryContent;
		return $this;
	}

	/**
	 * @param $width
	 * @return Helper_Image
	 */
	public function setWidth( $width )
	{
		$this->width = $width;
		return $this;
	}

	/**
	 * @param $height
	 * @return Helper_Image
	 */
	public function setHeight( $height )
	{
		$this->height = $height;
		return $this;
	}

	/**
	 * @param $isWriteWaterMark
	 * @return Helper_Image
	 */
	public function setIsWriteWaterMark( $isWriteWaterMark )
	{
		$this->isWriteWaterMark = $isWriteWaterMark;
		return $this;
	}

	/**
	 * @param $isCompress
	 * @return Helper_Image
	 */
	public function setIsCompress( $isCompress )
	{
		$this->isCompress = $isCompress;
		return $this;
	}

	/**
	 * @param string $forceType
	 * @return Helper_Image
	 */
	public function setForceType( $forceType )
	{
		$this->forceType = $forceType;
		return $this;
	}

	/**
	 * 设置裁剪模式
	 * @param	string	$cutType	裁剪模式
	 * @return	Helper_Image
	 */
	public function setCutType( $cutType )
	{
		$this->_cutType = $cutType;
		return $this;
	}

	/**
	 * 设置裁剪模式
	 * @param string $appendImage
	 * @return Helper_Image
	 */
	public function setAppendImage( $appendImage )
	{
		$this->appendImage = $appendImage;
		return $this;
	}

	/**
	 * 设置图片边框
	 * @param string $imageBorder
	 * @return Helper_Image
	 */
	public function setImageBorder( $imageBorder )
	{
		$this->imageBorder = $imageBorder;
		return $this;
	}

	/**
	 * @return null|string
	 */
	public function getBinaryContent()
	{
		return $this->binaryContent;
	}

	/**
	 * @return int
	 */
	public function getWidth()
	{
		return $this->width;
	}

	/**
	 * @return int
	 */
	public function getHeight()
	{
		return $this->height;
	}

	/**
	 * @return bool
	 */
	public function getIsWriteWaterMark()
	{
		return $this->isWriteWaterMark;
	}

	/**
	 * @return bool
	 */
	public function getIsCompress()
	{
		return $this->isCompress;
	}

	/**
	 * @return string
	 */
	public function getForceType()
	{
		return $this->forceType;
	}

	/**
	 * @return string
	 */
	public function getAppendImage()
	{
		return $this->appendImage;
	}

	/**
	 * @return string
	 */
	public function getImageBorder()
	{
		return $this->imageBorder;
	}
	
	/**
	 * 判断是否需要对图片进行压缩
	 * @param	string	$currentImageType	当前图片类型
	 * @return	boolean
	 */
	private function _isNeedCompress( $currentImageType )
	{
		return $this->_isSetWidth()
			|| $this->getIsWriteWaterMark()
			|| $this->_isMeetCompressFileSizeCondition()
			|| $this->_isHaveAppendImage()
			|| ( $this->_isMeetChangeImageTypeCondition( $currentImageType ) );
	}
	
	/**
	 * 是否满足了需要更换图片格式的条件
	 * @param	string	$currentImageType	当前图片类型
	 * @return	boolean
	 */
	private function _isMeetChangeImageTypeCondition( $currentImageType )
	{
		return $this->getForceType() && $this->getForceType() != $currentImageType;
	}
	
	/**
	 * 是否有图片要追加到图片尾部
	 * @return	boolean
	 */
	private function _isHaveAppendImage()
	{
		return strlen( $this->appendImage ) > 0;
	}
	
	/**
	 * 是否满足了压缩图片大小边界的条件
	 * @return	boolean
	 */
	private function _isMeetCompressFileSizeCondition()
	{
		return $this->getIsCompress() && strlen( $this->getBinaryContent() ) > Common::getConfig( 'imageMaxLength' );
	}
	
	/**
	 * 是否设置过生成的图片宽度
	 * @return	boolean
	 */
	private function _isSetWidth()
	{
		return $this->getWidth() > 0;
	}
}
